<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col sm:flex-row justify-between mb-4">
                        <form action="{{ route('superadmin.users.index') }}" method="GET" class="w-full sm:w-1/2">
                            <div class="flex">
                                <input type="text" name="search" placeholder="Cari nama atau email..."
                                    class="w-full rounded-l-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ $search ?? '' }}">
                                <button type="submit"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-r-md">
                                    Cari
                                </button>
                            </div>
                        </form>

                        @if (empty($search))
                            <a href="{{ route('superadmin.users.create') }}"
                                class="mt-2 sm:mt-0 sm:ml-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-md transition duration-300 ease-in-out whitespace-nowrap">
                                Tambah Pengguna
                            </a>
                        @endif
                    </div>

                    {{-- ... (kode untuk menampilkan pesan sukses/error) ... --}}

                    <div class="overflow-x-auto">
                        {{-- Hanya tampilkan tabel jika ada data pengguna --}}
                        @if ($users->isNotEmpty())
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        {{-- ========================================================== --}}
                                        {{-- LOGIKA SORTIR ASC/DESC YANG DISEMPURNAKAN --}}
                                        {{-- ========================================================== --}}
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'name' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('superadmin.users.index', ['sort' => 'name', 'direction' => $direction, 'search' => $search]) }}">
                                                Nama @if($sortColumn == 'name') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'email' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('superadmin.users.index', ['sort' => 'email', 'direction' => $direction, 'search' => $search]) }}">
                                                Email @if($sortColumn == 'email') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            @php $direction = ($sortColumn == 'role' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                            <a href="{{ route('superadmin.users.index', ['sort' => 'role', 'direction' => $direction, 'search' => $search]) }}">
                                                Peran @if($sortColumn == 'role') <span>{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span> @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->role_display }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('superadmin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">Lihat</a>
                                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="inline-block ml-4 form-hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada pengguna yang cocok dengan pencarian Anda.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @else
                            {{-- Tampilkan pesan ini jika tidak ada hasil pencarian --}}
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada pengguna yang cocok dengan
                                    pencarian Anda.</p>
                                <a href="{{ route('superadmin.users.index') }}"
                                    class="mt-4 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">Kembali
                                    ke daftar pengguna</a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        {{ $users->appends(['search' => $search])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tangkap semua form dengan class 'form-hapus'
            const deleteForms = document.querySelectorAll('.form-hapus');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    // Hentikan pengiriman form standar
                    event.preventDefault();

                    // Tampilkan pop-up konfirmasi
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data pengguna yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        // Jika pengguna menekan tombol "Ya, hapus!"
                        if (result.isConfirmed) {
                            // Lanjutkan pengiriman form
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
