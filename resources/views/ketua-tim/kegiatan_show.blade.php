<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- KOTAK UNTUK DETAIL INFORMASI --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Aksi di Atas --}}
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">
                            &larr; Kembali
                        </a>
                        @if (auth()->user()->role === 'ketua_tim')
                            <a href="{{ route('ketua-tim.kegiatan.edit', $kegiatan) }}"
                                class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
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
                            <p class="font-semibold">{{ $kegiatan->tanggal_mulai->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Selesai</p>
                            <p class="font-semibold">{{ $kegiatan->tanggal_selesai->format('d F Y') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Deskripsi Kegiatan</p>
                            <p>{{ $kegiatan->deskripsi ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Progres Disetujui</p>
                            <p class="font-semibold text-2xl text-green-500">{{ $kegiatan->progress }}%</p>
                        </div>
                    </div>

                    {{-- Detail Metadata --}}
                    <h3 class="text-lg font-semibold mb-2 mt-8 border-t pt-4">Informasi Metadata</h3>
                    <div>
                        <p class="text-sm text-gray-500">Nama Metadata</p>
                        <p class="font-semibold">
                            {{ $kegiatan->metadata?->nama_metadata ?? 'Data tidak ditemukan' }}</p>
                    </div>

                     {{-- Daftar Tim --}}
                    <h3 class="text-lg font-semibold mb-2 mt-8 border-t pt-4">Tim Kegiatan</h3>
                    <ul class="list-disc list-inside">
                        @foreach ($kegiatan->users as $user)
                            <li>{{ $user->name }} - <span
                                    class="text-sm text-gray-500">({{ $user->pivot->role_in_kegiatan === 'ketua' ? 'Ketua Tim' : 'Anggota' }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- KOTAK BARU KHUSUS UNTUK GANTT CHART --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                 <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Timeline Kegiatan</h3>
                    <div id="gantt_here" style='width:100%; height:400px;'></div>
                 </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

        <style>
            /* CSS untuk fitur pengingat deadline */
            .gantt_task_line.overdue {
                background-color: #d9534f; /* Merah untuk lewat deadline */
                border-color: #d43f3a;
            }
            .gantt_task_line.near_deadline {
                background-color: #f0ad4e; /* Oranye untuk mendekati deadline */
                border-color: #eea236;
            }
            .gantt_task_line.overdue .gantt_task_progress,
            .gantt_task_line.near_deadline .gantt_task_progress {
                background-color: #5cb85c; /* Warna progress bar tetap hijau */
            }
        </style>

        <script>
            // Konfigurasi Gantt Chart
            gantt.config.columns = [
                {name: "text", label: "Nama Kegiatan", tree: true, width: '*', resize: true},
                {name: "start_date", label: "Tanggal Mulai", align: "center", width: 120},
                {name: "duration", label: "Durasi (Hari)", align: "center", width: 80},
                {name: "add", width: 44}
            ];

            gantt.config.show_top_info = false;
            gantt.config.show_task_count = false;

            gantt.config.scales = [
                {unit: "month", step: 1, format: "%F, %Y"},
                {unit: "day", step: 1, format: "%d"}
            ];
            
            gantt.config.date_format = "%Y-%m-%d %H:%i:%s";

            // Fitur Pengingat Deadline
            gantt.templates.task_class = function(start, end, task){
                var today = new Date();
                var deadline = gantt.date.parseDate(task.end_date, "xml_date");

                if (!deadline) return "";

                var threeDaysBefore = new Date(deadline);
                threeDaysBefore.setDate(deadline.getDate() - 3);

                if(task.progress < 1 && today > deadline){
                    return "overdue";
                }
                if(task.progress < 1 && today >= threeDaysBefore && today <= deadline){
                    return "near_deadline";
                }
                return "";
            };

            // Inisialisasi dan load data dari API
            gantt.init("gantt_here");
            gantt.load("{{ route('gantt.kegiatan.data', ['id' => $kegiatan->id]) }}");
        </script>
    @endpush
</x-app-layout>