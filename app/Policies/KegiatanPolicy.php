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
        // Temukan metadata_id dari kegiatan yang ingin dilihat
        $metadataId = $kegiatan->metadata_id;
        
        // Temukan kegiatan utama yang terkait dengan metadata ini
        $kegiatanUtama = Kegiatan::where('metadata_id', $metadataId)
                                  ->whereNull('parent_id')
                                  ->first();

        if ($kegiatanUtama) {
            // Periksa apakah pengguna adalah anggota dari kegiatan utama tersebut.
            return $kegiatanUtama->users()->where('user_id', $user->id)->exists();
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
     * Determine whether the user can update progress.
     */
    public function updateProgress(User $user, Kegiatan $kegiatan): bool
    {
        // Periksa apakah user yang sedang login adalah anggota tim atau ketua tim
        // dari kegiatan yang bersangkutan.
        return $kegiatan->users()->where('user_id', $user->id)->exists();
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
