<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Siswa;
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
        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */

        Jurusan::factory()->count(3)->create();

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => '12345678',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Test Guru',
            'email' => 'guru@example.com',
            'password' => '12345678',
            'role' => 'guru',
        ]);

        $user = User::factory()->create([
            'name' => 'Test Siswa',
            'email' => 'siswa@example.com',
            'password' => '12345678',
            'role' => 'siswa',
        ]);

        Siswa::factory()->create([
            'user_id' => $user->id,
            'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
        ]);

        User::factory()->count(5)->create();

        User::factory()->setRole('siswa')->count(10)->create()->each(function ($user) {
            Siswa::factory()->create([
                'user_id' => $user->id,
                'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
            ]);
        });
    }
}
