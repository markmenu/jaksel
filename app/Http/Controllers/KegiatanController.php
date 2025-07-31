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
            $query->where(function($q) use ($search) {
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
        return view('ketua-tim.kegiatan_create', compact('calonAnggota'));
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
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // <-- Tambahkan validasi ini
            'nama_metadata' => 'required|string|max:255',
            'keterangan_metadata' => 'nullable|string',
            'file_metadata' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
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

            // Proses file pendukung kegiatan (BARU)
            $kegiatanFilePath = null;
            if ($request->hasFile('file_pendukung')) {
                $kegiatanFilePath = $request->file('file_pendukung')->store('file-kegiatan', 'public');
            }

            // Buat Kegiatan dan hubungkan dengan ID metadata & file pendukung
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'metadata_id' => $metadata->id,
                'file_pendukung' => $kegiatanFilePath, // <-- Tambahkan ini
            ]);

            // Lampirkan tim ke kegiatan
            $kegiatan->users()->attach(Auth::id(), ['role_in_kegiatan' => 'ketua']);
            if ($request->has('anggota_ids')) {
                $kegiatan->users()->attach($request->anggota_ids, ['role_in_kegiatan' => 'anggota']);
            }

            DB::commit();
            return redirect()->route('ketua-tim.kegiatan.index')->with('success', 'Kegiatan baru berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail kegiatan.
     * (Kita akan isi ini nanti dengan Gantt Chart)
     */
    public function show(Kegiatan $kegiatan)
    {
        Gate::authorize('view', $kegiatan);
        $kegiatan->load('metadata', 'users');

        // Siapkan data series untuk ApexCharts
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

        return view('ketua-tim.kegiatan_show', compact('kegiatan', 'series', 'progressData'));
    }

    /**
     * Menampilkan form untuk mengedit kegiatan.
     */
    public function edit(Kegiatan $kegiatan)
    {
        Gate::authorize('update', $kegiatan);
        $calonAnggota = User::where('role', 'anggota_tim')->get();
        $anggotaTerkait = $kegiatan->users()->where('role_in_kegiatan', 'anggota')->pluck('user_id')->toArray();
        return view('ketua-tim.kegiatan_edit', compact('kegiatan', 'calonAnggota', 'anggotaTerkait'));
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

            DB::commit();
            return redirect()->route('ketua-tim.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);
            
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
