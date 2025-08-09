<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('task.update', $task) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nama_kegiatan" class="block font-medium text-sm text-gray-700">Nama Task</label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" value="{{ old('nama_kegiatan', $task->nama_kegiatan) }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block font-medium text-sm text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $task->tanggal_mulai->format('Y-m-d')) }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block font-medium text-sm text-gray-700">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', $task->tanggal_selesai->format('Y-m-d')) }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="progress" class="block font-medium text-sm text-gray-700">Progress (%)</label>
                    <input type="number" name="progress" id="progress" min="0" max="100" value="{{ old('progress', $task->progress) }}" class="block mt-1 w-24 rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="parent_id" class="block font-medium text-sm text-gray-700">Parent Task (Opsional)</label>
                    <select name="parent_id" id="parent_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Tidak Ada (Task Utama) --</option>
                        @foreach ($kegiatans as $kegiatan)
                            <option value="{{ $kegiatan->id }}" @selected(old('parent_id', $task->parent_id) == $kegiatan->id)>
                                {{ $kegiatan->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <a href="{{ url()->previous() }}" class="mr-4 text-gray-600 hover:text-gray-900">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
