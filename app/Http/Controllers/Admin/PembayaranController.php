<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Pembayaran;
use App\Models\Admins\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index()
    {
        view()->share('title', 'Data Pembayaran');

        $item = Pembayaran::with([
            'pemesanan.user.biodata',
            'pemesanan.kamar',
            'pemesanan.fasilitas',
            'admin'
        ])
        ->latest()
        ->get();

        return view('pages.admins.pembayaran.data-pembayaran', compact('item'));
    }

    /*
    |--------------------------------------------------------------------------
    | BAYAR OFFLINE
    |--------------------------------------------------------------------------
    */
    public function bayar(Request $request, $id)
    {
        $pemesanan = Pemesanan::with([
            'kamar', 'fasilitas'
        ])->findOrFail($id);

        // validasi
        if ($pemesanan->status !== 'Menunggu Pembayaran') {
            return back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Transaksi Gagal',
                'text'  => 'Pemesanan ini tidak dapat dibayar.'
            ]);
        }

        // TRANSAKSI
        DB::transaction(function () use ($pemesanan) {

            Pembayaran::create([
                'id_pemesanan'      => $pemesanan->id,
                'tanggal_bayar'     => now(),
                'jumlah_bayar'      => $pemesanan->total_harga,
                'metode_pembayaran' => 'Cash',
                'transaction_id'    => null,
                'bukti'             => null,
                'status'            => 'Lunas',
                'id_admin'          => Auth::id()
            ]);

            $pemesanan->update(['status' => 'Aktif']);

        });

        return back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Transaksi Berhasil',
            'text'  => 'Pembayaran berhasil. Pemesanan sekarang aktif.'
        ]);
    }
}
