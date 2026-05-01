<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\UserController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

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
    });

    Route::apiResource('ujian', UjianController::class);
});

