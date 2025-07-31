<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\TaskLink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GanttController extends Controller
{
    public function getData()
    {
        // 1. Ambil semua kegiatan (tugas)
        $tasks = Kegiatan::all();

        // 2. Format data tugas sesuai kebutuhan DHTMLX
        $formattedTasks = $tasks->map(function ($task) {
            return [
                'id'        => $task->id,
                'text'      => $task->nama_kegiatan,
                'start_date'=> Carbon::parse($task->tanggal_mulai)->format('Y-m-d'),
                'duration'  => Carbon::parse($task->tanggal_mulai)->diffInDays(Carbon::parse($task->tanggal_selesai)),
                'progress'  => $task->progress / 100, // DHTMLX butuh progress dalam format 0-1
                'parent'    => $task->parent_id,
            ];
        });

        // 3. Ambil semua data dependensi (links)
        // Pastikan Anda sudah membuat model TaskLink
        if (class_exists(TaskLink::class)) {
            $links = TaskLink::all();
        } else {
            $links = [];
        }
        
        // 4. Gabungkan keduanya dalam satu response JSON
        return response()->json([
            'data'  => $formattedTasks,
            'links' => $links
        ]);
    }
}
