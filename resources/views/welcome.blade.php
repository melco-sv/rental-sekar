<!DOCTYPE html>
<html lang="id" style="color-scheme: light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Sewa Mobil Mudah & Terpercaya</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-bg { background: linear-gradient(135deg, #0f0c29 0%, #302b63 40%, #24243e 70%, #0f0c29 100%); }
        .vehicle-card { transition: all 0.4s cubic-bezier(0.4,0,0.2,1); }
        .vehicle-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 30px 80px rgba(139,92,246,0.2); }
        .nav-scrolled { background: rgba(15,12,41,0.95) !important; backdrop-filter: blur(20px); box-shadow: 0 4px 30px rgba(0,0,0,0.3); }
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-4px); }
        .count-up { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ mobileOpen: false }">

    {{-- ===== NAVBAR ===== --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
         style="background: rgba(15,12,41,0.5); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.05);"
         :class="{ 'nav-scrolled': scrollY > 50 }" x-data="{ scrollY: 0 }" @scroll.window="scrollY = window.scrollY">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold transition-transform duration-300 group-hover:scale-110"
                     style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 4px 16px rgba(124,58,237,0.4);">🚗</div>
                <span class="font-bold text-white">{{ config('app.name') }}</span>
            </a>

            <div class="hidden md:flex items-center gap-6">
                <a href="#vehicles" class="text-purple-300 hover:text-white text-sm font-medium transition-colors">Kendaraan</a>
                <a href="#features" class="text-purple-300 hover:text-white text-sm font-medium transition-colors">Fitur</a>
                <a href="#stats" class="text-purple-300 hover:text-white text-sm font-medium transition-colors">Statistik</a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5"
                           style="background: linear-gradient(135deg, #7C3AED, #2563EB);">Admin Panel</a>
                    @elseif(auth()->user()->isOwner())
                        <a href="{{ route('owner.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5"
                           style="background: linear-gradient(135deg, #7C3AED, #EC4899);">Owner Panel</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5"
                           style="background: linear-gradient(135deg, #7C3AED, #2563EB);">Dashboard 🏠</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-purple-300 hover:text-white text-sm font-medium transition-colors hidden sm:block">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                       style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 4px 16px rgba(124,58,237,0.3);">
                        Daftar Gratis 🚀
                    </a>
                @endauth

                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-purple-300 hover:text-white p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden px-4 py-4 space-y-2 border-t border-white/10"
             style="background: rgba(15,12,41,0.98);">
            <a href="#vehicles" @click="mobileOpen=false" class="block px-3 py-2 text-purple-300 hover:text-white text-sm rounded-xl hover:bg-white/5">Kendaraan</a>
            <a href="#features" @click="mobileOpen=false" class="block px-3 py-2 text-purple-300 hover:text-white text-sm rounded-xl hover:bg-white/5">Fitur</a>
            @guest
                <a href="{{ route('login') }}" class="block px-3 py-2 text-purple-300 hover:text-white text-sm rounded-xl hover:bg-white/5">Masuk</a>
            @endguest
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="hero-bg min-h-screen flex items-center relative overflow-hidden pt-16">
        {{-- Background blobs --}}
        <div class="blob absolute w-[500px] h-[500px] opacity-20 -top-40 -right-40 pointer-events-none"
             style="background: linear-gradient(135deg, #7C3AED, #2563EB);"></div>
        <div class="blob-2 absolute w-80 h-80 opacity-15 bottom-20 -left-20 pointer-events-none"
             style="background: linear-gradient(135deg, #EC4899, #F97316);"></div>
        <div class="absolute inset-0 opacity-5 pointer-events-none"
             style="background-image: radial-gradient(rgba(139,92,246,0.4) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold text-purple-300 border border-purple-500/30 mb-6 animate-fade-in"
                         style="background: rgba(139,92,246,0.1);">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        ✨ Platform Sewa #1 di Indonesia
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6 animate-slide-up">
                        Sewa Mobil
                        <span class="block gradient-text mt-1">Kapan Aja,</span>
                        <span class="block text-white">Dimana Aja 🚗</span>
                    </h1>

                    <p class="text-purple-200 text-lg mb-8 leading-relaxed animate-slide-up animation-delay-200">
                        Nikmati kemudahan sewa kendaraan dengan sistem modern. Proses cepat, harga transparan, armada berkualitas.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 animate-slide-up animation-delay-300">
                        @guest
                            <a href="{{ route('login') }}" class="ripple inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-bold text-white transition-all duration-300 hover:-translate-y-1"
                               style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
                                🚀 Mulai Gratis
                            </a>
                            <a href="#vehicles" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-semibold text-white transition-all duration-300 hover:-translate-y-1 border border-white/20 hover:border-white/40"
                               style="background: rgba(255,255,255,0.08);">
                                🔍 Lihat Kendaraan
                            </a>
                        @else
                            <a href="{{ route('customer.vehicles.index') }}" class="ripple inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-bold text-white transition-all duration-300 hover:-translate-y-1"
                               style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
                                🚗 Cari Kendaraan
                            </a>
                        @endguest
                    </div>

                    {{-- Trust badges --}}
                    <div class="flex items-center gap-6 mt-10 animate-fade-in animation-delay-500">
                        <div class="flex -space-x-2">
                            @foreach(['bg-purple-500','bg-blue-500','bg-pink-500','bg-green-500'] as $c)
                                <div class="w-8 h-8 rounded-full border-2 border-gray-900 flex items-center justify-center text-xs text-white {{ $c }}">
                                    {{ ['A','B','C','D'][array_search($c,['bg-purple-500','bg-blue-500','bg-pink-500','bg-green-500'])] }}
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">500+ Pelanggan Puas</p>
                            <p class="text-purple-400 text-xs">⭐⭐⭐⭐⭐ Rating 5.0</p>
                        </div>
                    </div>
                </div>

                {{-- Hero Image / Floating Card --}}
                <div class="relative hidden lg:flex items-center justify-center">
                    <div class="animate-float relative w-full max-w-md">
                        <div class="glass rounded-3xl p-6 text-white" style="box-shadow: 0 25px 80px rgba(124,58,237,0.3);">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-purple-300 text-xs font-semibold uppercase tracking-wider">Pemesanan Aktif</p>
                                    <p class="text-2xl font-black text-white mt-0.5">Toyota Fortuner</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                     style="background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(37,99,235,0.3));">🚙</div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="rounded-xl p-3" style="background: rgba(255,255,255,0.07);">
                                    <p class="text-purple-300 text-xs mb-0.5">Mulai</p>
                                    <p class="text-white text-sm font-bold">{{ now()->format('d M Y') }}</p>
                                </div>
                                <div class="rounded-xl p-3" style="background: rgba(255,255,255,0.07);">
                                    <p class="text-purple-300 text-xs mb-0.5">Selesai</p>
                                    <p class="text-white text-sm font-bold">{{ now()->addDays(3)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-300 text-xs">Total</p>
                                    <p class="text-white text-xl font-black">Rp 1.800.000</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-green-400 border border-green-400/30" style="background: rgba(16,185,129,0.1);">
                                    ● Aktif
                                </span>
                            </div>
                        </div>

                        {{-- Floating mini cards --}}
                        <div class="absolute -top-6 -right-6 glass rounded-2xl p-3 animate-float animation-delay-300 flex items-center gap-2">
                            <div class="text-2xl">✅</div>
                            <div>
                                <p class="text-white text-xs font-bold">Verified</p>
                                <p class="text-purple-300 text-xs">Pembayaran</p>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -left-6 glass rounded-2xl p-3 animate-float animation-delay-700 flex items-center gap-2">
                            <div class="text-2xl">⚡</div>
                            <div>
                                <p class="text-white text-xs font-bold">Proses Cepat</p>
                                <p class="text-purple-300 text-xs">< 5 menit</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-purple-400 animate-bounce">
            <span class="text-xs">Scroll</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </section>

    {{-- ===== STATS ===== --}}
    <section id="stats" class="py-16 relative overflow-hidden" style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([
                    ['num' => 500, 'suffix' => '+', 'label' => 'Pelanggan Puas', 'icon' => '😊'],
                    ['num' => 5, 'suffix' => '+', 'label' => 'Armada Kendaraan', 'icon' => '🚗'],
                    ['num' => 1000, 'suffix' => '+', 'label' => 'Transaksi Sukses', 'icon' => '✅'],
                    ['num' => 24, 'suffix' => '/7', 'label' => 'Layanan Support', 'icon' => '🛟'],
                ] as $stat)
                    <div class="reveal">
                        <div class="text-4xl mb-2">{{ $stat['icon'] }}</div>
                        <div class="text-3xl sm:text-4xl font-black text-white count-up">
                            <span data-counter="{{ $stat['num'] }}">{{ $stat['num'] }}</span>{{ $stat['suffix'] }}
                        </div>
                        <p class="text-blue-200 text-sm mt-1 font-medium">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== VEHICLES ===== --}}
    <section id="vehicles" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100 mb-4 uppercase tracking-wider">
                    🚗 Armada Kami
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3">
                    Pilih Kendaraan <span class="gradient-text">Impianmu</span>
                </h2>
                <p class="text-gray-500 max-w-md mx-auto">Berbagai pilihan kendaraan premium siap menemani perjalananmu</p>
            </div>

            @if($vehicles->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    @foreach($vehicles as $i => $vehicle)
                        <div class="vehicle-card bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 reveal"
                             style="animation-delay: {{ $i * 100 }}ms">
                            @if($vehicle->photo)
                                <div class="relative overflow-hidden h-48">
                                    <img src="{{ asset('storage/' . $vehicle->photo) }}" alt="{{ $vehicle->name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                </div>
                            @else
                                <div class="h-48 flex items-center justify-center relative overflow-hidden"
                                     style="background: linear-gradient(135deg, #EDE9FE, #DBEAFE);">
                                    <span class="text-7xl animate-float">🚗</span>
                                    <div class="absolute inset-0 bg-gradient-to-t from-purple-100/50 to-transparent"></div>
                                </div>
                            @endif

                            <div class="p-5">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-black text-gray-900 text-lg leading-tight">{{ $vehicle->name }}</h3>
                                        <p class="text-gray-400 text-xs mt-0.5">{{ $vehicle->type }} · {{ $vehicle->plate_number }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 shrink-0 ml-2">
                                        ● Tersedia
                                    </span>
                                </div>

                                @if($vehicle->description)
                                    <p class="text-gray-500 text-xs mb-3 line-clamp-2">{{ $vehicle->description }}</p>
                                @endif

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-3">
                                    <div>
                                        <p class="text-xs text-gray-400">Mulai dari</p>
                                        <p class="font-black text-purple-600 text-lg">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-gray-400">/hari</span>
                                        </p>
                                    </div>
                                    @auth
                                        <a href="{{ route('customer.bookings.create', $vehicle) }}"
                                           class="ripple px-5 py-2.5 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                                           style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                                            Pesan
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="ripple px-5 py-2.5 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5"
                                           style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                                            Pesan
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center reveal">
                    @auth
                        <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center gap-2 text-purple-600 font-semibold hover:text-purple-800 transition-colors">
                            Lihat semua kendaraan <span class="text-lg">→</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-purple-600 font-semibold hover:text-purple-800 transition-colors">
                            Daftar untuk lihat semua <span class="text-lg">→</span>
                        </a>
                    @endauth
                </div>
            @else
                <div class="text-center py-20">
                    <div class="text-7xl mb-4 animate-float">🚗</div>
                    <p class="text-gray-400 text-lg">Belum ada kendaraan tersedia.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section id="features" class="py-20" style="background: #f8f7ff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100 mb-4 uppercase tracking-wider">✨ Keunggulan</span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3">Kenapa Pilih <span class="gradient-text">Kami?</span></h2>
                <p class="text-gray-500 max-w-md mx-auto">Kami hadirkan pengalaman sewa kendaraan yang beda dari yang lain</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'⚡','title'=>'Pemesanan Kilat','desc'=>'Buat pemesanan dalam hitungan menit. Pilih tanggal, upload bukti bayar, langsung jalan!','color'=>'from-yellow-400 to-orange-500','bg'=>'bg-yellow-50'],
                    ['icon'=>'🛡️','title'=>'Terpercaya & Aman','desc'=>'Armada terawat, proses terverifikasi, dan layanan transparan untuk ketenangan pikiran kamu.','color'=>'from-emerald-400 to-green-500','bg'=>'bg-emerald-50'],
                    ['icon'=>'💜','title'=>'Support 24/7','desc'=>'Tim kami siap membantu kapan pun kamu butuh. Chat, telepon, atau datang langsung.','color'=>'from-purple-500 to-indigo-500','bg'=>'bg-purple-50'],
                    ['icon'=>'📱','title'=>'All Device','desc'=>'Akses dari HP, tablet, atau laptop. Desain responsif untuk semua layar.','color'=>'from-blue-400 to-cyan-500','bg'=>'bg-blue-50'],
                    ['icon'=>'💰','title'=>'Harga Transparan','desc'=>'Tidak ada biaya tersembunyi. Harga sudah termasuk semua, lihat total sebelum bayar.','color'=>'from-pink-400 to-rose-500','bg'=>'bg-pink-50'],
                    ['icon'=>'⏰','title'=>'Perpanjangan Mudah','desc'=>'Butuh waktu lebih? Ajukan perpanjangan sewa langsung dari aplikasi, cepat dan mudah.','color'=>'from-violet-400 to-purple-500','bg'=>'bg-violet-50'],
                ] as $i => $f)
                    <div class="feature-card glass-card rounded-3xl p-6 reveal" style="animation-delay: {{ $i * 100 }}ms">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-4 {{ $f['bg'] }}"
                             style="">{{ $f['icon'] }}</div>
                        <h3 class="font-black text-gray-900 text-lg mb-2">{{ $f['title'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== CTA ===== --}}
    @guest
    <section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);">
        <div class="blob absolute w-96 h-96 opacity-20 -top-20 -right-20 pointer-events-none"
             style="background: linear-gradient(135deg, #7C3AED, #2563EB);"></div>
        <div class="max-w-3xl mx-auto px-4 text-center reveal">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">
                Siap Mulai Perjalanan <span class="gradient-text">Seru</span>?
            </h2>
            <p class="text-purple-200 text-lg mb-8">Daftar gratis sekarang dan dapatkan akses ke semua kendaraan premium kami.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="ripple inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold text-white transition-all duration-300 hover:-translate-y-1"
                   style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.4);">
                    🚀 Daftar Sekarang — Gratis
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold text-white border border-white/20 transition-all duration-300 hover:-translate-y-1 hover:bg-white/10">
                    Sudah punya akun? Masuk ✨
                </a>
            </div>
        </div>
    </section>
    @endguest

    {{-- ===== FOOTER ===== --}}
    <footer class="py-12" style="background: #0a0a1f;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
                         style="background: linear-gradient(135deg, #7C3AED, #2563EB);">🚗</div>
                    <div>
                        <p class="text-white font-bold">{{ config('app.name') }}</p>
                        <p class="text-purple-400 text-xs">Sistem Informasi Penyewaan Kendaraan</p>
                    </div>
                </div>
                <p class="text-purple-500 text-sm">&copy; {{ date('Y') }} {{ config('app.name') }} · Made with 💜</p>
            </div>
        </div>
    </footer>

</body>
</html>
