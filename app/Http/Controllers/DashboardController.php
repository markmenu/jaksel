<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Metadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboardPublik()
    {
        // Ambil semua data kegiatan dan metadata
        $kegiatans = Kegiatan::orderBy('tanggal_mulai', 'desc')->get();
        $metadata = Metadata::orderBy('nama_metadata', 'asc')->get();

        return view('welcome', compact('kegiatans', 'metadata'));
    }

    public function showKegiatanPublik(Kegiatan $kegiatan)
    {
        // Eager load relasi metadata untuk efisiensi
        $kegiatan->load('metadata');
        return view('kegiatan_publik_show', compact('kegiatan'));
    }
    
    public function dashboardB()
    {
        return view('superadmin.dashboard');
    }

    public function dashboardC()
    {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'ketua_tim') {
            // Data untuk Ketua Tim
            $kegiatans = Kegiatan::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('role_in_kegiatan', 'ketua');
            });

            $data['totalKegiatan'] = $kegiatans->count();
            $data['kegiatanBerjalan'] = $kegiatans->clone()->where('status', 'Berjalan')->count();
            $data['menungguPersetujuan'] = $kegiatans->clone()->where('status', 'Menunggu Persetujuan')->count();
            $data['notifikasiTerbaru'] = $user->unreadNotifications()->take(5)->get();

        } elseif ($user->role === 'anggota_tim') {
            // Data untuk Anggota Tim
            $kegiatans = $user->kegiatans()->wherePivot('role_in_kegiatan', 'anggota');

            $data['totalTugas'] = $kegiatans->count();
            $data['tugasBerjalan'] = $kegiatans->clone()->where('status', 'Berjalan')->count();
            $data['tugasSelesai'] = $kegiatans->clone()->where('status', 'Selesai')->count();
            $data['deadlineTerdekat'] = $kegiatans->clone()->where('status', '!=', 'Selesai')->orderBy('tanggal_selesai', 'asc')->take(5)->get();
        }

        return view('tim.dashboard', $data);
    }

    public function dashboardKepala()
    {
        $semuaKegiatan = Kegiatan::all();

        // Siapkan data series untuk ApexCharts
        $series = $semuaKegiatan->map(function ($kegiatan) {
            return [
                'x' => $kegiatan->nama_kegiatan,
                'y' => [
                    // Konversi tanggal ke timestamp milidetik untuk JavaScript
                    Carbon::parse($kegiatan->tanggal_mulai)->getTimestamp() * 1000,
                    Carbon::parse($kegiatan->tanggal_selesai)->getTimestamp() * 1000
                ],
                // Beri warna berbeda jika sudah selesai
                'fillColor' => $kegiatan->progress == 100 ? '#4CAF50' : '#4f46e5', 
            ];
        });
        
        // Siapkan data progress terpisah untuk label
        $progressData = $semuaKegiatan->pluck('progress', 'nama_kegiatan');

        return view('kepala.dashboard', compact('series', 'progressData'));
    }
}
