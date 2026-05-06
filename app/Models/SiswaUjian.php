<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaUjian extends Model
{
    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'nilai_akhir',
        'urutan_soal'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'urutan_soal' => 'array'
    ];

    public function ujian() {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function siswa() {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function jawaban() {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function ujianTimeOut(): bool {
        $duration = $this->ujian->durasi_menit * 60;
        $used = now()->diffInSeconds($this->waktu_mulai);
        $remaining = $duration - $used;
        
        return max(0, $remaining);
    }

    public function allJawabanScored(): bool {
        $soalManual = $this->ujian->soal()
            ->whereIn('tipe_soal', ['isian', 'essay'])
            ->pluck('id');
        
        if ($soalManual->isEmpty()) return true;

        $scored = $this->jawaban()
            ->whereIn('soal_id', $soalManual)
            ->whereNotNull('nilai_manual_guru')
            ->count();

        return $scored === $soalManual->count();
    }
}
