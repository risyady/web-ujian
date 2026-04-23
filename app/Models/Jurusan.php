<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    /** @use HasFactory<\Database\Factories\JurusanFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan'
    ];

    public function siswa() {
        return $this->hasMany(Siswa::class, 'jurusan_id');
    }
}
