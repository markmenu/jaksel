<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="space-y-4">
                        @forelse ($notifications as $notification)
                            <a href="{{ route('notifications.markAsRead', $notification->id) }}" 
                               class="block p-4 rounded-lg transition duration-300 {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700' : 'bg-blue-50 dark:bg-blue-900/50 hover:bg-blue-100' }}">
                                
                                <div class="flex justify-between items-center">
                                    <p class="font-semibold">{{ $notification->data['anggota_name'] ?? 'Sistem' }}</p>
                                    @if (!$notification->read_at)
                                        <span class="text-xs text-blue-500 font-bold">BARU</span>
                                    @endif
                                </div>
                                <p class="text-sm mt-1">{{ $notification->data['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p>Tidak ada notifikasi untuk ditampilkan.</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
