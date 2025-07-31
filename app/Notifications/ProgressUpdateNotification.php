<?php

namespace App\Notifications;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProgressUpdateNotification extends Notification
{
    use Queueable;

    protected $kegiatan;
    protected $anggota;

    /**
     * Create a new notification instance.
     */
    public function __construct(Kegiatan $kegiatan, User $anggota)
    {
        $this->kegiatan = $kegiatan;
        $this->anggota = $anggota;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Kita akan menyimpan notifikasi di database
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Ini adalah data yang akan disimpan di kolom 'data' pada tabel notifikasi
        return [
            'kegiatan_id' => $this->kegiatan->id,
            'kegiatan_name' => $this->kegiatan->nama_kegiatan,
            'anggota_name' => $this->anggota->name,
            'message' => "{$this->anggota->name} telah memperbarui progres kegiatan '{$this->kegiatan->nama_kegiatan}' dan membutuhkan persetujuan Anda.",
        ];
    }
}
