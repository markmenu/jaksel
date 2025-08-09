<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kegiatan;
use App\Models\User;
use App\Notifications\ProgressUpdateNotification;
use Illuminate\Support\Facades\Gate;

class AnggotaTimController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Mengambil semua kegiatan yang terhubung dengan user ini
        // dan hanya ambil yang merupakan kegiatan utama (parent_id adalah null)
        $kegiatans = $user->kegiatans()
            ->wherePivot('role_in_kegiatan', 'anggota')
            ->whereNull('parent_id')
            ->paginate(10);

        return view('anggota-tim.kegiatan_index', compact('kegiatans'));
    }

    public function show(Kegiatan $kegiatan)
    {
        // Pengecekan otorisasi menggunakan Policy
        Gate::authorize('view', $kegiatan);

        // Perbaikan: Muat relasi anak-anak rekursif DARI kegiatan utama.
        $kegiatan->load('childrenRecursive');

        // Mengirim kegiatan itu sendiri ke view. View akan menampilkan
        // hierarki dari kegiatan ini.
        return view('anggota-tim.kegiatan_show', compact('kegiatan'));
    }

    public function updateProgress(Request $request, Kegiatan $kegiatan)
    {
        // Gunakan Gate/Policy untuk otorisasi
        Gate::authorize('updateProgress', $kegiatan);

        // Memuat ulang relasi children untuk memeriksa apakah task memiliki anak
        $kegiatan->load('children');
        if ($kegiatan->children->isNotEmpty()) {
            return back()->with('error', 'Progres hanya bisa diubah pada task terkecil.');
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);
        
        $kegiatan->update([
            'progress' => $request->progress,
            'status' => $request->progress == 100 ? 'Selesai' : 'Berjalan',
        ]);
        
        // Panggil metode untuk menghitung ulang progres parent secara rekursif
        $this->recalculateParentProgress($kegiatan->parent);

        return back()->with('success', 'Progres task berhasil diperbarui.');
    }
    
    /**
     * Metode rekursif untuk menghitung ulang progres parent.
     * @param Kegiatan|null $parentTask
     */
    private function recalculateParentProgress(?Kegiatan $parentTask)
    {
        if (!$parentTask) {
            return;
        }
        
        // Muat ulang relasi children agar data terbaru
        $parentTask->load('children');
        
        // Hitung rata-rata progress dari semua anak
        $childrenProgress = $parentTask->children->avg('progress');
        
        // Update progress parent
        $parentTask->update([
            'progress' => round($childrenProgress, 2),
            'status' => $childrenProgress == 100 ? 'Selesai' : 'Berjalan',
        ]);
        
        // Panggil dirinya sendiri untuk parent di atasnya (rekursif)
        $this->recalculateParentProgress($parentTask->parent);
    }
}
