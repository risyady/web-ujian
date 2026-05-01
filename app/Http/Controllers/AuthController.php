<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthProfileRequest;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Email atau password salah', 401);
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Login berhasil');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }

    }

    public function logout(Request $request) {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->deletedResponse('Logout berhasil');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function me(Request $request) {
        try {
            $user = $request->user()->load('jurusan');

            return $this->successResponse($user);

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function editProfile(AuthProfileRequest $request) {
        $user = auth()->user();
        $this->authorize('editProfile', $user);
        
        $data = $request->validated();

        $user->update($data);

        return $this->successResponse($user->fresh(), 'Profile berhasil diperbarui');
    }
}
