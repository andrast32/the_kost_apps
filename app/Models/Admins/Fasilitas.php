<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Fasilitas extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'kode',
        'slug',
        'nama',
        'deskripsi',
        'harga',
        'stok',
        'foto'
    ];

    public static function generateCode($prefix)
    {

        $lastCode = self::withTrashed()
                        ->where('kode', 'like', $prefix . '%')
                        ->orderBy('kode', 'desc')
                        ->value('kode');

        if(!$lastCode) {
            return $prefix . '0001';
        }

        $number = intval(substr($lastCode, strlen($prefix)));
        return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);

    }

    protected static function booted()
    {

        static::creating(function ($fasilitas) {
            $fasilitas->slug = Str::slug($fasilitas->kode . ' ' . $fasilitas->nama, '-');
        });

        static::updating(function ($fasilitas) {
            $fasilitas->slug = Str::slug($fasilitas->kode . ' ' . $fasilitas->nama . ' ' . $fasilitas->id, '-');
        });

        static::forceDeleted(function ($fasilitas) {
            if ($fasilitas->foto && Storage::disk('public')->exists('uploads/fasilitas/' . $fasilitas->foto)) {
                Storage::disk('public')->delete('uploads/fasilitas/' . $fasilitas->foto);
            }
        });

    }

}
