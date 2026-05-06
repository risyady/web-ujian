<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    protected $fillable = [
        'siswa_ujian_id',
        'soal_id',
        'id_pilihan_terpilih',
        'jawaban_teks',
        'pasangan_terpilih'
    ];

    protected $casts = [
        'id_pilihan_terpilih' => 'array',
        'pasangan_terpilih' => 'array'
    ];

    public function siswaUjian() {
        return $this->belongsTo(SiswaUjian::class);
    }

    public function soal() {
        return $this->belongsTo(Soal::class);
    }
}
