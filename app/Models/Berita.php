<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // ← ini yang penting ditambahkan!

class Berita extends Model
{
    //
    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'kategori',
        'tanggal_publish',
        'penulis',
        'gambar',
    ];


    // Auto-generate slug
    protected static function booted()
    {
        static::creating(function ($berita) {
            $berita->slug = Str::slug($berita->judul);
        });

        static::updating(function ($berita) {
            $berita->slug = Str::slug($berita->judul);
        });
    }

    // Optional: Route model binding by slug
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
