<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Tombol Aksi di Atas --}}
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">
                            &larr; Kembali
                        </a>
                        @if(auth()->user()->role === 'ketua_tim')
                        <a href="{{ route('ketua-tim.kegiatan.edit', $kegiatan) }}" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                            Edit Kegiatan Ini
                        </a>
                        @endif
                    </div>

                    {{-- Detail Kegiatan --}}
                    <h3 class="text-lg font-semibold mb-2">Informasi Kegiatan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Nama Kegiatan</p>
                            <p class="font-semibold">{{ $kegiatan->nama_kegiatan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold">{{ $kegiatan->status }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Mulai</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Selesai</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('d F Y') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Deskripsi Kegiatan</p>
                            <p>{{ $kegiatan->deskripsi ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">File Pendukung Kegiatan</p>
                            @if($kegiatan->file_pendukung)
                                <a href="{{ asset('storage/' . $kegiatan->file_pendukung) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File</a>
                            @else
                                <p>-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Progres Disetujui</p>
                            <p class="font-semibold text-2xl text-green-500">{{ $kegiatan->progress }}%</p>
                        </div>
                        @if($kegiatan->status === 'Menunggu Persetujuan' && !is_null($kegiatan->pending_progress))
                        <div class="md:col-span-2">
                             <div class="p-4 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Progres Diajukan (Menunggu Persetujuan)</p>
                                <p class="font-semibold text-2xl text-yellow-700 dark:text-yellow-400">{{ $kegiatan->pending_progress }}%</p>
                             </div>
                        </div>
                        @endif
                    </div>

                    {{-- Detail Metadata --}}
                    <h3 class="text-lg font-semibold mb-2 mt-8 border-t pt-4">Informasi Metadata</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Nama Metadata</p>
                            <p class="font-semibold">{{ $kegiatan->metadata?->nama_metadata ?? 'Data tidak ditemukan' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Keterangan Metadata</p>
                            <p>{{ $kegiatan->metadata?->keterangan ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">File Metadata</p>
                            @if($kegiatan->metadata?->file_metadata)
                                <a href="{{ asset('storage/' . $kegiatan->metadata->file_metadata) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File</a>
                            @else
                                <p>-</p>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Tim --}}
                    <h3 class="text-lg font-semibold mb-2 mt-8 border-t pt-4">Tim Kegiatan</h3>
                    <ul class="list-disc list-inside">
                        @foreach($kegiatan->users as $user)
                            <li>{{ $user->name }} - <span class="text-sm text-gray-500">({{ $user->pivot->role_in_kegiatan === 'ketua' ? 'Ketua Tim' : 'Anggota' }})</span></li>
                        @endforeach
                    </ul>

                    {{-- BAGIAN GANTT CHART --}}
                    <h3 class="text-lg font-semibold mb-4 mt-8 border-t pt-4">Jadwal Kegiatan</h3>
                    <div id="gantt-chart"></div>

                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'ketua_tim' && $kegiatan->status === 'Menunggu Persetujuan')
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-200 p-4"
                role="alert">
                <p class="font-bold">Tindakan Diperlukan</p>
                <p>Anggota tim telah mengajukan pembaruan progres untuk kegiatan ini. Silakan tinjau dan berikan
                    persetujuan.</p>

                <div class="mt-4">
                    <form action="{{ route('ketua-tim.kegiatan.handlePersetujuan', $kegiatan) }}" method="POST"
                        class="inline-block">
                        @csrf
                        <input type="hidden" name="aksi" value="setuju">
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">
                            Setujui
                        </button>
                    </form>

                    <form action="{{ route('ketua-tim.kegiatan.handlePersetujuan', $kegiatan) }}" method="POST"
                        class="inline-block ml-2">
                        @csrf
                        <input type="hidden" name="aksi" value="tolak">
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600">
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->role === 'anggota_tim')
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Update Progres Kegiatan</h3>
                    <form action="{{ route('anggota-tim.kegiatan.updateProgress', $kegiatan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="progress"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">Progress
                                    (%)</label>
                                <input type="number" name="progress" id="progress" min="0" max="100"
                                    value="{{ old('progress', $kegiatan->progress) }}"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                    required>
                            </div>
                            <div>
                                <label for="status"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">Status
                                    Kegiatan</label>
                                <select name="status" id="status"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                    <option value="Berjalan" @selected(old('status', $kegiatan->status) == 'Berjalan')>Berjalan</option>
                                    <option value="Menunggu Persetujuan" @selected(old('status', $kegiatan->status) == 'Menunggu Persetujuan')>Ajukan untuk
                                        Persetujuan</option>
                                    <option value="Selesai" @selected(old('status', $kegiatan->status) == 'Selesai')>Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                                Simpan Progres
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Menambahkan JS untuk Frappe Gantt dan inisialisasi chart --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.min.js"></script>
    <script>
        // Ambil data JSON dari controller dengan cara yang paling aman
        var tasks = @json($tasksForGantt ?? []);

        // Inisialisasi Gantt Chart
        if (tasks && tasks.length > 0) {
            var gantt = new Gantt("#gantt-chart", tasks, {
                header_height: 50,
                bar_height: 25,
                padding: 18,
                view_mode: 'Week',
                date_format: 'YYYY-MM-DD',
                custom_popup_html: function(task) {
                    return `
                        <div class="p-2">
                            <h4 class="font-bold">${task.name}</h4>
                            <p>Mulai: ${task._start.toLocaleDateString()}</p>
                            <p>Selesai: ${task._end.toLocaleDateString()}</p>
                            <p>Progres: ${task.progress}%</p>
                        </div>
                    `;
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
