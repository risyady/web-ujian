<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('siswa.jurusan')->latest()->paginate(10);
        return $this->successResponse($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,guru,siswa',
            'nisn' => 'required_if:role,siswa|unique:siswa,nisn',
            'jurusan_id' => 'required_if:role,siswa|exists:jurusan,id',
        ]);

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

        return $this->createdResponse($this->loadUserData($user), 'User berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->successResponse($this->loadUserData($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:admin,guru,siswa',
            'nisn' => 'required_if:role,siswa|string|unique:siswas,nisn,' . $user->id . ',user_id',
            'jurusan_id' => 'required_if:role,siswa|exists:jurusans,id'
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role ?? $user->role,
        ]);

        if ($user->role === 'siswa') {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nisn' => $request->nisn ?? optional($user->siswa)->nisn,
                    'jurusan_id' => $request->jurusan_id ?? optional($user->siswa)->jurusan_id,
                ]
            );
        }

        if ($request->role && $request->role !== 'siswa') {
            $user->siswa()->delete();
        }

        return $this->successResponse($this->loadUserData($user), 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->deletedResponse('User berhasil dihapus');
    }

    private function loadUserData(User $user): User
    {
        if ($user->role === 'siswa') {
            return $user->load('siswa.jurusan');
        }

        return $user;
    }
}
