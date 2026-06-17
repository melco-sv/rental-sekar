<!DOCTYPE html>
<html lang="id" x-data="{ mobileOpen: false }" style="color-scheme: light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [data-navbar].scrolled {
            background: rgba(255,255,255,0.95) !important;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
        }
        body { background: #f8f7ff; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased">

    {{-- Navbar --}}
    <nav data-navbar class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
         style="background: rgba(255,255,255,0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.3);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold text-white transition-transform duration-300 group-hover:scale-110"
                         style="background: linear-gradient(135deg, #7C3AED, #2563EB);">🚗</div>
                    <span class="font-bold text-gray-900 text-sm hidden sm:block">{{ config('app.name') }}</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    @php
                        $navItems = [
                            ['route' => 'home', 'label' => 'Beranda', 'icon' => '🏠'],
                            ['route' => 'customer.vehicles.index', 'label' => 'Kendaraan', 'icon' => '🚗'],
                            ['route' => 'customer.bookings.index', 'label' => 'Pemesanan', 'icon' => '📋'],
                            ['route' => 'customer.extensions.index', 'label' => 'Perpanjangan', 'icon' => '⏰'],
                        ];
                    @endphp
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')
                                     ? 'text-purple-700 bg-purple-50'
                                     : 'text-gray-600 hover:text-purple-700 hover:bg-purple-50' }}">
                            <span>{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    {{-- Profile Dropdown --}}
                    <div class="relative hidden md:block" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition-all duration-200">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                 style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-24 truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-cloak
                             class="absolute right-0 mt-2 w-48 rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                             style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);">
                            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 transition-colors">
                                <span>🏠</span> Dashboard
                            </a>
                            <a href="{{ route('customer.profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 transition-colors">
                                <span>👤</span> Edit Profil
                            </a>
                            <hr class="border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <span>🚪</span> Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Mobile menu button --}}
                    <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-2 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-white/30 px-4 py-3 space-y-1"
             style="background: rgba(255,255,255,0.95);">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs($item['route']) ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <span>{{ $item['icon'] }}</span> {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('customer.profile.edit') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <span>👤</span> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                    <span>🚪</span> Keluar
                </button>
            </form>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="fixed top-20 right-4 z-50 space-y-2 max-w-sm w-full">
        @if (session('success'))
            <div data-alert class="flex items-center gap-3 bg-white rounded-2xl shadow-xl border border-green-100 px-4 py-3 animate-slide-down"
                 style="box-shadow: 0 8px 32px rgba(16,185,129,0.15);">
                <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center text-sm flex-shrink-0">✅</div>
                <p class="text-sm text-gray-800 font-medium">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">✕</button>
            </div>
        @endif
        @if (session('error'))
            <div data-alert class="flex items-center gap-3 bg-white rounded-2xl shadow-xl border border-red-100 px-4 py-3 animate-slide-down"
                 style="box-shadow: 0 8px 32px rgba(239,68,68,0.15);">
                <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center text-sm flex-shrink-0">❌</div>
                <p class="text-sm text-gray-800 font-medium">{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">✕</button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <main class="pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-gray-200 mt-12 py-6" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
