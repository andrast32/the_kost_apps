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
        $data = [
            'items'      => Pemesanan::with(['user', 'kamar', 'fasilitas'])->latest()->get(),
            'penyewa'    => User::where('role', 'User')->get(),
            'kamars'     => Kamar::where('status', 'Kosong')->get(), // ⬅️ TAMBAH INI
            'fasilitas'  => Fasilitas::all(),
            'Sampah'     => Pemesanan::onlyTrashed()->count(),
            'now'        => now(),
        ];

        return view('pages.admins.pemesanan.data-pemesanan', $data);
    }

    public function trash()
    {

        view()->share('title', 'Data Sampah Pemesanan');

        $data = [
            'items'     => Pemesanan::onlyTrashed()->latest()->get(),
            'Sampah'    => Pemesanan::onlyTrashed()->count(),
        ];

        return view('pages.admins.pemesanan.sampah-pemesanan', $data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'kamar_id'   => 'required|exists:kamars,id',
            'tgl_masuk'  => 'required|date',
            'durasi'     => 'required|integer|min:1',
            'jenis_sewa' => 'required|in:Bulanan,Harian',
        ]);

        return DB::transaction(function () use ($request) {

            $kamar = Kamar::lockForUpdate()->findOrFail($request->kamar_id);

            if ($kamar->status === 'Terisi') {
                return back()->with('alert', [
                    'icon' => 'Error',
                    'titile' => 'Kamar sudah terisi'
                ]);
            }

            $Masuk = Carbon::parse($request->tgl_masuk);
            $Keluar = $request->jenis_sewa === 'Bulanan'
                ? $Masuk->copy()->addMonths($request->durasi)
                : $Masuk->copy()->addDays($request->durasi);

            $fasilitasList = collect();
            if ($request->fasilitas_ids) {
                $fasilitasList = Fasilitas::whereIn('id', $request->fasilitas_ids)
                    ->lockForUpdate()->get();

                foreach ($fasilitasList as $f) {
                    if ($f->stok <= 0) {
                        return back()->with('alert', [
                            'icon' => 'Error',
                            'titile' => "Stok {$f->nama_fasilitas} habis"
                        ]);
                    }
                }
            }

            $totalFasilitas = $fasilitasList->sum('harga');
            $harga = ($kamar->harga + $totalFasilitas) * $request->durasi;

            dd($request->all());

            $pemesanan = Pemesanan::create([
                'user_id'     => $request->user_id,
                'kamar_id'    => $kamar->id,
                'tgl_masuk'   => $Masuk,
                'tgl_keluar'  => $Keluar,
                'jenis_sewa'  => $request->jenis_sewa,
                'total_harga' => $harga,
                'status'      => 'Aktif'
            ]);

            foreach ($fasilitasList as $f) {
                $f->decrement('stok');
                $pemesanan->fasilitas()->attach($f->id, [
                    'harga_snap' => $f->harga
                ]);
            }

            $kamar->update(['status' => 'Terisi']);

            return back()->with('alert', [
                'icon' => 'Success',
                'titile' => 'Pemesanan berhasil ditambahkan'
            ]);
        });

    }

    public function update(Request $request, Pemesanan $pemesanan)
    {
        if (!$pemesanan->masihBisaEdit()) {
            return back()->with('alert', [
                'icon' => 'Error',
                'titile' => 'Waktu edit sudah habis (2×24 jam)'
            ]);
        }

        return DB::transaction(function () use ($request, $pemesanan) {

            // balikin stok lama
            foreach ($pemesanan->fasilitas as $f) {
                $f->increment('stok');
            }

            $pemesanan->fasilitas()->detach();

            $fasilitasList = Fasilitas::whereIn('id', $request->fasilitas_ids ?? [])
                ->lockForUpdate()->get();

            foreach ($fasilitasList as $f) {
                if ($f->stok <= 0) {
                    return back()->with('alert', [
                        'icon' => 'Error',
                        'titile' => "Stok {$f->nama_fasilitas} habis"
                    ]);
                }
            }

            foreach ($fasilitasList as $f) {
                $f->decrement('stok');
                $pemesanan->fasilitas()->attach($f->id, [
                    'harga_snap' => $f->harga
                ]);
            }

            return back()->with('alert', [
                'icon' => 'Success',
                'titile' => 'Pemesanan berhasil diperbarui'
            ]);
        });
    }

    public function destroy(Pemesanan $pemesanan)
    {
        //
    }
    
}
