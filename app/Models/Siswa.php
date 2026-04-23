<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    /** @use HasFactory<\Database\Factories\SiswaFactory> */
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ([
        'user_id',
        'nisn',
        'jurusan_id',
    ]);

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function jurusan() {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}
