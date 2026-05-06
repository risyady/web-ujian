<?php

namespace App\Policies;

use App\Models\SiswaUjian;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiswaUjianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SiswaUjian $siswaUjian): bool
    {
        if ($user->isAdmin()) return true;

        if ($user->isGuru()) {
            return $siswaUjian->ujian->guru_id === $user->id;
        }

        if ($user->isSiswa()) {
            return $siswaUjian->siswa_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiswaUjian $siswaUjian): bool
    {
        if ($user->isAdmin()) return true;

        if ($user->isGuru()) {
            return $siswaUjian->ujian->guru_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiswaUjian $siswaUjian): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiswaUjian $siswaUjian): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiswaUjian $siswaUjian): bool
    {
        return false;
    }
}
