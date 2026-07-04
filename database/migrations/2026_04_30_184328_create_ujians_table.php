<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->string('judul_ujian', 150)->index();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->string('kelas', 15);
            $table->string('tahun_ajar', 10);
            $table->enum('tipe_ujian', ['harian', 'sts', 'uts', 'uas'])->default('harian');
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->string('kode_ujian', 10)->nullable()->unique();
            $table->unsignedTinyInteger('durasi_menit')->default(60);
            $table->date('tanggal_ujian');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('status', ['draft', 'published', 'ongoing', 'finished'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
