<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->setRole('siswa'),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'jurusan_id' => Jurusan::factory(),
        ];
    }
}
