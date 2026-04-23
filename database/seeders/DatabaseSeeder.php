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

        User::factory()->count(5)->create();

        Jurusan::factory()->count(3)->create();

        User::factory()->setRole('siswa')->count(10)->create()->each(function ($user) {
            Siswa::factory()->create([
                'user_id' => $user->id,
                'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
            ]);
        });
    }
}
