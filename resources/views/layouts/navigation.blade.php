<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @if (auth()->user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    @elseif (auth()->user()->role === 'kepala')
                        <a href="{{ route('kepala.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    @else
                        <a href="{{ route('tim.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Link Dashboard Utama --}}
                    @if (auth()->user()->role === 'superadmin')
                        <x-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @elseif (auth()->user()->role === 'kepala')
                         <x-nav-link :href="route('kepala.dashboard')" :active="request()->routeIs('kepala.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('tim.dashboard')" :active="request()->routeIs('tim.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    {{-- MENU KHUSUS UNTUK SETIAP PERAN --}}
                    @if (auth()->user()->role === 'superadmin')
                        <x-nav-link :href="route('superadmin.users.index')" :active="request()->routeIs('superadmin.users.*')">
                            {{ __('Manajemen Pengguna') }}
                        </x-nav-link>
                    @endif
                    @if (auth()->user()->role === 'ketua_tim')
                        <x-nav-link :href="route('ketua-tim.kegiatan.index')" :active="request()->routeIs('ketua-tim.kegiatan.*')">
                            {{ __('Manajemen Kegiatan') }}
                        </x-nav-link>
                    @endif
                    @if (auth()->user()->role === 'anggota_tim')
                        <x-nav-link :href="route('anggota-tim.kegiatan.index')" :active="request()->routeIs('anggota-tim.kegiatan.*')">
                            {{ __('Kegiatan Saya') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown & Notifikasi -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if(auth()->user()->role === 'ketua_tim')
                    <div class="me-3">
                        <x-dropdown align="right" width="64">
                            <x-slot name="trigger">
                                <button class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 rounded-lg hover:text-gray-700 focus:outline-none">
                                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 4 14h12a1 1 0 0 0 .707-1.707L16 11.586V8a6 6 0 0 0-6-6Zm0 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                    </svg>
                                    <span class="sr-only">Notifikasi</span>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -end-1">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </div>
                                    @endif
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="p-2 text-sm text-gray-700 dark:text-gray-200 max-h-60 overflow-y-auto">
                                    @forelse (auth()->user()->unreadNotifications as $notification)
                                        <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                                            <p class="font-semibold">{{ $notification->data['anggota_name'] }}</p>
                                            <p class="text-xs">{{ Str::limit($notification->data['message'], 50) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <p class="px-4 py-2">Tidak ada notifikasi baru.</p>
                                    @endforelse
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-600">
                                    <a href="{{ route('notifications.index') }}" class="block w-full text-center px-4 py-2 text-sm text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown (Profil Pengguna) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (auth()->user()->role === 'superadmin')
                <x-responsive-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @elseif (auth()->user()->role === 'kepala')
                <x-responsive-nav-link :href="route('kepala.dashboard')" :active="request()->routeIs('kepala.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('tim.dashboard')" :active="request()->routeIs('tim.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
