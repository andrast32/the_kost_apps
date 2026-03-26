<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Fasilitas;
use App\Models\Admins\Kamar;
use App\Models\Admins\Pemesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemesananController extends Controller
{

    public function index()
    {

        view()->share('title', 'Data Pemesanan kamar dan fasilitas');

        $data = [
            'item'     => Pemesanan::with(['user', 'kamar', 'fasilitas'])
                        ->orderBy('status', 'ASC')
                        ->orderBy('kode_pemesanan', 'ASC')
                        ->get(),

            'penyewa'  => User::with('biodata')
                        ->where('role', 'User')
                        ->whereDoesntHave('pemesanan', function (Builder $q) 
                            {$q->whereIn('status', ['Menunggu Pembayaran', 'Aktif']);})
                        ->get(),

            'kamars' => Kamar::whereIn('status', ['Kosong','Dipesan'])
                ->orderBy('kode')
                ->get(),

            'fasilitas' => Fasilitas::where('stok', '>', 0)->get(),

            'Sampah'    => Pemesanan::onlyTrashed()->count()
        ];
        
        return view('pages.admins.pemesanan.data-pemesanan', $data);
    }

    public function lap()
    {

        view()->share('title', 'Laporan data Pemesanan kamar dan fasilitas');

        $data = [
            'item'     => Pemesanan::with(['user', 'kamar', 'fasilitas'])
                        ->orderBy('status', 'ASC')
                        ->orderBy('kode_pemesanan', 'ASC')
                        ->get(),

            'penyewa'  => User::with('biodata')
                        ->where('role', 'User')
                        ->whereDoesntHave('pemesanan', function (Builder $q) 
                            {$q->whereIn('status', ['Menunggu Pembayaran', 'Aktif']);})
                        ->get(),

            'kamars' => Kamar::whereIn('status', ['Kosong','Dipesan'])
                ->orderBy('kode')
                ->get(),

            'fasilitas' => Fasilitas::where('stok', '>', 0)->get(),

            'Sampah'    => Pemesanan::onlyTrashed()->count()
        ];
        
        return view('pages.admins.pemesanan.laporan-pemesanan', $data);
    }

    public function invoice($id)
    {

        view()->share('title', 'Invoice Pemesanan kamar dan fasilitas');

        $data = [
            'item'     => Pemesanan::with(['user', 'kamar', 'fasilitas'])
                        ->orderBy('status', 'ASC')
                        ->orderBy('kode_pemesanan', 'ASC')
                        ->get()
                        ->findOrFail($id),

            'penyewa'  => User::with('biodata')
                        ->where('role', 'User')
                        ->whereDoesntHave('pemesanan', function (Builder $q) 
                            {$q->whereIn('status', ['Menunggu Pembayaran', 'Aktif']);})
                        ->get(),

            'kamars' => Kamar::whereIn('status', ['Kosong','Dipesan'])
                ->orderBy('kode')
                ->get(),

        ];
        
        return view('pages.admins.pemesanan.invoice', $data);
    }

    public function trash()
    {

        view()->share('title', 'Sampah Data Pemesanan kamar dan fasilitas');

        $data = [
            'item'      => Pemesanan::with(['user', 'kamar', 'fasilitas'])
                        -> onlyTrashed()
                        ->latest()
                        ->get(),

            'penyewa'   => User::with('biodata')
                        ->where('role', 'User')
                        ->whereDoesntHave('pemesanan', function (Builder $q) 
                            {$q->whereIn('status', ['Menunggu Pembayaran', 'Aktif']);})
                        ->get(),

            'fasilitas' => Fasilitas::where('stok', '>', 0)->get(),

            'Sampah'    => Pemesanan::onlyTrashed()->count()
        ];
        
        return view('pages.admins.pemesanan.sampah-pemesanan', $data);
    }

    public function getKamars(Request $request)
    {
        $user = User::with('biodata')->find($request->user_id);

        if (!$user || !$user->biodata) {
            return response()->json([]);
        }

        // Normalisasi gender
        $gender = strtolower(trim($user->biodata->jenis_kelamin));

        $kamars = Kamar::whereRaw('LOWER(status) = ?', ['kosong'])->where(function ($q) use ($gender) {
            $q->whereRaw('LOWER(khusus) = ?', [$gender])
            ->orWhereRaw('LOWER(khusus) = ?', ['keluarga']);
        })->get(['id','kode','harga','foto','status','khusus']);

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
                'title' => 'Berhasil Pemesanan berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menambahkan pemesanan'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kamar_id'      => 'required|exists:kamars,id',
            'fasilitas_ids' => 'nullable|array'
        ]);

        try {

            DB::beginTransaction();

            $pemesanan = Pemesanan::with(['kamar', 'fasilitas'])
                            ->lockForUpdate()
                            ->findOrFail($id);

            // 🔒 Hanya bisa edit jika masih boleh
            if (!$pemesanan->masihBisaEdit()) {
                return back()->with('alert', [
                    'icon' => 'error',
                    'title' => 'Pemesanan tidak bisa diedit lagi'
                ]);
            }

            $kamarLama = $pemesanan->kamar;
            $kamarBaru = Kamar::lockForUpdate()->findOrFail($request->kamar_id);

            /* ============================================
            1. HANDLE GANTI KAMAR
            ============================================ */

            if ($kamarLama->id != $kamarBaru->id) {

                // Kosongkan kamar lama
                $kamarLama->update(['status' => 'Kosong']);

                // Set kamar baru jadi Dipesan
                $kamarBaru->update(['status' => 'Dipesan']);

                $pemesanan->kamar_id = $kamarBaru->id;
            }

            /* ============================================
            2. BALIKIN STOK FASILITAS LAMA
            ============================================ */

            foreach ($pemesanan->fasilitas as $fas) {
                $fas->increment('stok');
            }

            $pemesanan->fasilitas()->detach();

            /* ============================================
            3. PROSES FASILITAS BARU
            ============================================ */

            $totalHargaFasilitas = 0;

            if ($request->fasilitas_ids) {

                $fasilitasBaru = Fasilitas::whereIn('id', $request->fasilitas_ids)
                                    ->lockForUpdate()
                                    ->get();

                foreach ($fasilitasBaru as $fas) {

                    if ($fas->stok < 1) {
                        throw new \Exception("Stok {$fas->nama} habis");
                    }

                    $pemesanan->fasilitas()->attach($fas->id, [
                        'harga_snap' => $fas->harga
                    ]);

                    $totalHargaFasilitas += $fas->harga;
                    $fas->decrement('stok');
                }
            }

            /* ============================================
            4. HITUNG ULANG TOTAL
            ============================================ */

            $jenis  = $pemesanan->jenis_sewa;

            $tglMasuk  = Carbon::parse($pemesanan->tgl_masuk);
            $tglKeluar = Carbon::parse($pemesanan->tgl_keluar);

            if ($jenis === 'Bulanan') {
                $durasi = $tglMasuk->diffInMonths($tglKeluar);
            } else {
                $durasi = $tglMasuk->diffInDays($tglKeluar);
            }

            $hargaKamar = $kamarBaru->harga;

            if ($jenis === 'Bulanan') {
                $total = ($hargaKamar + $totalHargaFasilitas) * $durasi;
            } else {
                $total = (($hargaKamar + $totalHargaFasilitas) / 30) * $durasi;
            }

            $pemesanan->total_harga = ceil($total);

            $pemesanan->save();

            DB::commit();

            return back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Pemesanan berhasil diperbarui'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal memperbarui pemesanan'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            Pemesanan::findOrFail($id)->delete();

            return back()->with('alert', [
                'icon' => 'success', 
                'title' => 'Data berhasil dihapus dan dipindahkan ke sampah.'
            ]);

        } catch (\Exception $e) {
            return back()->with('alert', [
                'icon' => 'error', 
                'title' => 'Data gagal dihapus.'
            ]);
        }
    }

    public function restore(string $id)
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

            Pemesanan::onlyTrashed()->findOrFail($id)->restore();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Pemesanan telah berhasil dikembalikan'
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal restore pemesanan dengan ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Pemesanan gagal dikembalikan'
            ]);
        }
    }

    public function force(string $id)
    {
        try {
            Pemesanan::onlyTrashed()->findOrFail($id)->forceDelete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Pemesanan telah berhasil dihapus permananen'
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal hapus pemesanan dengan ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Pemesanan gagal dihapus'
            ]);
        }
    }

}