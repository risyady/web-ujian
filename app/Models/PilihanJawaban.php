<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    /** @use HasFactory<\Database\Factories\PilihanJawabanFactory> */
    use HasFactory;

    protected $fillable = [
        'soal_id',
        'teks_pilihan',
        'teks_pasangan',
        'persentase_nilai',
        'is_true',
    ];

    public function soal() {
        return $this->belongsTo(Soal::class);
    }
}
