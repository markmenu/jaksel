<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex flex-col sm:flex-row justify-between mb-4">
                        <form action="{{ route('ketua-tim.kegiatan.index') }}" method="GET" class="w-full sm:w-1/2">
                            <div class="flex">
                                <input type="text" name="search" placeholder="Cari nama kegiatan..." 
                                       class="w-full rounded-l-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" 
                                       value="{{ $search ?? '' }}">
                                <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-r-md">
                                    Cari
                                </button>
                            </div>
                        </form>
                        @if(empty($search))
                        <a href="{{ route('ketua-tim.kegiatan.create') }}" 
                           class="mt-2 sm:mt-0 sm:ml-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-md transition duration-300 ease-in-out whitespace-nowrap">
                            Buat Kegiatan Baru
                        </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        @if($kegiatans->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'nama_kegiatan' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('ketua-tim.kegiatan.index', ['sort' => 'nama_kegiatan', 'direction' => $direction, 'search' => $search]) }}">
                                                Nama Kegiatan @if($sortColumn == 'nama_kegiatan') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'tanggal_mulai' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('ketua-tim.kegiatan.index', ['sort' => 'tanggal_mulai', 'direction' => $direction, 'search' => $search]) }}">
                                                Tanggal Mulai @if($sortColumn == 'tanggal_mulai') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'tanggal_selesai' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('ketua-tim.kegiatan.index', ['sort' => 'tanggal_selesai', 'direction' => $direction, 'search' => $search]) }}">
                                                Tanggal Selesai @if($sortColumn == 'tanggal_selesai') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'status' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('ketua-tim.kegiatan.index', ['sort' => 'status', 'direction' => $direction, 'search' => $search]) }}">
                                                Status @if($sortColumn == 'status') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($kegiatans as $kegiatan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $kegiatan->nama_kegiatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $kegiatan->status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('ketua-tim.kegiatan.show', $kegiatan) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">Lihat</a>
                                            <a href="{{ route('ketua-tim.kegiatan.edit', $kegiatan) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 ml-4">Edit</a>
                                            <form action="{{ route('ketua-tim.kegiatan.destroy', $kegiatan) }}" method="POST" class="inline-block ml-4 form-hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada kegiatan yang cocok dengan pencarian Anda.</p>
                                <a href="{{ route('ketua-tim.kegiatan.index') }}" class="mt-4 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">Kembali ke daftar kegiatan</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        @if($kegiatans->isNotEmpty())
                            {{ $kegiatans->appends(['search' => $search])->links() }}
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Script untuk konfirmasi hapus
        document.querySelectorAll('.form-hapus').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data kegiatan yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
    
</x-app-layout>
