<?php

namespace App\Http\Controllers;

abstract class Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
