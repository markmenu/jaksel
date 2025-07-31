<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kegiatan;
use App\Models\User;
use App\Notifications\ProgressUpdateNotification;

class AnggotaTimController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Mengambil semua kegiatan yang terhubung dengan user ini
        // di mana perannya adalah 'anggota'
        $kegiatans = $user->kegiatans()
                          ->wherePivot('role_in_kegiatan', 'anggota')
                          ->paginate(10);

        return view('anggota-tim.kegiatan_index', compact('kegiatans'));
    }

    public function updateProgress(Request $request, Kegiatan $kegiatan)
    {
        if (!$kegiatan->users->contains(Auth::id())) {
            abort(403, 'AKSES DITOLAK.');
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:Berjalan,Selesai,Menunggu Persetujuan',
        ]);

        // Jika status adalah "Menunggu Persetujuan", simpan ke kolom pending
        if ($request->status === 'Menunggu Persetujuan') {
            $kegiatan->update([
                'pending_progress' => $request->progress,
                'status' => 'Menunggu Persetujuan',
            ]);

            // Kirim notifikasi
            $ketuaTim = $kegiatan->users()->where('role_in_kegiatan', 'ketua')->first();
            if ($ketuaTim) {
                $ketuaTim->notify(new ProgressUpdateNotification($kegiatan, Auth::user()));
            }
            return back()->with('success', 'Progres kegiatan berhasil diajukan untuk persetujuan.');
        } 
        
        // Jika hanya mengubah status biasa (bukan pengajuan)
        $kegiatan->update([
            'progress' => $request->progress,
            'status' => $request->status,
        ]);
        return back()->with('success', 'Progres kegiatan berhasil diperbarui.');
    }
}
