<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SelesaiPemesanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:selesai-pemesanan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pemesananList = Pemesanan::where('status', 'Aktif')
            ->where('tgl_keluar', '<=', now())
            ->get();

        foreach ($pemesananList as $p) {

            foreach ($p->fasilitas as $f) {
                $f->increment('stok');
            }

            $p->kamar->update(['status' => 'Kosong']);
            $p->update(['status' => 'Selesai']);
        }
    }
}
