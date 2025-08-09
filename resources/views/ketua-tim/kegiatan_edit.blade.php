<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ========================================================== --}}
                    {{-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN ERROR VALIDASI --}}
                    {{-- ========================================================== --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg" role="alert">
                            <p class="font-bold">Terjadi kesalahan validasi:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ketua-tim.kegiatan.update', $kegiatan) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- BAGIAN INFO KEGIATAN --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Kegiatan</h3>

                        <div class="mb-4">
                            <label for="nama_kegiatan" class="block font-medium text-sm text-gray-700">Nama
                                Kegiatan</label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block font-medium text-sm text-gray-700">Deskripsi
                                Kegiatan</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tanggal_mulai" class="block font-medium text-sm text-gray-700">Tanggal
                                    Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                    value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai) }}" required>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block font-medium text-sm text-gray-700">Tanggal
                                    Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                    value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="anggota_ids" class="block font-medium text-sm text-gray-700">Pilih Anggota
                                Tim</label>
                            <select name="anggota_ids[]" id="anggota_ids" multiple
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                @foreach ($calonAnggota as $anggota)
                                    <option value="{{ $anggota->id }}" @selected(in_array($anggota->id, old('anggota_ids', $anggotaTerkait)))>
                                        {{ $anggota->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="file_pendukung" class="block font-medium text-sm text-gray-700">File Pendukung
                                Kegiatan</label>
                            <input type="file" name="file_pendukung" id="file_pendukung" class="block mt-1 w-full">
                            @if ($kegiatan->file_pendukung)
                                <p class="text-sm text-gray-500 mt-1">File saat ini: <a
                                        href="{{ asset('storage/' . $kegiatan->file_pendukung) }}" target="_blank"
                                        class="text-blue-500 hover:underline">Lihat File</a>.</p>
                            @endif
                        </div>

                        {{-- Bagian input task/subtask dinamis --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-8">Task dan Subtask</h3>

                        <div id="tasks_container">
                            @foreach ($tasks as $index => $task)
                                <div class="task_item mb-2">
                                    <input type="hidden" name="tasks[{{ $index }}][id]"
                                        value="{{ $task->id }}">
                                    <input type="text" name="tasks[{{ $index }}][nama_task]"
                                        value="{{ old("tasks.$index.nama_task", $task->nama_kegiatan) }}" required
                                        class="border rounded px-2 py-1 mr-2 mb-1">
                                    <input type="date" name="tasks[{{ $index }}][tanggal_mulai]"
                                        value="{{ old("tasks.$index.tanggal_mulai", $task->tanggal_mulai->format('Y-m-d')) }}"
                                        required class="border rounded px-2 py-1 mr-2 mb-1">
                                    <input type="date" name="tasks[{{ $index }}][tanggal_selesai]"
                                        value="{{ old("tasks.$index.tanggal_selesai", $task->tanggal_selesai->format('Y-m-d')) }}"
                                        required class="border rounded px-2 py-1 mr-2 mb-1">
                                    <input type="number" name="tasks[{{ $index }}][progress]"
                                        value="{{ old("tasks.$index.progress", $task->progress) }}" min="0"
                                        max="100" class="border rounded px-2 py-1 w-20 mr-2 mb-1">

                                    <label for="tasks[{{ $index }}][parent_id]"
                                        class="block font-medium text-sm text-gray-700">
                                        Parent Task (Opsional)</label>
                                    <select name="tasks[{{ $index }}][parent_id]"
                                        id="tasks[{{ $index }}][parent_id]"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="">Pilih Task Induk</option>
                                        @foreach ($parentTasks as $parentTask)
                                            @if ($parentTask->id != $task->id)
                                                <option value="{{ $parentTask->id }}" @selected(old("tasks.$index.parent_id", $task->parent_id) == $parentTask->id)>
                                                    {{ $parentTask->nama_kegiatan }}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                    <button type="button" onclick="removeTask(this)"
                                        class="bg-red-600 text-white px-2 rounded mb-1">Hapus</button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" onclick="addTask()"
                            class="px-3 py-1 bg-blue-600 text-white rounded">Tambah Task</button>

                        <script>
                            // taskIndex akan mengambil jumlah task yang sudah ada di halaman edit, atau 1 di halaman create.
                            let taskIndex =
                                @if (isset($tasks))
                                    {{ $tasks->count() }}
                                @else
                                    1
                                @endif ;

                            function addTask() {
                                const container = document.getElementById('tasks_container');
                                const div = document.createElement('div');
                                div.className = 'task_item mb-2';

                                // Buat opsi parent task dari variabel PHP.
                                let parentOptions = `<option value="">-- Tidak Ada (Task Utama) --</option>`;
                                @if (isset($parentTasks))
                                    @foreach ($parentTasks as $parentTask)
                                        parentOptions += `<option value="{{ $parentTask->id }}">{{ $parentTask->nama_kegiatan }}</option>`;
                                    @endforeach
                                @elseif (isset($existingTasks))
                                    @foreach ($existingTasks as $parentTask)
                                        parentOptions += `<option value="{{ $parentTask->id }}">{{ $parentTask->nama_kegiatan }}</option>`;
                                    @endforeach
                                @endif

                                div.innerHTML = `
            <input type="hidden" name="tasks[${taskIndex}][id]" value="">
            <input type="text" name="tasks[${taskIndex}][nama_task]" placeholder="Nama Task" required class="border rounded px-2 py-1 mr-2 mb-1">
            <input type="date" name="tasks[${taskIndex}][tanggal_mulai]" required class="border rounded px-2 py-1 mr-2 mb-1">
            <input type="date" name="tasks[${taskIndex}][tanggal_selesai]" required class="border rounded px-2 py-1 mr-2 mb-1">
            <input type="number" name="tasks[${taskIndex}][progress]" min="0" max="100" value="0" class="border rounded px-2 py-1 w-20 mr-2 mb-1">
            <select name="tasks[${taskIndex}][parent_id]" class="border rounded px-2 py-1 mr-2 mb-1">${parentOptions}</select>
            <button type="button" onclick="removeTask(this)" class="bg-red-600 text-white px-2 rounded mb-1">Hapus</button>
        `;
                                container.appendChild(div);
                                taskIndex++;
                            }

                            function removeTask(button) {
                                button.parentElement.remove();
                            }
                        </script>


                        {{-- BAGIAN INFO METADATA --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-8">Informasi Metadata</h3>

                        <div class="mb-4">
                            <label for="nama_metadata" class="block font-medium text-sm text-gray-700">Nama
                                Metadata</label>
                            <input type="text" name="nama_metadata" id="nama_metadata"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                value="{{ old('nama_metadata', $kegiatan->metadata->nama_metadata) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan_metadata"
                                class="block font-medium text-sm text-gray-700">Keterangan Metadata</label>
                            <textarea name="keterangan_metadata" id="keterangan_metadata" rows="4"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('keterangan_metadata', $kegiatan->metadata->keterangan) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_metadata" class="block font-medium text-sm text-gray-700">File
                                Metadata</label>
                            <input type="file" name="file_metadata" id="file_metadata" class="block mt-1 w-full">
                            @if ($kegiatan->metadata->file_metadata)
                                <p class="text-sm text-gray-500 mt-1">File saat ini: <a
                                        href="{{ asset('storage/' . $kegiatan->metadata->file_metadata) }}"
                                        target="_blank" class="text-blue-500 hover:underline">Lihat File</a>.</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('ketua-tim.kegiatan.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
