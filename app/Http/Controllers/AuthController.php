<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,guru,siswa',
            'nisn' => 'required_if:role,siswa|string|unique:siswas,nisn,',
            'jurusan_id' => 'required_if:role,siswa|exists:jurusans,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if ($request->role === 'siswa') {
                Siswa::create([
                    'user_id' => $user->id,
                    'nisn' => $request->nisn,
                    'jurusan_id' => $request->jurusan_id,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->createdResponse([
                'user' => $user,
                'token' => $token,
            ], 'Registrasi berhasil');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

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
}
