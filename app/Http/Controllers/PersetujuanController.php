<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class PersetujuanController extends Controller
{
    /**
     * Menangani aksi persetujuan atau penolakan dari Ketua Tim.
     */
    public function handle(Request $request, Kegiatan $kegiatan)
    {
        // Validasi input
        $request->validate([
            'aksi' => 'required|string|in:setuju,tolak',
        ]);

        if ($request->aksi === 'setuju') {
            // Ambil nilai dari pending_progress
            $approvedProgress = $kegiatan->pending_progress;

            // Tentukan status baru berdasarkan progres yang disetujui
            $newStatus = $approvedProgress == 100 ? 'Selesai' : 'Berjalan';

            // Update kolom utama dan kosongkan kolom pending
            $kegiatan->update([
                'progress' => $approvedProgress,
                'status' => $newStatus,
                'pending_progress' => null,
            ]);
            return back()->with('success', 'Perubahan progres telah disetujui.');
        }

        if ($request->aksi === 'tolak') {
            // Kembalikan status ke 'Berjalan' dan kosongkan kolom pending
            $kegiatan->update([
                'status' => 'Berjalan',
                'pending_progress' => null,
            ]);
            return back()->with('success', 'Perubahan progres telah ditolak.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
