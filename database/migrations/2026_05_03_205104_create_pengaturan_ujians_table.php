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
        Schema::create('pengaturan_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->unique()->constrained('ujians')->onDelete('cascade');
            $table->unsignedTinyInteger('bobot_objektif')->default(20);
            $table->unsignedTinyInteger('bobot_ganda_kompleks')->default(20);
            $table->unsignedTinyInteger('bobot_menjodohkan')->default(20);
            $table->unsignedTinyInteger('bobot_isian')->default(20);
            $table->unsignedTinyInteger('bobot_essay')->default(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_ujians');
    }
};
