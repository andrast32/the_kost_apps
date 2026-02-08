<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
