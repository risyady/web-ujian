<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAdmin extends Model
{
    /** @use HasFactory<\Database\Factories\PengaturanAdminFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'keterangan'
    ];

    public static function ambil(string $key): ?string {
        return static::where('key', $key)->value('value');
    }

    public static function set(string $key, ?string $value): void {
        static::where('key', $key)->update(['value' => $value]);
    }
}
