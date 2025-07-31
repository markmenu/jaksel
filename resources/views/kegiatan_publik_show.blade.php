<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $kegiatan->nama_kegiatan }} - BPS Jakarta Selatan</title>
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
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="text-xl font-bold">BPS Jakarta Selatan</a>
                    </div>
                    <div class="hidden sm:block">
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Log in</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 md:p-8">
                            <a href="{{ route('home') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mb-6 inline-block">&larr; Kembali ke Halaman Utama</a>
                            
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $kegiatan->nama_kegiatan }}</h1>
                            <p class="text-md text-gray-500 dark:text-gray-400 mt-2">
                                Jadwal Pelaksanaan: <strong>{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d F Y') }}</strong> - <strong>{{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('d F Y') }}</strong>
                            </p>

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold mb-2">Deskripsi Kegiatan</h2>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $kegiatan->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                            </div>

                            @if($kegiatan->metadata)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold mb-2">Informasi Metadata</h2>
                                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <dt class="text-sm text-gray-500">Nama Metadata</dt>
                                        <dd class="font-semibold">{{ $kegiatan->metadata->nama_metadata }}</dd>
                                    </div>
                                    <div class="md:col-span-2">
                                        <dt class="text-sm text-gray-500">Keterangan</dt>
                                        <dd>{{ $kegiatan->metadata->keterangan ?: '-' }}</dd>
                                    </div>
                                    @if($kegiatan->metadata->file_metadata)
                                    <div>
                                        <dt class="text-sm text-gray-500">Dokumen Terkait</dt>
                                        <dd>
                                            <a href="{{ asset('storage/' . $kegiatan->metadata->file_metadata) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">Unduh File</a>
                                        </dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                            @endif

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
