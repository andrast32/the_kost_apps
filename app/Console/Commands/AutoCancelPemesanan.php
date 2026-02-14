<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admins\Pemesanan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoCancelPemesanan extends Command
{
    protected $signature = 'sewa:autocancel';
    protected $description = 'Batalkan pemesanan yang belum dibayar lebih dari 2x24 jam';

    public function handle()
    {
        $batas = Carbon::now()->subHours(48);

        $orders = Pemesanan::with(['kamar', 'fasilitas'])
            ->where('status', 'Menunggu Pembayaran')
            ->where('created_at', '<=', $batas)
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($order) {

                $order->update(['status' => 'Batal']);

                if ($order->kamar) {
                    $order->kamar->update(['status' => 'Kosong']);
                }

                foreach ($order->fasilitas as $f) {
                    $f->increment('stok');
                }
            });

            $count++;
        }

        $this->info("{$count} pemesanan dibatalkan otomatis.");
    }
}
