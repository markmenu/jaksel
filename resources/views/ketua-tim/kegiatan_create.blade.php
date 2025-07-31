<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Kegiatan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Pastikan form memiliki enctype untuk file upload --}}
                    <form action="{{ route('ketua-tim.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- BAGIAN INFO KEGIATAN --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Kegiatan</h3>
                        
                        <div class="mb-4">
                            <label for="nama_kegiatan" class="block font-medium text-sm text-gray-700">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nama_kegiatan') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block font-medium text-sm text-gray-700">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tanggal_mulai" class="block font-medium text-sm text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('tanggal_mulai') }}" required>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block font-medium text-sm text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="anggota_ids" class="block font-medium text-sm text-gray-700">Pilih Anggota Tim</label>
                            <select name="anggota_ids[]" id="anggota_ids" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                @foreach ($calonAnggota as $anggota)
                                    <option value="{{ $anggota->id }}">{{ $anggota->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu.</p>
                        </div>

                        <div class="mb-4">
                            <label for="file_pendukung" class="block font-medium text-sm text-gray-700">File Pendukung Kegiatan (Opsional)</label>
                            <input type="file" name="file_pendukung" id="file_pendukung" class="block mt-1 w-full">
                        </div>

                        {{-- BAGIAN INFO METADATA (TAMBAHAN) --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-8">Informasi Metadata</h3>
                        
                        <div class="mb-4">
                            <label for="nama_metadata" class="block font-medium text-sm text-gray-700">Nama Metadata</label>
                            <input type="text" name="nama_metadata" id="nama_metadata" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nama_metadata') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan_metadata" class="block font-medium text-sm text-gray-700">Keterangan Metadata</label>
                            <textarea name="keterangan_metadata" id="keterangan_metadata" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('keterangan_metadata') }}</textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="file_metadata" class="block font-medium text-sm text-gray-700">File Metadata (Opsional)</label>
                            <input type="file" name="file_metadata" id="file_metadata" class="block mt-1 w-full">
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('ketua-tim.kegiatan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                                Simpan Kegiatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
