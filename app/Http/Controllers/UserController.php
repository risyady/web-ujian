<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth()->user();

        $users = $this->querySelectUser($currentUser->role);
        return $this->successResponse($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();    
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        
        return $this->createdResponse($this->loadJurusanSiswa($user), 'User berhasil dibuat');
    }

    public function bulkStore(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new UsersImport;
        Excel::import($import, $request->file('file'));

        return $this->successResponse([
            'failures' => $import->failures(),
            'errors' => $import->errors(),
        ], 'Import selesai');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->successResponse($this->loadJurusanSiswa($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $data['password'] ? Hash::make($data['password']) :$data['password'];

        $user->update($data);

        return $this->successResponse($this->loadJurusanSiswa($user), 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->deletedResponse('User berhasil dihapus');
    }

    private function querySelectUser($role) {
        return User::with('jurusan')->when($role !== 'superadmin', function ($query) {
            $query->where('role', '!=', 'admin')->where('role', '!=', 'superadmin');
        })->latest()->paginate(10);
    }
    
    private function loadJurusanSiswa(User $user): User
    {
        if ($user->role === 'siswa') {
            return $user->load('jurusan');
        }

        return $user;
    }
}
