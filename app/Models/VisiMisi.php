<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisiMisi extends Model
{
    protected $table = 'visimisi';

    protected $fillable = [
        'visi',
        'misi',
    ];
}
