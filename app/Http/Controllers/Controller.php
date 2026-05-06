<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    protected function uploadImage($file, string $folder = 'soal'): string {
        $path = $file->store($folder, 's3');
        Storage::disk('s3')->setVisibility($path, 'public');
        return $$path;
    }

    protected function deleteImage(?string $path): void {
        if ($path && Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
        }
    }

    protected function urlGambar(?string $path): ?string {
        if (!$path) return null;
        return Storage::disk('s3')->url($path);
    }

    protected function successResponse($data, string $message = 'Berhasil', int $code = 200) {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function createdResponse($data, string $message = 'Berhasil dibuat') {
        return $this->successResponse($data, $message, 201);
    }

    protected function deletedResponse(string $message = 'Berhasil dihapus') {
        return response()->json([
            'message' => $message,
        ]);
    }

    protected function errorResponse(string $message = 'Terjadi kesalahan', int $code = 400) {
        return response()->json([
            'message' => $message
        ], $code);
    }

    protected function serverErrorResponse(string $message = 'Terjadi kesalah pada server', int $code = 500) {
        return response()->json([
            "message" => $message,
        ], $code);
    }
}
