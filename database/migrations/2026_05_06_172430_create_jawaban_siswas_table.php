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
        Schema::create('jawaban_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_ujian_id')->constrained('siswa_ujians')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->json('id_pilihan_terpilih')->nullable();
            $table->text('jawaban_teks')->nullable();
            $table->json('pasangan_terpilih')->nullable();
            $table->unsignedTinyInteger('nilai_manual_guru')->nullable();
            $table->timestamps();

            $table->unique(['siswa_ujian_id', 'soal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswas');
    }
};
