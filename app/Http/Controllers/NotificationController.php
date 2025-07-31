<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menandai notifikasi sebagai sudah dibaca dan mengarahkan ke halaman terkait.
     */
    public function index()
    {
        // Ambil semua notifikasi (dibaca & belum dibaca), paginasi per 15
        $notifications = Auth::user()->notifications()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }
    
     public function markAsRead($notificationId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Cari notifikasi berdasarkan ID, jika tidak ketemu akan error 404
        $notification = $user->notifications()->findOrFail($notificationId);

        // Tandai sebagai sudah dibaca
        $notification->markAsRead();

        // Ambil ID kegiatan dari data notifikasi
        $kegiatanId = $notification->data['kegiatan_id'];

        // Arahkan ke halaman detail kegiatan yang sesuai
        return redirect()->route('ketua-tim.kegiatan.show', $kegiatanId);
    }
}
