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
        Schema::create('pilihan_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->text('teks_pilihan')->nullable();
            $table->text('teks_pasangan')->nullable();
            $table->unsignedTinyInteger('persentase_nilai')->default(0)->nullable();
            $table->boolean('is_true')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawabans');
    }
};
