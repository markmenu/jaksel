{{-- resources/views/anggota-tim/partials/task_item.blade.php --}}

<li style="margin-left: {{ $level * 2 }}rem;" class="border-b last:border-b-0 py-2">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <span class="font-semibold">{{ $task->nama_kegiatan }}</span>
            <span class="text-sm text-gray-500 ml-2">({{ $task->tanggal_mulai->format('d M') }} -
                {{ $task->tanggal_selesai->format('d M') }})</span>
        </div>

        {{-- Cek apakah task ini adalah unit terkecil (tidak punya anak) --}}
        @if ($task->children->isEmpty())
            <form action="{{ route('anggota-tim.kegiatan.updateProgress', $task) }}" method="POST"
                class="inline-block ml-4">
                @csrf
                @method('PUT')
                <div class="flex items-center">
                    <input type="number" name="progress" min="0" max="100"
                        value="{{ old('progress', $task->progress) }}"
                        class="w-20 px-2 py-1 text-sm border-gray-300 rounded-md">
                    <button type="submit"
                        class="ml-2 px-3 py-1 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">Update</button>
                </div>
            </form>
        @else
            <span class="text-sm font-semibold text-gray-500">Progres: {{ $task->progress }}%</span>
        @endif
    </div>

    {{-- Rekursif untuk menampilkan subtask --}}
    @if ($task->children->isNotEmpty())
        <ul class="list-none mt-2">
            @foreach ($task->children as $child)
                @include('anggota-tim.partials.task_item', ['task' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
