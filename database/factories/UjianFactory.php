<?php

namespace Database\Factories;

use App\Models\Ujian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ujian>
 */
class UjianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $namaMapel = $this->faker->randomElement([
            'Bahasa Inggris',
            'Bahasa Indonesia',
            'Pemrograman Dasar',
            'Matematika',
            'Seni Budaya',
            'Geografi',
        ]);
        $tipeUjian = $this->faker->randomElement([
            'harian', 'sts', 'uts', 'uas',
        ]);
        $kelas = $this->faker->randomElement([
            'X', 'XI', 'XII', 'XI RPL 1', 'X RPL 2', 'XXI',
        ]);
        $tahun = $this->faker->year();
        $tipeUjian = $this->faker->randomElement([
            'harian', 'sts', 'uts', 'uas',
        ]);
        $waktuMulai = $this->faker->time();
        $status = $this->faker->randomElement([
            'draft', 'published', 'ongoing', 'finished',
        ]);
        
        $judulUjian = $tipeUjian . " " . $namaMapel;
        $tahunAjar = $tahun . "/" . ($tahun-1); 
        $waktuSelesai = Carbon::parse($waktuMulai)->addMinutes(60)->format('H:i:s');

        return [
            'judul_ujian' => $judulUjian,
            'guru_id' => User::where('role', 'guru')->inRandomOrder()->first()->id,
            'kelas' => $kelas,
            'tahun_ajar' => $tahunAjar,
            'tipe_ujian' => $tipeUjian,
            'semester' => $this->faker->randomElement(['ganjil', 'genap']),
            'kode_ujian' => $this->faker->unique()->regexify('[A-Z0-9]{6}'),
            'durasi_menit' => 60,
            'tanggal_ujian' => $this->faker->date(),
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'status' => $status,
        ];
    }
}
