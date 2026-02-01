<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\{Pemesanan, Kamar, Fasilitas};
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    public function index()
    {
        $items = Pemesanan::with(['user', 'kamar', 'fasilitas'])
            ->where('status', 'Aktif')
            ->latest()
            ->get();
            
        $penyewas = User::where('role', 'User')->get();
        $fasilitas = Fasilitas::where('stok', '>', 0)->get();
        
        return view('pages.admins.pemesanan.data-pemesanan', compact('items', 'penyewas', 'fasilitas'));
    }

    public function getKamars(Request $request)
    {
        // 1. Validasi User
        $user = User::with('biodata')->find($request->user_id);
        
        if (!$user || !$user->biodata) {
            return response()->json(['error' => 'User belum melengkapi biodata'], 400);
        }

        $gender = $user->biodata->jenis_kelamin; // 'Laki-Laki' atau 'Perempuan'

        // 2. Filter Kamar (PERBAIKAN STATUS DISINI)
        // Cari status 'Kosong' (bukan 'Tersedia')
        $kamars = Kamar::where('status', 'Kosong')
                    ->where(function($q) use ($gender) {
                        $q->where('khusus', $gender)
                            ->orWhere('khusus', 'Keluarga');
                    })->get();

        return response()->json($kamars);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'kamar_id' => 'required',
            'tgl_masuk' => 'required|date',
            'durasi' => 'required|numeric|min:1',
            'jenis_sewa' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                $kamar = Kamar::findOrFail($request->kamar_id);
                $tglMasuk = Carbon::parse($request->tgl_masuk);
                
                // Hitung Tgl Keluar
                if ($request->jenis_sewa == 'Bulanan') {
                    $tglKeluar = $tglMasuk->copy()->addMonths($request->durasi);
                } else {
                    $tglKeluar = $tglMasuk->copy()->addDays($request->durasi);
                }

                // Cek Stok Fasilitas
                $fasilitasIds = $request->fasilitas_ids ?? [];
                $totalHargaFasilitas = 0;
                $fasilitasData = [];

                if (!empty($fasilitasIds)) {
                    $fasilitasItems = Fasilitas::whereIn('id', $fasilitasIds)->lockForUpdate()->get();

                    foreach ($fasilitasItems as $f) {
                        if ($f->stok < 1) {
                            throw new \Exception("Stok {$f->nama_fasilitas} habis!");
                        }
                        
                        $fasilitasData[] = ['id' => $f->id, 'harga' => $f->harga];
                        $totalHargaFasilitas += $f->harga;
                        $f->decrement('stok');
                    }
                }

                // Hitung Harga
                // Jika harian, harga kamar dibagi 30
                $hargaKamarDasar = ($request->jenis_sewa == 'Harian') ? ($kamar->harga / 30) : $kamar->harga;
                $hargaFasilitasDasar = ($request->jenis_sewa == 'Harian') ? ($totalHargaFasilitas / 30) : $totalHargaFasilitas;
                
                $totalTrans = ($hargaKamarDasar + $hargaFasilitasDasar) * $request->durasi;

                // Simpan Pemesanan
                $pemesanan = Pemesanan::create([
                    'user_id'       => $request->user_id,
                    'kamar_id'      => $request->kamar_id,
                    'tgl_masuk'     => $tglMasuk,
                    'tgl_keluar'    => $tglKeluar,
                    'jenis_sewa'    => $request->jenis_sewa,
                    'total_harga'   => $totalTrans,
                    'status'        => 'Aktif'
                ]);

                foreach ($fasilitasData as $fd) {
                    $pemesanan->fasilitas()->attach($fd['id'], ['harga_snap' => $fd['harga']]);
                }

                // PERBAIKAN: Ubah status jadi 'Terisi' (bukan 'Penuh')
                $kamar->update(['status' => 'Terisi']);
            });

            return redirect()->back()->with('alert', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Pemesanan berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('alert', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $e->getMessage()
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