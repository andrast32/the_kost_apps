<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admins\Kamar;
use App\Models\Admins\Fasilitas;

class Pemesanan extends Model
{
    use SoftDeletes;

    protected $table = 'pemesanan';
    protected $guarded = [];

    protected $casts = [
        'tgl_masuk'  => 'date',
        'tgl_keluar' => 'date',
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function fasilitas()
    {
        return $this->belongsToMany(
                Fasilitas::class,
                'pemesanan_fasilitas',
                'pemesanan_id',
                'fasilitas_id'
            )
            ->withPivot(['harga_snap']);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS CHECKER
    |--------------------------------------------------------------------------
    */

    public function masihBisaEdit(): bool
    {
        return $this->status === 'Menunggu Pembayaran'
            && $this->created_at
            && $this->created_at->diffInHours(now()) <= 24;
    }

    public function isExpired(): bool
    {
        return $this->status === 'Menunggu Pembayaran'
            && $this->created_at
            && now()->greaterThan($this->created_at->copy()->addHours(48));
    }

    public function isAktif(): bool
    {
        return $this->status === 'Aktif';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'Selesai';
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO CANCEL (48 JAM)
    |--------------------------------------------------------------------------
    */

    public static function autoCancelExpired(): int
    {
        $expiredOrders = self::where('status', 'Menunggu Pembayaran')
            ->where('created_at', '<=', now()->subHours(48))
            ->with(['kamar', 'fasilitas'])
            ->get();

        foreach ($expiredOrders as $order) {

            \DB::transaction(function () use ($order) {

                // 1️⃣ Ubah status
                $order->update([
                    'status' => 'Dibatalkan'
                ]);

                // 2️⃣ Kosongkan kamar
                if ($order->kamar) {
                    $order->kamar->update([
                        'status' => 'Kosong'
                    ]);
                }

                // 3️⃣ Kembalikan stok fasilitas
                foreach ($order->fasilitas as $f) {

                    $jumlah = $f->pivot->jumlah ?? 1;

                    $f->increment('stok', $jumlah);
                }
            });
        }

        return $expiredOrders->count();
    }
}
