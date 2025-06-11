<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{


    protected $fillable = [
        'gambar_program',
        'nama_program',
        'slug',
        'deskripsi',
        'status', // draft, published, archived
    ];

    // Auto-generate slug
    protected static function booted()
    {
        static::creating(function ($program) {
            $program->slug = \Illuminate\Support\Str::slug($program->nama_program);
        });

        static::updating(function ($program) {
            $program->slug = \Illuminate\Support\Str::slug($program->nama_program);
        });
    }

    // Optional: Route model binding by slug
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
