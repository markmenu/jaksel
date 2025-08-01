<?php

namespace App\Policies;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KegiatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kegiatan $kegiatan): bool
    {
        return $kegiatan->users->contains($user);
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
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        return $kegiatan->users()
                        ->where('user_id', $user->id)
                        ->where('role_in_kegiatan', 'ketua')
                        ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        return $this->update($user, $kegiatan);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kegiatan $kegiatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kegiatan $kegiatan): bool
    {
        return false;
    }
}
