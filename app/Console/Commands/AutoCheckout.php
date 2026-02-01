<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admins\Pemesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoCheckout extends Command
{
    protected $signature = 'sewa:autocheckout';
    protected $description = 'Otomatis set selesai pemesanan yang sudah lewat tanggal';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Cari pemesanan yang statusnya masih 'Aktif' tapi tgl_keluar < hari ini
        $expiredOrders = Pemesanan::with(['kamar', 'fasilitas'])
                        ->where('status', 'Aktif')
                        ->where('tgl_keluar', '<', $today)
                        ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // 1. Ubah Status Pemesanan
                $order->update(['status' => 'Selesai']);

                // 2. Kosongkan Kamar
                if ($order->kamar) {
                    $order->kamar->update(['status' => 'Kosong']);
                }

                // 3. Kembalikan Stok Fasilitas
                foreach ($order->fasilitas as $f) {
                    $f->increment('stok');
                }
            });
            $count++;
        }

        $this->info("Berhasil memproses {$count} pemesanan yang berakhir.");
    }
}