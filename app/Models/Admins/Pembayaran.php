<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $guarded = [];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar'  => 'decimal:2'
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class,'id_pemesanan');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function isLunas(): bool
    {
        return $this->status === 'Lunas';
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'Menunggu';
    }
}
