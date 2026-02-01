<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Pemesanan extends Model
{
    use SoftDeletes;

    protected $table = 'pemesanan';
    protected $guarded = [];

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
        )->withPivot('harga_snap');
    }

    /** 
     * cek apakah masih boleh diedit (2x24 jam)
     */
    public function masihBisaEdit(): bool
    {
        return now()->lessThanOrEqualTo(
            $this->created_at->copy()->addHours(48)
        );
    }
}
