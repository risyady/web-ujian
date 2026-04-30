<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    /** @use HasFactory<\Database\Factories\UjianFactory> */
    use HasFactory;

    protected $fillable = [
        'judul_ujian',
        'guru_id',
        'kelas',
        'tahun_ajar',
        'tipe_ujian',
        'semester',
        'kode_ujian',
        'durasi_menit',
        'tanggal_ujian',
        'waktu_mulai',
        'waktu_selesai',
        'status',
    ];

    public function guru() {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
