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

                    {{-- Tombol Kembali --}}
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <a href="{{ route('anggota-tim.kegiatan.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">
                            &larr; Kembali ke Daftar Kegiatan
                        </a>
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
                    @if ($kegiatan->metadata)
                        <h3 class="text-lg font-semibold mb-2 mt-8 border-t pt-4">Informasi Metadata</h3>
                        <div>
                            <p class="text-sm text-gray-500">Nama Metadata</p>
                            <p class="font-semibold">
                                {{ $kegiatan->metadata->nama_metadata }}</p>
                        </div>
                    @endif

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

            {{-- KOTAK BARU KHUSUS UNTUK DAFTAR TASK --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Daftar Task</h3>
                    
                    <ul class="list-none space-y-4">
                        @forelse($kegiatan->childrenRecursive as $task)
                            @include('anggota-tim.partials.task_item', ['task' => $task, 'level' => 0])
                        @empty
                            <p class="text-gray-500">Tidak ada task terkait.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif
    @endpush
</x-app-layout>
