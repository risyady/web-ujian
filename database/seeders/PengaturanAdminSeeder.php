<?php

namespace Database\Seeders;

use App\Models\PengaturanAdmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengaturanAdmin::insert([
            [
                'key' => 'allowed_ip',
                'value' => null,
                'keterangan' => 'IP yang diizinkan untuk mengakses ujian. Pisahkan dengan koma jika lebih dari 1.',
            ],
            [
                'key' => 'default_password',
                'value' => '123456',
                'keterangan' => 'Password default saat user dibuat atau reset password.',
            ],
            [
                'key' => 'latitude',
                'value' => null,
                'keterangan' => 'latitude lokasi sekolah.',
            ],
            [
                'key' => 'longitude',
                'value' => null,
                'keterangan' => 'Longitude lokasi sekolah.',
            ],
        ]);
    }
}
