<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\{Pemesanan, Kamar, Fasilitas};
use App\Models\Admins\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PemesananController extends Controller
{
    public function index()
    {

        view()->share('title', 'Data Pemesanan kamar dan fasilitas');

        $items = Pemesanan::with(['user', 'kamar', 'fasilitas'])
            ->latest()
            ->get();
            
        $penyewas = User::with('biodata')
            ->where('role', 'User')
            ->whereDoesntHave('pemesanan', function (Builder $q) {
                $q->whereIn('status', ['Menunggu Pembayaran', 'Aktif']);
            })
            ->get();
        $fasilitas = Fasilitas::where('stok', '>', 0)->get();
        
        return view('pages.admins.pemesanan.data-pemesanan', compact('items', 'penyewas', 'fasilitas'));
    }

    public function getKamars(Request $request)
    {
        $user = User::with('biodata')->find($request->user_id);

        if (!$user || !$user->biodata || !$user->biodata->jenis_kelamin) {
            return response()->json([]);
        }

        // Normalisasi gender
        $gender = strtolower(trim($user->biodata->jenis_kelamin));
        $gender = str_replace(' ', '-', $gender);

        $kamars = Kamar::whereRaw('LOWER(TRIM(status)) = ?', ['kosong'])
            ->where(function ($q) use ($gender) {
                $q->whereRaw(
                    'REPLACE(LOWER(TRIM(khusus)), " ", "-") = ?',
                    [$gender]
                )->orWhereRaw('LOWER(TRIM(khusus)) = ?', ['keluarga']);
            })
            ->get([
                'id',
                'kode',
                'harga',
                'foto',
                'status',
                'khusus'
            ]);

        return response()->json($kamars);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'kamar_id'      => 'required|exists:kamars,id',
            'tgl_masuk'     => 'required|date',
            'durasi'        => 'required|numeric|min:1',
            'jenis_sewa'    => 'required|in:Harian,Bulanan',
            'fasilitas_ids' => 'nullable|array'
        ]);

        try {

            DB::beginTransaction();

            // ===============================
            // AMBIL DATA & NORMALISASI
            // ===============================
                $kamar      = Kamar::lockForUpdate()->findOrFail($request->kamar_id);
                $tglMasuk   = Carbon::parse($request->tgl_masuk);
                $durasi     = (int) $request->durasi;
                $jenisSewa  = $request->jenis_sewa;
                $last       = Pemesanan::whereBetween('kode_pemesanan', [1000000, 9999999])->withTrashed()->orderBy('kode_pemesanan', 'desc')->first();
                $newkode    = $last ? ($last->kode_pemesanan + 1) : 1000000;

            // ===============================
            // HITUNG TANGGAL KELUAR
            // ===============================
                if ($jenisSewa === 'Bulanan') {
                    $tglKeluar = $tglMasuk->copy()->addMonths($durasi);
                } else {
                    $tglKeluar = $tglMasuk->copy()->addDays($durasi);
                }

            // ===============================
            // CEK & PROSES FASILITAS
            // ===============================
                $totalHargaFasilitas = 0;
                $fasilitasData = [];

                if (!empty($request->fasilitas_ids)) {
                    $fasilitasItems = Fasilitas::whereIn('id', $request->fasilitas_ids)
                        ->lockForUpdate()
                        ->get();

                    foreach ($fasilitasItems as $f) {
                        if ($f->stok < 1) {
                            throw new \Exception("Stok {$f->nama_fasilitas} habis");
                        }

                        $fasilitasData[] = [
                            'id'    => $f->id,
                            'harga' => $f->harga
                        ];

                        $totalHargaFasilitas += $f->harga;
                        $f->decrement('stok');
                    }
                }

            // ===============================
            // HITUNG TOTAL HARGA
            // ===============================
            // Harian = harga / 30 (sesuai DB bulanan)
                $hargaKamarDasar = ($jenisSewa === 'Harian')
                    ? ($kamar->harga / 30)
                    : $kamar->harga;

                $hargaFasilitasDasar = ($jenisSewa === 'Harian')
                    ? ($totalHargaFasilitas / 30)
                    : $totalHargaFasilitas;

                $totalTransaksi = ($hargaKamarDasar + $hargaFasilitasDasar) * $durasi;

            // ===============================
            // SIMPAN PEMESANAN
            // ===============================
            $pemesanan = Pemesanan::create([
                'user_id'           => $request->user_id,
                'kamar_id'          => $kamar->id,
                'tgl_masuk'         => $tglMasuk,
                'tgl_keluar'        => $tglKeluar,
                'jenis_sewa'        => $jenisSewa,
                'total_harga'       => ceil($totalTransaksi),
                'status'            => 'Menunggu Pembayaran',
                'kode_pemesanan'    => $newkode
            ]);

            // ===============================
            // SIMPAN RELASI FASILITAS
            // ===============================
            foreach ($fasilitasData as $fd) {
                $pemesanan->fasilitas()->attach($fd['id'], [
                    'harga_snap' => $fd['harga']
                ]);
            }

            // ===============================
            // UPDATE STATUS KAMAR
            // ===============================
            $kamar->update(['status' => 'Dipesan']);

            DB::commit();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Berhasil',
                'text'  => 'Pemesanan berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal',
                'text'  => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $pemesanan = Pemesanan::with('fasilitas')->findOrFail($id);

                // Balikin Stok
                foreach ($pemesanan->fasilitas as $f) {
                    $f->increment('stok');
                }

                // PERBAIKAN: Ubah status jadi 'Kosong' (bukan 'Tersedia')
                $pemesanan->kamar->update(['status' => 'Kosong']);

                $pemesanan->delete();
            });

            return back()->with('alert', ['icon' => 'success', 'title' => 'Dihapus', 'text' => 'Data dipindahkan ke sampah.']);

        } catch (\Exception $e) {
            return back()->with('alert', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }
}