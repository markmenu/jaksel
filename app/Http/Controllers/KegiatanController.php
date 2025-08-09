<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use App\Models\Metadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class KegiatanController extends Controller
{
    /**
     * Menampilkan daftar kegiatan yang dikelola oleh Ketua Tim.
     */
    public function index(Request $request)
    {
        $ketuaTimId = Auth::id();
        $search = $request->input('search');

        // Ambil parameter untuk sortir dari request
        $sortColumn = $request->input('sort', 'nama_kegiatan'); // Default sortir berdasarkan nama
        $sortDirection = $request->input('direction', 'asc'); // Default arah menaik

        // Validasi kolom yang diizinkan untuk disortir
        $allowedSortColumns = ['nama_kegiatan', 'tanggal_mulai', 'tanggal_selesai', 'status'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'nama_kegiatan';
        }

        $query = Kegiatan::whereHas('users', function ($query) use ($ketuaTimId) {
            $query->where('user_id', $ketuaTimId)->where('role_in_kegiatan', 'ketua');
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Terapkan logika orderBy ke query
        $query->orderBy($sortColumn, $sortDirection);

        $kegiatans = $query->paginate(10);

        return view('ketua-tim.kegiatan_index', compact('kegiatans', 'search', 'sortColumn', 'sortDirection'));
    }

    /**
     * Menampilkan form untuk membuat kegiatan baru.
     */
    public function create()
    {
        // Mengambil semua user yang berpotensi menjadi anggota tim
        $calonAnggota = User::where('role', 'anggota_tim')->get();
        // Mengambil kegiatan utama yang sudah ada untuk opsi parent_id
        $existingTasks = Kegiatan::whereNull('parent_id')->orderBy('nama_kegiatan')->get();
        return view('ketua-tim.kegiatan_create', compact('calonAnggota', 'existingTasks'));
    }

    /**
     * Menyimpan kegiatan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggota_ids' => 'nullable|array',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'nama_metadata' => 'required|string|max:255',
            'keterangan_metadata' => 'nullable|string',
            'file_metadata' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

            'tasks' => 'nullable|array',
            'tasks.*.nama_task' => 'required_with:tasks|string|max:255',
            'tasks.*.tanggal_mulai' => 'required_with:tasks|date',
            'tasks.*.tanggal_selesai' => 'required_with:tasks|date|after_or_equal:tasks.*.tanggal_mulai',
            'tasks.*.progress' => 'nullable|numeric|between:0,100',
            'tasks.*.parent_id' => 'nullable|exists:kegiatans,id',
        ]);

        DB::beginTransaction();
        try {
            // Proses file metadata
            $metadataFilePath = null;
            if ($request->hasFile('file_metadata')) {
                $metadataFilePath = $request->file('file_metadata')->store('file-metadata', 'public');
            }
            $metadata = Metadata::create([
                'nama_metadata' => $request->nama_metadata,
                'keterangan' => $request->keterangan_metadata,
                'file_metadata' => $metadataFilePath,
            ]);

            // Proses file pendukung
            $kegiatanFilePath = null;
            if ($request->hasFile('file_pendukung')) {
                $kegiatanFilePath = $request->file('file_pendukung')->store('file-kegiatan', 'public');
            }

            // Buat Kegiatan induk
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'metadata_id' => $metadata->id, // metadata_id hanya untuk kegiatan induk
                'file_pendukung' => $kegiatanFilePath,
            ]);

            // Lampirkan ketua tim dan anggota tim
            $kegiatan->users()->attach(Auth::id(), ['role_in_kegiatan' => 'ketua']);
            if ($request->has('anggota_ids')) {
                $kegiatan->users()->attach($request->anggota_ids, ['role_in_kegiatan' => 'anggota']);
            }

            // Simpan task anak (jika ada)
            if ($request->filled('tasks')) {
                foreach ($request->tasks as $taskInput) {
                    $kegiatan->children()->create([
                        'nama_kegiatan' => $taskInput['nama_task'],
                        'tanggal_mulai' => $taskInput['tanggal_mulai'],
                        'tanggal_selesai' => $taskInput['tanggal_selesai'],
                        'progress' => $taskInput['progress'] ?? 0,
                        'metadata_id' => $kegiatan->metadata_id,
                        'parent_id' => $taskInput['parent_id'] ?? $kegiatan->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('ketua-tim.kegiatan.index')->with('success', 'Kegiatan baru beserta task berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail kegiatan.
     */
    public function show(Kegiatan $kegiatan)
    {
        Gate::authorize('view', $kegiatan);
        $kegiatan->load('metadata', 'users');

        // Perbaikan: Ambil hierarki task secara rekursif
        $tasks = Kegiatan::where('parent_id', $kegiatan->id)
            ->with('childrenRecursive')
            ->get();

        // Siapkan data series untuk ApexCharts (jika masih digunakan di view ini)
        $series = [
            [
                'x' => $kegiatan->nama_kegiatan,
                'y' => [
                    Carbon::parse($kegiatan->tanggal_mulai)->getTimestamp() * 1000,
                    Carbon::parse($kegiatan->tanggal_selesai)->getTimestamp() * 1000
                ]
            ]
        ];

        // Siapkan data progress
        $progressData = [
            $kegiatan->nama_kegiatan => $kegiatan->progress
        ];

        // Perbaikan: Kirimkan variabel $tasks ke view
        return view('ketua-tim.kegiatan_show', compact('kegiatan', 'series', 'progressData', 'tasks'));
    }

    /**
     * Menampilkan form untuk mengedit kegiatan.
     */
    public function edit(Kegiatan $kegiatan)
    {
        Gate::authorize('update', $kegiatan);
        $kegiatan->load('metadata', 'users');

        $calonAnggota = User::where('role', 'anggota_tim')->get();
        $anggotaTerkait = $kegiatan->users()->where('role_in_kegiatan', 'anggota')->pluck('user_id')->toArray();
        $tasks = $kegiatan->children()->with('childrenRecursive')->get();

        // Ambil semua task yang terkait dengan kegiatan utama untuk opsi Parent Task
        $parentTasks = Kegiatan::where('metadata_id', $kegiatan->metadata_id)
            ->where('id', '!=', $kegiatan->id)
            ->get();

        return view('ketua-tim.kegiatan_edit', compact('kegiatan', 'calonAnggota', 'anggotaTerkait', 'tasks', 'parentTasks'));
    }

    /**
     * Memperbarui data kegiatan.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        Gate::authorize('update', $kegiatan);
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'anggota_ids' => 'nullable|array',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'nama_metadata' => 'required|string|max:255',
            'keterangan_metadata' => 'nullable|string',
            'file_metadata' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

            // Validasi untuk task array
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|exists:kegiatans,id',
            'tasks.*.nama_task' => 'required_with:tasks|string|max:255',
            'tasks.*.tanggal_mulai' => 'required_with:tasks|date',
            'tasks.*.tanggal_selesai' => 'required_with:tasks|date|after_or_equal:tasks.*.tanggal_mulai',
            'tasks.*.progress' => 'nullable|numeric|between:0,100',
            'tasks.*.parent_id' => 'nullable|exists:kegiatans,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Metadata
            $metadata = $kegiatan->metadata;
            $metadataData = [
                'nama_metadata' => $request->nama_metadata,
                'keterangan' => $request->keterangan_metadata,
            ];
            if ($request->hasFile('file_metadata')) {
                if ($metadata->file_metadata) Storage::disk('public')->delete($metadata->file_metadata);
                $metadataData['file_metadata'] = $request->file('file_metadata')->store('file-metadata', 'public');
            }
            $metadata->update($metadataData);

            // 2. Update Kegiatan
            $kegiatanData = [
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ];
            if ($request->hasFile('file_pendukung')) {
                if ($kegiatan->file_pendukung) Storage::disk('public')->delete($kegiatan->file_pendukung);
                $kegiatanData['file_pendukung'] = $request->file('file_pendukung')->store('file-kegiatan', 'public');
            }
            $kegiatan->update($kegiatanData);

            // 3. Sinkronisasi Tim
            $anggotaIds = $request->input('anggota_ids', []);
            $syncData = [];
            // Tambahkan ketua tim ke data sinkronisasi
            $syncData[Auth::id()] = ['role_in_kegiatan' => 'ketua'];
            // Tambahkan anggota tim
            foreach ($anggotaIds as $id) {
                $syncData[$id] = ['role_in_kegiatan' => 'anggota'];
            }
            $kegiatan->users()->sync($syncData);

            // Handling update/insert/delete task
            $inputTasks = $request->input('tasks', []);

            // Ambil ID task lama sebagai referensi
            $existingTaskIds = $kegiatan->children()->pluck('id')->toArray();
            $inputTaskIds = [];

            foreach ($inputTasks as $taskData) {
                if (!empty($taskData['id']) && in_array($taskData['id'], $existingTaskIds)) {
                    // Update task yang sudah ada
                    $task = Kegiatan::find($taskData['id']);
                    $task->update([
                        'nama_kegiatan' => $taskData['nama_task'],
                        'tanggal_mulai' => $taskData['tanggal_mulai'],
                        'tanggal_selesai' => $taskData['tanggal_selesai'],
                        'progress' => $taskData['progress'] ?? 0,
                        'parent_id' => !empty($taskData['parent_id']) ? $taskData['parent_id'] : null,
                    ]);
                    $inputTaskIds[] = $task->id;
                } else {
                    // Buat task baru
                    $newTask = $kegiatan->children()->create([
                        'nama_kegiatan' => $taskData['nama_task'],
                        'tanggal_mulai' => $taskData['tanggal_mulai'],
                        'tanggal_selesai' => $taskData['tanggal_selesai'],
                        'progress' => $taskData['progress'] ?? 0,
                        'parent_id' => !empty($taskData['parent_id']) ? $taskData['parent_id'] : null,
                        'metadata_id' => $kegiatan->metadata_id,
                    ]);
                    $inputTaskIds[] = $newTask->id;
                }
            }

            // Hapus task yang sudah ada tapi tidak ada di input (task dihapus oleh user)
            $tasksToDelete = array_diff($existingTaskIds, $inputTaskIds);
            if (!empty($tasksToDelete)) {
                Kegiatan::destroy($tasksToDelete);
            }

            DB::commit();
            return redirect()->route('ketua-tim.kegiatan.index')->with('success', 'Kegiatan dan task berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus kegiatan.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        Gate::authorize('delete', $kegiatan);
        DB::beginTransaction();
        try {
            $metadata = $kegiatan->metadata;

            // Hapus file pendukung dari storage jika ada
            if ($kegiatan->file_pendukung) {
                Storage::disk('public')->delete($kegiatan->file_pendukung);
            }
            // Hapus file metadata dari storage jika ada
            if ($metadata && $metadata->file_metadata) {
                Storage::disk('public')->delete($metadata->file_metadata);
            }

            // Hapus record kegiatan. Relasi onDelete('cascade') di database
            // akan secara otomatis menghapus record metadata yang terkait.
            $kegiatan->delete();

            DB::commit();
            return redirect()->route('ketua-tim.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }
}
