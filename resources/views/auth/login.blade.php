<!-- File: resources/views/auth/login.blade.php -->
<!-- Tampilan ini menggabungkan desain kustom dengan logika Blade dari Laravel Breeze. -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk - BPS Jakarta Selatan</title>
    <!-- Pastikan Anda sudah menginstal dan menjalankan 'npm run dev' -->
    @vite('resources/css/app.css')
    <!-- Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-bps-orange/10 antialiased">

    <!-- Container Utama, Centered -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
            
            <!-- Session Status (Pesan berhasil/gagal) -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Logo BPS -->
            <div class="flex justify-center mb-6">
                <!-- Ganti URL placeholder ini dengan path ke logo BPS Anda -->
                <img src="{{ asset('images/bps-hd.png') }}" alt="Logo BPS" class="w-24 h-24 object-contain">
            </div>
            
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Masuk</h1>
                <p class="text-sm text-gray-500 mt-1">Gunakan akun email BPS Anda</p>
            </div>

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-bps-gray">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-bps-gray">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Ingat saya di perangkat ini</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500 transition-colors">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="w-full bg-bps-orange text-white font-bold py-3 px-4 rounded-lg shadow-md hover:bg-bps-orange/90 transition-colors">
                        MASUK
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
