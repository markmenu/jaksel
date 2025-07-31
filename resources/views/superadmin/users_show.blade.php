<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="text-lg font-semibold">{{ $user->name }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-lg font-semibold">{{ $user->email }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Peran</p>
                        <p class="text-lg font-semibold">{{ $user->role_display }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                        <p class="text-lg font-semibold">{{ $user->created_at->format('d F Y, H:i') }}</p>
                    </div>

                    <div class="flex items-center justify-start mt-6">
                        {{-- Tombol untuk kembali ke daftar pengguna --}}
                        <a href="{{ route('superadmin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                            &larr; Kembali ke Daftar Pengguna
                        </a>

                        {{-- Tombol untuk mengarahkan ke halaman edit --}}
                        <a href="{{ route('superadmin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                            Edit Pengguna
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
