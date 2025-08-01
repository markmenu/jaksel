<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\TaskLink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GanttController extends Controller
{
    public function data()
    {
        $kegiatans = Kegiatan::all();

        // Transformasi data agar sesuai format DHTMLX
        $tasks = $kegiatans->map(function ($kegiatan) {
            return [
                'id'       => $kegiatan->id,
                'text'     => $kegiatan->nama_kegiatan,
                'start_date' => $kegiatan->tanggal_mulai->format('d-m-Y'),
                'duration' => $kegiatan->tanggal_mulai->diffInDays($kegiatan->tanggal_selesai),
                'progress' => $kegiatan->progress / 100, // DHTMLX butuh progress dalam format 0 sampai 1
                'parent'   => $kegiatan->parent_id,
                // 'open'     => true, // Uncomment ini jika ingin semua cabang terbuka otomatis
            ];
        });

        // Endpoint untuk data dependensi/link
        $links = TaskLink::all()->map(function ($link) {
            return [
                'id'     => $link->id,
                'source' => $link->source,
                'target' => $link->target,
                'type'   => '0' // Tipe 0: Finish to Start
            ];
        });

        return response()->json([
            "data"  => $tasks,
            "links" => $links
        ]);
    }

    public function kegiatanData(Request $request, $id)
    {
        // 1. Ambil kegiatan utama (induk)
        $kegiatanUtama = Kegiatan::findOrFail($id);

        // 2. Ambil semua sub-kegiatan yang memiliki parent_id ini
        $subKegiatan = Kegiatan::where('parent_id', $id)->get();

        // 3. Gabungkan keduanya
        $kegiatans = $subKegiatan->push($kegiatanUtama);

        // Transformasi data agar sesuai format DHTMLX
        $tasks = $kegiatans->map(function ($kegiatan) {
            return [
                'id'       => $kegiatan->id,
                'text'     => $kegiatan->nama_kegiatan,
                'start_date' => $kegiatan->tanggal_mulai->format('d-m-Y'),
                'duration' => $kegiatan->tanggal_mulai->diffInDays($kegiatan->tanggal_selesai),
                'progress' => $kegiatan->progress / 100,
                'parent'   => $kegiatan->parent_id,
                'open'     => true, // Buka semua cabang secara otomatis
            ];
        });

        // Ambil dependensi yang relevan
        $kegiatanIds = $kegiatans->pluck('id');
        $links = TaskLink::whereIn('source_task_id', $kegiatanIds) // <--- UBAH INI
            ->orWhereIn('target_task_id', $kegiatanIds) // <--- UBAH INI
            ->get()
            ->map(function ($link) {
                return [
                    'id'     => $link->id,
                    'source' => $link->source_task_id, // <--- UBAH INI
                    'target' => $link->target_task_id, // <--- UBAH INI
                    'type'   => '0'
                ];
            });

        return response()->json([
            "data"  => $tasks,
            "links" => $links
        ]);
    }
}
