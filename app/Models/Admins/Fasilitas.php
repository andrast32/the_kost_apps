<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fasilitas extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'harga',
        'stok',
        'foto',
        'slug'
    ];

    public static function generateCode($prefix)
    {
        
    }

}
