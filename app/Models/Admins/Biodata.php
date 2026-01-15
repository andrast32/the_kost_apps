<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{

    protected $table = 'biodata';

    protected $fillable = [
        'user_id',
        'no_hp',
        'jenis_kelamin',
        'pekerjaan',
        'alamat',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
