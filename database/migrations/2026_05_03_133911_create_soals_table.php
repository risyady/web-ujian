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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained("ujians")->onDelete('cascade');
            $table->text('teks_soal')->index();
            $table->enum('tipe_soal', [
                'objektif', 
                'ganda_kompleks', 
                'menjodohkan', 
                'isian', 
                'essay',
            ]);
            $table->string('path_gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
