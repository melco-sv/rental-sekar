<!DOCTYPE html>
<html lang="id" x-data="adminApp()" style="color-scheme: light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0f0f23; }
        .main-content { background: #f0f0ff; border-radius: 20px 0 0 20px; }
        @media (max-width: 768px) { .main-content { border-radius: 0; } }
        @keyframes slideInRight {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0);    opacity: 1; }
            to   { transform: translateX(110%); opacity: 0; }
        }
        .toast-enter { animation: slideInRight 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .toast-leave { animation: slideOutRight 0.3s ease-in forwards; }
        .notif-badge {
            animation: badgePop 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes badgePop {
            from { transform: scale(0); }
            to   { transform: scale(1); }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased" style="background: linear-gradient(135deg, #0f0f23, #1a1a3e, #0f0f23);">

    {{-- ===== TOAST NOTIFICATIONS (top-right) ===== --}}
    <div class="fixed top-4 right-4 z-[9999] space-y-3 w-80 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto flex items-start gap-3 bg-white rounded-2xl shadow-2xl p-4 border-l-4 toast-enter"
                 :class="{
                     'border-blue-500':   toast.type === 'booking',
                     'border-green-500':  toast.type === 'payment',
                     'border-orange-500': toast.type === 'extension',
                     'border-pink-500':   toast.type === 'cancellation',
                     'border-red-500':    toast.type === 'error',
                 }"
                 style="box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                     :class="{
                         'bg-blue-100':   toast.type === 'booking',
                         'bg-green-100':  toast.type === 'payment',
                         'bg-orange-100': toast.type === 'extension',
                         'bg-pink-100':   toast.type === 'cancellation',
                         'bg-red-100':    toast.type === 'error',
                     }">
                    <span x-text="toast.icon"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-sm" x-text="toast.title"></p>
                    <p class="text-gray-500 text-xs mt-0.5 leading-snug" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0">✕</button>

                {{-- Progress bar --}}
                <div class="absolute bottom-0 left-0 h-1 rounded-b-2xl transition-all"
                     :class="{
                         'bg-blue-400':   toast.type === 'booking',
                         'bg-green-400':  toast.type === 'payment',
                         'bg-orange-400': toast.type === 'extension',
                         'bg-pink-400':   toast.type === 'cancellation',
                         'bg-red-400':    toast.type === 'error',
                     }"
                     :style="'width:' + toast.progress + '%;right:0'">
                </div>
            </div>
        </template>
    </div>

    <div class="flex min-h-screen">
        {{-- ===== SIDEBAR ===== --}}
        <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed top-0 left-0 h-full z-40 w-64 flex flex-col transition-transform duration-300 ease-in-out"
               style="background: linear-gradient(180deg, #1a0938 0%, #0f1941 50%, #0a0a1a 100%); border-right: 1px solid rgba(255,255,255,0.05);">

            {{-- Logo --}}
            <div class="p-5 border-b border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg text-white flex-shrink-0 transition-transform duration-300 group-hover:scale-110 animate-glow"
                         style="background: linear-gradient(135deg, #7C3AED, #2563EB);">🚗</div>
                    <div>
                        <p class="font-bold text-white text-sm leading-tight">{{ config('app.name') }}</p>
                        <p class="text-purple-400 text-xs">Admin Panel</p>
                    </div>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @php
                    $adminNav = [
                        ['route' => 'admin.dashboard',        'label' => 'Dashboard',    'icon' => '📊', 'match' => 'admin.dashboard',     'key' => null],
                        ['route' => 'admin.vehicles.index',   'label' => 'Kendaraan',    'icon' => '🚗', 'match' => 'admin.vehicles.*',    'key' => null],
                        ['route' => 'admin.bookings.index',   'label' => 'Pemesanan',    'icon' => '📋', 'match' => 'admin.bookings.*',    'key' => 'bookings'],
                        ['route' => 'admin.payments.index',   'label' => 'Pembayaran',   'icon' => '💳', 'match' => 'admin.payments.*',    'key' => 'payments'],
                        ['route' => 'admin.extensions.index', 'label' => 'Perpanjangan', 'icon' => '⏰', 'match' => 'admin.extensions.*', 'key' => 'extensions'],
                        ['route' => 'admin.refunds.index',    'label' => 'Pengembalian Dana', 'icon' => '💸', 'match' => 'admin.refunds.*', 'key' => 'cancellations'],
                        ['route' => 'admin.customers.index',  'label' => 'Pelanggan',    'icon' => '👥', 'match' => 'admin.customers.*',  'key' => null],
                        ['route' => 'admin.reports.index',    'label' => 'Laporan',      'icon' => '📈', 'match' => 'admin.reports.*',    'key' => null],
                    ];
                @endphp

                @foreach($adminNav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-all duration-200
                              {{ request()->routeIs($item['match']) ? 'active text-white' : 'text-gray-400 hover:text-white' }}">
                        <span class="text-base w-6 text-center flex-shrink-0">{{ $item['icon'] }}</span>
                        <span class="flex-1">{{ $item['label'] }}</span>

                        {{-- Real-time badge --}}
                        @if($item['key'])
                            <span x-show="counts.{{ $item['key'] }} > 0"
                                  x-text="counts.{{ $item['key'] }}"
                                  x-cloak
                                  class="notif-badge min-w-[20px] h-5 px-1.5 rounded-full text-xs font-black text-white flex items-center justify-center"
                                  :class="{
                                      'bg-blue-500':   '{{ $item['key'] }}' === 'bookings',
                                      'bg-green-500':  '{{ $item['key'] }}' === 'payments',
                                      'bg-orange-500': '{{ $item['key'] }}' === 'extensions',
                                      'bg-pink-500':   '{{ $item['key'] }}' === 'cancellations',
                                  }">
                            </span>
                        @endif

                        @if(request()->routeIs($item['match']))
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse-slow flex-shrink-0"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- User & Logout --}}
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                         style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-purple-400 text-xs">Administrator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200 border border-red-500/20">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
             class="fixed inset-0 z-30 bg-black/60 md:hidden backdrop-blur-sm"></div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 flex flex-col md:ml-64 min-h-screen main-content">
            {{-- Top Header --}}
            <header class="sticky top-0 z-20 px-6 py-4 flex items-center justify-between"
                    style="background: rgba(240,240,255,0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(139,92,246,0.1);">
                <div class="flex items-center gap-3">
                    <button @click="mobileOpen = true"
                            class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
                    </div>
                </div>

                {{-- Header notification summary --}}
                <div class="flex items-center gap-3">
                    {{-- Total pending badge in header --}}
                    <div x-show="totalPending > 0" x-cloak
                         class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold text-white animate-pulse-slow"
                         style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span x-text="totalPending + ' perlu ditindak'"></span>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium text-purple-700 bg-purple-50">
                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse-slow"></span>
                        Admin
                    </div>
                    <div class="text-sm text-gray-600 font-medium hidden sm:block">{{ auth()->user()->name }}</div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-6 pt-4 space-y-2">
                @if (session('success'))
                    <div data-alert class="flex items-center gap-3 bg-white rounded-2xl shadow-sm border border-green-100 px-4 py-3 animate-slide-down">
                        <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center text-sm flex-shrink-0">✅</div>
                        <p class="text-sm text-gray-800">{{ session('success') }}</p>
                        <button onclick="this.parentElement.remove()" class="font-bold ml-auto text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                    </div>
                @endif
                @if (session('error'))
                    <div data-alert class="flex items-center gap-3 bg-white rounded-2xl shadow-sm border border-red-100 px-4 py-3 animate-slide-down">
                        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center text-sm flex-shrink-0">❌</div>
                        <p class="text-sm text-gray-800">{{ session('error') }}</p>
                        <button onclick="this.parentElement.remove()" class="font-bold ml-auto text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <main class="flex-1 px-6 py-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    {{-- ===== REAL-TIME SYSTEM (polling 10 detik + refresh card dashboard) ===== --}}
    <script>
    function adminApp() {
        return {
            mobileOpen: false,
            counts: {
                bookings:      {{ $notifCounts['bookings']      ?? 0 }},
                payments:      {{ $notifCounts['payments']      ?? 0 }},
                extensions:    {{ $notifCounts['extensions']    ?? 0 }},
                cancellations: {{ $notifCounts['cancellations'] ?? 0 }},
            },
            prevCounts: { bookings: -1, payments: -1, extensions: -1, cancellations: -1 },
            toasts: [],

            get totalPending() {
                return this.counts.bookings + this.counts.payments + this.counts.extensions + this.counts.cancellations;
            },

            init() {
                this.prevCounts = { ...this.counts };

                // Poll setiap 10 detik
                setInterval(() => this.fetchCounts(), 10000);

                // Fetch pertama setelah 2 detik
                setTimeout(() => this.fetchCounts(), 2000);
            },

            // Refresh isi card dashboard via AJAX (hanya saat di halaman dashboard)
            async refreshCards() {
                const grid = document.getElementById('pending-cards-grid');
                if (!grid) return;

                try {
                    const res = await fetch('{{ route('admin.dashboard.cards') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    if (!res.ok) return;
                    const html = await res.text();

                    // Fade out → swap content → fade in
                    grid.style.transition = 'opacity 0.15s ease';
                    grid.style.opacity    = '0.3';
                    setTimeout(() => {
                        grid.innerHTML     = html;
                        grid.style.opacity = '1';
                    }, 150);
                } catch (e) { /* koneksi putus sementara */ }
            },

            async fetchCounts() {
                try {
                    const res = await fetch('{{ route('admin.notifications.counts') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    const newCounts = {
                        bookings:      data.pending_bookings,
                        payments:      data.pending_payments,
                        extensions:    data.pending_extensions,
                        cancellations: data.pending_refunds,
                    };

                    // Deteksi perubahan apapun (naik atau turun)
                    const hasChange =
                        newCounts.bookings      !== this.counts.bookings      ||
                        newCounts.payments      !== this.counts.payments      ||
                        newCounts.extensions    !== this.counts.extensions    ||
                        newCounts.cancellations !== this.counts.cancellations;

                    // Toast notif hanya saat kenaikan
                    if (this.prevCounts.bookings >= 0) {
                        const diff = {
                            bookings:      newCounts.bookings      - this.prevCounts.bookings,
                            payments:      newCounts.payments      - this.prevCounts.payments,
                            extensions:    newCounts.extensions    - this.prevCounts.extensions,
                            cancellations: newCounts.cancellations - this.prevCounts.cancellations,
                        };
                        if (diff.bookings      > 0) this.addToast('🚗 Pemesanan Baru Masuk!',     diff.bookings      + ' pemesanan menunggu konfirmasimu.', 'booking',      '📋');
                        if (diff.payments      > 0) this.addToast('💳 Bukti Pembayaran Diterima!', diff.payments      + ' pembayaran menunggu verifikasi.',   'payment',      '💳');
                        if (diff.extensions    > 0) this.addToast('⏰ Permintaan Perpanjangan!',   diff.extensions    + ' perpanjangan menunggu konfirmasimu.','extension',    '⏰');
                        if (diff.cancellations > 0) this.addToast('💸 Ada Pembatalan Pemesanan!', diff.cancellations + ' pengembalian dana menunggu diproses.', 'cancellation', '💸');
                    }

                    this.prevCounts = { ...newCounts };
                    this.counts     = newCounts;

                    // Refresh card SELALU saat ada perubahan data
                    if (hasChange) this.refreshCards();

                } catch (e) { /* koneksi putus sementara */ }
            },

            addToast(title, message, type, icon) {
                const id   = Date.now();
                const step = 100 / (5000 / 100);
                this.toasts.push({ id, title, message, type, icon, progress: 100 });

                const interval = setInterval(() => {
                    const toast = this.toasts.find(t => t.id === id);
                    if (!toast) { clearInterval(interval); return; }
                    toast.progress -= step;
                    if (toast.progress <= 0) { clearInterval(interval); this.removeToast(id); }
                }, 100);
            },

            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
        };
    }
    </script>
</body>
</html>
