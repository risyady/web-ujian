<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\PengaturanAdmin;
use App\Models\Ujian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Jurusan::factory()->count(3)->create();

        User::factory()->create([
            'nama' => 'Test SuperAdmin',
            'email' => 'suadmin@example.com',
            'password' => '123456',
            'role' => 'superadmin',
            'nisn' => null,
            'jurusan_id' => null,
        ]);

        User::factory()->create([
            'nama' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => '123456',
            'role' => 'admin',
            'nisn' => null,
            'jurusan_id' => null,
        ]);

        User::factory()->create([
            'nama' => 'Test Guru',
            'email' => 'guru@example.com',
            'password' => '123456',
            'role' => 'guru',
            'nisn' => null,
            'jurusan_id' => null,
        ]);

        User::factory()->create([
            'nama' => 'Test Siswa',
            'email' => 'siswa@example.com',
            'password' => '123456',
            'role' => 'siswa',
            'nisn' => '123456789',
            'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
        ]);

        User::factory()->count(10)->create();

        Ujian::factory()->count(5)->create()->each(fn($ujian) => $ujian->pengaturan()->create());

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
            ]
        ]);
    }
}
