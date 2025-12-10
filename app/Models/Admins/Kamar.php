<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Kamar extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'kode',
        'slug',
        'deskripsi',
        'harga',
        'status',
        'khusus',
        'foto'
    ];

    public static function generateNextCode($prefix)
    {

        $lastCode = self::withTrashed()
                        ->where('kode', 'like', $prefix . '%')
                        ->orderBy('kode', 'desc')
                        ->value('kode');

        if (!$lastCode) {
            return $prefix . '0001';
        }

        $number = intval(substr($lastCode, strlen($prefix)));
        return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);

    }

    protected static function booted()
    {

        static::creating(function ($kamar) {
            $kamar->slug = Str::slug($kamar->kode);
        });

        static::updating(function ($kamar) {
            $kamar->slug = Str::slug($kamar->kode . ' ' . $kamar->khusus . ' ' . $kamar->id, '-');
        });

        static::forceDeleted(function ($kamar) {
            if ($kamar->foto && Storage::disk('public')->exists('uploads/kamar/' . $kamar->foto)) {
                Storage::disk('public')->delete('uploads/kamar/' . $kamar->foto);
            }
        });

    }

}
