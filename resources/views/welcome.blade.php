<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Kegiatan Statistik BPS Jakarta Selatan</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="relative min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold">BPS Jakarta Selatan</h1>
                    </div>
                    <!-- Tombol Login/Register -->
                    <div class="hidden sm:block">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log
                                in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Daftar Kegiatan -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold mb-4">Daftar Kegiatan Statistik</h2>
                            <div class="space-y-4">
                                @forelse ($kegiatans as $kegiatan)
                                    <a href="{{ route('kegiatan.publik.show', $kegiatan) }}"
                                        class="block p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-300">
                                        <h3 class="font-semibold text-lg">{{ $kegiatan->nama_kegiatan }}</h3>
                                        <p class="text-sm text-gray-500">Jadwal:
                                            {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('d M Y') }}</p>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                                            {{ Str::limit($kegiatan->deskripsi, 150) }}</p>
                                    </a>
                                @empty
                                    <p>Saat ini belum ada kegiatan yang ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Metadata -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold mb-4">Metadata Tersedia</h2>
                            <ul class="list-disc list-inside">
                                @forelse ($metadata as $meta)
                                    <li>
                                        {{ $meta->nama_metadata }}
                                        @if ($meta->file_metadata)
                                            <a href="{{ asset('storage/' . $meta->file_metadata) }}" target="_blank"
                                                class="text-blue-500 hover:underline ml-2">(Lihat File)</a>
                                        @endif
                                    </li>
                                @empty
                                    <li>Belum ada metadata yang tersedia.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} BPS Kota Jakarta Selatan. All rights reserved.
        </footer>
    </div>
</body>

</html>
