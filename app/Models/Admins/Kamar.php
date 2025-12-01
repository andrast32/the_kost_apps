<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kamar extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_kamar',
        'deskripsi',
        'harga',
        'status',
        'khusus',
        'foto',
        'slug_kamar',
    ];

}
