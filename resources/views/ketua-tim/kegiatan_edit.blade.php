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

                    <form action="{{ route('ketua-tim.kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        {{-- BAGIAN INFO KEGIATAN --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Kegiatan</h3>
                        
                        <div class="mb-4">
                            <label for="nama_kegiatan" class="block font-medium text-sm text-gray-700">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block font-medium text-sm text-gray-700">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tanggal_mulai" class="block font-medium text-sm text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai) }}" required>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block font-medium text-sm text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="anggota_ids" class="block font-medium text-sm text-gray-700">Pilih Anggota Tim</label>
                            <select name="anggota_ids[]" id="anggota_ids" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                @foreach ($calonAnggota as $anggota)
                                    <option value="{{ $anggota->id }}" @selected(in_array($anggota->id, old('anggota_ids', $anggotaTerkait)))>
                                        {{ $anggota->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="file_pendukung" class="block font-medium text-sm text-gray-700">File Pendukung Kegiatan</label>
                            <input type="file" name="file_pendukung" id="file_pendukung" class="block mt-1 w-full">
                            @if ($kegiatan->file_pendukung)
                                <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="{{ asset('storage/' . $kegiatan->file_pendukung) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File</a>.</p>
                            @endif
                        </div>

                        {{-- BAGIAN INFO METADATA --}}
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4 mt-8">Informasi Metadata</h3>
                        
                        <div class="mb-4">
                            <label for="nama_metadata" class="block font-medium text-sm text-gray-700">Nama Metadata</label>
                            <input type="text" name="nama_metadata" id="nama_metadata" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nama_metadata', $kegiatan->metadata->nama_metadata) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan_metadata" class="block font-medium text-sm text-gray-700">Keterangan Metadata</label>
                            <textarea name="keterangan_metadata" id="keterangan_metadata" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('keterangan_metadata', $kegiatan->metadata->keterangan) }}</textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="file_metadata" class="block font-medium text-sm text-gray-700">File Metadata</label>
                            <input type="file" name="file_metadata" id="file_metadata" class="block mt-1 w-full">
                            @if ($kegiatan->metadata->file_metadata)
                                <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="{{ asset('storage/' . $kegiatan->metadata->file_metadata) }}" target="_blank" class="text-blue-500 hover:underline">Lihat File</a>.</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('ketua-tim.kegiatan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
