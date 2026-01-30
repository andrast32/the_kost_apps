<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\{Pemesanan, Kamar, Fasilitas};
use App\Models\Admins\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PemesananController extends Controller
{

    public function index()
    {
        $items      = Pemesanan::with(['user', 'kamar', 'fasilitas'])->latest()->get();
        $penyewa    = User::where('role', 'penyewa')->get();
        $fasilitas  = Fasilitas::all();
        // kirim waktu sekarang
        $now = now();

        return view('pages.admins.pemesanan.data-pemesanan', compact('items', 'penyewa', 'fasilitas', 'now'));
    }

    public function store(Request $request)
    {

        $kamar = Kamar::findOrFail($request->kamar_id);
        $Masuk = Carbon::parse($request->tgl_masuk);

        if ($request->jenis_sewa == 'Bulanan') {
            $Keluar = $Masuk->copy()->addMonths($request->durasi);
        } else {
            $Keluar = $Masuk->copy()->addDays($request->durasi);
        }

        $fasilitas = Fasilitas::whereIn('id', $request->fasilitas_ids ?? [])->sum('harga');
        $harga = ($kamar->harga + $fasilitas) * $request->durasi;

        $pemesanan = Pemesanan::create([
            'user_id'       => $request->user_id,
            'kamar_id'      => $request->kamar_id,
            'tgl_masuk'     => $Masuk,
            'tgl_keluar'    => $Keluar,
            'jenis_sewa'    => $request->jenis_sewa,
            'total_harga'   => $harga,
            'status'        => 'Aktif'
        ]);

        if($request->fasilitas_ids) {
            foreach ($request->fasilitas_ids as $fId) {
                $f = Fasilitas::find($fId);
                $pemesanan->fasilitas()->attach($fId, ['harga_snap' => $f->harga]);
            }
        }

    }

    public function destroy(Pemesanan $pemesanan)
    {
        //
    }
    
}
