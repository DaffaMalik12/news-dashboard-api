<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // ← ini yang penting ditambahkan!

class structure extends Model
{
    protected $fillable =  [
        'gambar',
        'nama',
        'jabatan',
        'detail'
    ];
}
