<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JawabanSiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PengaturanAdminController;
use App\Http\Controllers\PengaturanUjianController;
use App\Http\Controllers\SiswaUjianController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::put('auth/me/edit', [AuthController::class, 'editProfile']);

    Route::middleware('role:admin,superadmin')->group(function() {
        Route::apiResource('user', UserController::class);
        Route::apiResource('jurusan', JurusanController::class);
        Route::post('user/bulk-store', [UserController::class, 'bulkStore']);
        Route::get('user/{user}/reset-password', [UserController::class, 'resetPassword']);

        Route::get('pengaturan', [PengaturanAdminController::class, 'index']);
        Route::put('pengaturan', [PengaturanAdminController::class, 'update']);
    });

    Route::apiResource('ujian', UjianController::class);

    Route::get('ujian/{ujian}/pengaturan', [PengaturanUjianController::class, 'show']);
    Route::put('ujian/{ujian}/pengaturan', [PengaturanUjianController::class, 'update']);

    Route::apiResource('ujian.soal', SoalController::class);

    Route::middleware('cek_ip')->group(function() {
        Route::post('ujian/redeem', [SiswaUjianController::class, 'redeemCode']);
        Route::post('ujian/{siswaUjian}/submit', [SiswaUjianController::class, 'submit']);
        
        Route::post('ujian/{siswaUjian}/jawaban', [JawabanSiswaController::class, 'save']);
    });

    Route::middleware('role:guru,admin,superadmin')->group(function() {
        Route::get('ujian/{siswaUjian}/nilai', [NilaiController::class, 'show']);
        
        Route::put('ujian/{siswaUjian}/essay', [NilaiController::class, 'inputEssay']);
        Route::put('ujian/{siswaUjian}/isian', [NilaiController::class, 'inputFillInBlank']);
    });
});

