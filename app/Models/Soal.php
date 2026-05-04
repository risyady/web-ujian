<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    /** @use HasFactory<\Database\Factories\SoalFactory> */
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'teks_soal',
        'tipe_soal',
        'jalur_gambar',
    ];

    public function ujian() {
        return $this->belongsTo(Ujian::class);
    }

    public function pilihanJawaban() {
        return $this->hasMany(PilihanJawaban::class);
    }
}
