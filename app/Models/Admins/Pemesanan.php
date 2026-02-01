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

    protected $table = 'pemesanan'; // Pastikan nama tabel singular
    protected $guarded = [];

    // Relasi ke User (Penyewa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Kamar
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    // Relasi ke Fasilitas (Banyak ke Banyak)
    public function fasilitas()
    {
        // 'pemesanan_fasilitas' adalah nama tabel pivot
        return $this->belongsToMany(Fasilitas::class, 'pemesanan_fasilitas', 'pemesanan_id', 'fasilitas_id')
                    ->withPivot('harga_snap');
    }
}