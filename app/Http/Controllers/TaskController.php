<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Menampilkan form tambah task mandiri terkait kegiatan.
     */
    public function create()
    {
        // Ambil kegiatan induk untuk memilih parent_id
        $kegiatans = Kegiatan::whereNull('parent_id')->orderBy('nama_kegiatan')->get();
        return view('task_create', compact('kegiatans'));
    }

    /**
     * Simpan task baru ke db.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'progress' => 'nullable|numeric|between:0,100',
            'parent_id' => 'required|exists:kegiatans,id',
        ]);

        $parentKegiatan = Kegiatan::findOrFail($request->parent_id);

        Kegiatan::create([
            'nama_kegiatan'   => $request->nama_kegiatan,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'progress'        => $request->progress ?? 0,
            'parent_id'       => $request->parent_id,
            'metadata_id'     => $parentKegiatan->metadata_id,
        ]);

        return redirect()->route('task.create')->with('success', 'Task baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit task.
     */
    public function edit(Kegiatan $task)
    {
        Gate::authorize('update', $task);

        // Ambil semua task yang terkait dengan kegiatan utama task ini
        $allRelatedTasks = Kegiatan::where('metadata_id', $task->metadata_id)->get();
        $kegiatans = $allRelatedTasks->where('id', '!=', $task->id);

        return view('task_edit', compact('task', 'kegiatans'));
    }

    /**
     * Update data task.
     */
    public function update(Request $request, Kegiatan $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'progress' => 'nullable|numeric|between:0,100',
            'parent_id' => 'nullable|exists:kegiatans,id',
        ]);

        $task->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'progress' => $request->progress ?? 0,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('task.edit', $task)->with('success', 'Task berhasil diperbarui.');
    }

    /**
     * Hapus task.
     */
    public function destroy(Kegiatan $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('task.create')->with('success', 'Task berhasil dihapus.');
    }
}
