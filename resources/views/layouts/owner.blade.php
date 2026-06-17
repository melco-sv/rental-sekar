<!DOCTYPE html>
<html lang="id" x-data="{ mobileOpen: false }" style="color-scheme: light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Owner — {{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0a0a1f; }
        .main-content { background: #f5f0ff; border-radius: 20px 0 0 20px; }
        @media (max-width: 768px) { .main-content { border-radius: 0; } }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased min-h-screen" style="background: linear-gradient(135deg, #0a0a1f, #1a0938, #0a0a1f);">
    <div class="flex min-h-screen">
        <aside class="fixed top-0 left-0 h-full z-40 w-64 flex flex-col transition-transform duration-300 md:translate-x-0"
               :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
               style="background: linear-gradient(180deg, #1a0a38 0%, #0f0630 100%); border-right: 1px solid rgba(139,92,246,0.1);">

            <div class="p-5 border-b border-white/5">
                <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg text-white flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                         style="background: linear-gradient(135deg, #7C3AED, #EC4899);">💎</div>
                    <div>
                        <p class="font-bold text-white text-sm">{{ config('app.name') }}</p>
                        <p class="text-purple-300 text-xs">Owner Panel</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                @php $ownerNav = [
                    ['route'=>'owner.dashboard','label'=>'Dashboard','icon'=>'💰','match'=>'owner.dashboard'],
                    ['route'=>'owner.reports.index','label'=>'Laporan Keuangan','icon'=>'📈','match'=>'owner.reports.*'],
                ]; @endphp
                @foreach($ownerNav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium
                              {{ request()->routeIs($item['match']) ? 'active text-white' : 'text-purple-300 hover:text-white' }}">
                        <span class="text-base">{{ $item['icon'] }}</span> {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-white/5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold"
                         style="background: linear-gradient(135deg, #7C3AED, #EC4899);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-purple-300 text-xs">Pemilik</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-pink-400 hover:bg-pink-500/10 transition-all border border-pink-500/20">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="mobileOpen" x-cloak @click="mobileOpen = false" class="fixed inset-0 z-30 bg-black/60 md:hidden backdrop-blur-sm"></div>

        <div class="flex-1 flex flex-col md:ml-64 main-content">
            <header class="sticky top-0 z-20 px-6 py-4 flex items-center justify-between"
                    style="background: rgba(245,240,255,0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(139,92,246,0.15);">
                <div class="flex items-center gap-3">
                    <button @click="mobileOpen = true" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium text-purple-700 bg-purple-50">
                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse-slow"></span> Owner
                    </span>
                </div>
            </header>

            <div class="px-6 pt-4 space-y-2">
                @if(session('success'))
                    <div data-alert class="flex items-center gap-3 bg-white rounded-2xl shadow-sm border border-green-100 px-4 py-3 animate-slide-down">
                        <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center text-sm">✅</div>
                        <p class="text-sm text-gray-800">{{ session('success') }}</p>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400">✕</button>
                    </div>
                @endif
            </div>

            <main class="flex-1 px-6 py-6">@yield('content')</main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
