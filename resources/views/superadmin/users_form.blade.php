<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($user) ? route('superadmin.users.update', $user) : route('superadmin.users.store') }}" method="POST">
                        @csrf
                        @if (isset($user))
                            @method('PUT')
                        @endif

                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                            <input type="text" name="name" id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('name', $user->name ?? '') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block font-medium text-sm text-gray-700">Peran</label>
                            <select name="role" id="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                <option value="superadmin" @selected(old('role', $user->role ?? '') == 'superadmin')>Superadmin</option>
                                <option value="ketua_tim" @selected(old('role', $user->role ?? '') == 'ketua_tim')>Ketua Tim</option>
                                <option value="anggota_tim" @selected(old('role', $user->role ?? '') == 'anggota_tim')>Anggota Tim</option>
                                <option value="kepala" @selected(old('role', $user->role ?? '') == 'kepala')>Kepala BPS</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                            <input type="password" name="password" id="password" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            @if (isset($user))
                                <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('superadmin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>