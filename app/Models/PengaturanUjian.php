<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanUjian extends Model
{
    /** @use HasFactory<\Database\Factories\PengaturanUjianFactory> */
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'bobot_objektif',
        'bobot_ganda_kompleks',
        'bobot_menjodohkan',
        'bobot_isian',
        'bobot_essay',
    ];
}
