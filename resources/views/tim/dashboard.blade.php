<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Tim') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tampilan untuk Ketua Tim --}}
            @if(auth()->user()->role === 'ketua_tim')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card Ringkasan -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Kegiatan Dikelola</h3>
                        <p class="text-3xl font-bold mt-2">{{ $totalKegiatan }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Kegiatan Berjalan</h3>
                        <p class="text-3xl font-bold mt-2">{{ $kegiatanBerjalan }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Menunggu Persetujuan</h3>
                        <p class="text-3xl font-bold mt-2 text-yellow-500">{{ $menungguPersetujuan }}</p>
                    </div>
                </div>

                <!-- Notifikasi Terbaru -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Notifikasi Terbaru</h3>
                        <div class="space-y-3">
                            @forelse($notifikasiTerbaru as $notification)
                                <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="block p-3 bg-blue-50 dark:bg-blue-900/50 rounded-lg hover:bg-blue-100">
                                    <p class="text-sm">{{ $notification->data['message'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p>Tidak ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tampilan untuk Anggota Tim --}}
            @if(auth()->user()->role === 'anggota_tim')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card Ringkasan -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Tugas</h3>
                        <p class="text-3xl font-bold mt-2">{{ $totalTugas }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tugas Berjalan</h3>
                        <p class="text-3xl font-bold mt-2">{{ $tugasBerjalan }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tugas Selesai</h3>
                        <p class="text-3xl font-bold mt-2 text-green-500">{{ $tugasSelesai }}</p>
                    </div>
                </div>

                <!-- Deadline Terdekat -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Deadline Terdekat</h3>
                        <ul class="list-disc list-inside">
                            @forelse($deadlineTerdekat as $kegiatan)
                                <li>
                                    <a href="{{ route('anggota-tim.kegiatan.show', $kegiatan) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $kegiatan->nama_kegiatan }}
                                    </a>
                                    - <span class="text-sm text-gray-500">Batas waktu: {{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('d M Y') }}</span>
                                </li>
                            @empty
                                <p>Tidak ada deadline dalam waktu dekat.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
