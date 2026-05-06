<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Soal extends Model
{
    /** @use HasFactory<\Database\Factories\SoalFactory> */
    use HasFactory;

    protected $appends = ['url_gambar'];

    protected $fillable = [
        'ujian_id',
        'teks_soal',
        'tipe_soal',
        'path_gambar',
    ];

    public function getUrlGambarAttribute(): ?string {
        if (!$this->path_gambar) return null;
        return Storage::disk('s3')->url($this->path_gambar);
    }

    public function ujian() {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function pilihanJawaban() {
        return $this->hasMany(PilihanJawaban::class, 'soal_id');
    }
}
