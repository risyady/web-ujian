<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['admin', 'guru', 'siswa']);

        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => $role,
            'password' => static::$password ??= Hash::make('password'),
            'nisn' => $role === 'siswa' ? $this->faker->unique()->numerify('##########') : null,
            'jurusan_id' => $role === 'siswa' ? Jurusan::inRandomOrder()->first()->id : null,
        ];
    }
}
