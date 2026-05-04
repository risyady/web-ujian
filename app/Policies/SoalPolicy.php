<?php

namespace App\Policies;

use App\Models\Soal;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SoalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Ujian $ujian): bool
    {
        return $user->isAdmin() || $ujian->guru_id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Soal $soal): bool
    {
        return $user->isAdmin() || $soal->ujian->guru_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Ujian $ujian): bool
    {
        return $ujian->guru_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Soal $soal): bool
    {
        return $soal->ujian->guru_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Soal $soal): bool
    {
        return $soal->ujian->guru_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Soal $soal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Soal $soal): bool
    {
        return false;
    }
}
