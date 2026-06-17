@extends('layouts.customer')
@section('title', $vehicle->name)

@section('content')
@php
    $allPhotos    = $vehicle->allPhotos();
    $hasPhotos    = count($allPhotos) > 0;
    $isRented     = $vehicle->status === 'rented';
    $isMaintenance = $vehicle->status === 'maintenance';
@endphp

<div class="mb-5 animate-fade-in">
    <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-500 hover:text-purple-700 transition-colors">
        ← Kembali ke daftar kendaraan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ===== PHOTO GALLERY ===== --}}
    <div class="lg:col-span-3 animate-slide-up"
         x-data="{
            current: 0,
            photos: {{ json_encode($hasPhotos ? array_map(fn($p) => asset('storage/'.$p), $allPhotos) : []) }},
            open: false,
            hasPhotos: {{ $hasPhotos ? 'true' : 'false' }},
            prev() { this.current = (this.current - 1 + this.photos.length) % this.photos.length },
            next() { this.current = (this.current + 1) % this.photos.length },
            goTo(i) { this.current = i }
         }">

        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-purple-100 to-blue-100 mb-3"
             style="aspect-ratio: 16/9;">
            <template x-if="hasPhotos">
                <img :src="photos[current]" :alt="'Foto ' + (current + 1)"
                     class="w-full h-full object-cover transition-all duration-500 {{ $isRented ? 'brightness-95' : '' }}"
                     @click="open = true" style="cursor: zoom-in;">
            </template>
            <template x-if="!hasPhotos">
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-8xl animate-float">🚗</span>
                </div>
            </template>

            <template x-if="photos.length > 1">
                <div>
                    <button @click.stop="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white text-xl transition-all hover:scale-110" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(8px)">‹</button>
                    <button @click.stop="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white text-xl transition-all hover:scale-110" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(8px)">›</button>
                </div>
            </template>

            <div class="absolute bottom-3 left-3 flex items-center gap-2">
                <template x-if="photos.length > 1">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(0,0,0,0.5)">
                        <span x-text="current + 1"></span> / <span x-text="photos.length"></span>
                    </span>
                </template>
                <template x-if="current === 0 && hasPhotos">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(124,58,237,0.7)">🏠 Eksterior</span>
                </template>
                <template x-if="current > 0">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(59,130,246,0.7)">🪑 Interior</span>
                </template>
            </div>

            {{-- Status badge --}}
            <div class="absolute top-3 right-3">
                @if($vehicle->status === 'available')
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(16,185,129,0.85);backdrop-filter:blur(8px)">✅ Tersedia Sekarang</span>
                @elseif($vehicle->status === 'rented')
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(249,115,22,0.85);backdrop-filter:blur(8px)">🕐 Sedang Disewa</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(107,114,128,0.85);backdrop-filter:blur(8px)">🔧 Perawatan</span>
                @endif
            </div>
        </div>

        <template x-if="photos.length > 1">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <template x-for="(photo, i) in photos" :key="i">
                    <button @click="goTo(i)" class="flex-shrink-0 w-16 h-16 rounded-2xl overflow-hidden border-2 transition-all hover:scale-105"
                            :class="current === i ? 'border-purple-500 shadow-lg shadow-purple-500/30' : 'border-transparent opacity-60 hover:opacity-90'">
                        <img :src="photo" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </template>

        {{-- Lightbox --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.92)"
             @click.self="open = false" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="relative w-full max-w-4xl" @keydown.escape.window="open = false">
                <img :src="photos[current]" class="w-full max-h-[80vh] object-contain rounded-2xl">
                <button @click="open = false" class="absolute -top-4 -right-4 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition text-xl">✕</button>
                <template x-if="photos.length > 1">
                    <div>
                        <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center text-white text-2xl">‹</button>
                        <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center text-white text-2xl">›</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===== DATE AVAILABILITY SECTION ===== --}}
        <div class="glass-card rounded-3xl p-5 mt-4">
            <h3 class="font-black text-gray-900 text-base mb-4 flex items-center gap-2">
                📅 Ketersediaan Tanggal
            </h3>

            @if($vehicle->status === 'available' && $bookedRanges->isEmpty())
                {{-- Fully available --}}
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-xl flex-shrink-0">✅</div>
                    <div>
                        <p class="font-bold text-emerald-800 text-sm">Tersedia untuk semua tanggal!</p>
                        <p class="text-emerald-600 text-xs mt-0.5">Kendaraan ini belum memiliki pemesanan. Langsung pesan tanggal yang kamu mau.</p>
                    </div>
                </div>
            @else
                {{-- Show booked ranges + gaps --}}
                @if($vehicle->status === 'rented' || $bookedRanges->isNotEmpty())
                    <div class="space-y-2 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Sudah Dipesan</p>
                        @foreach($bookedRanges as $range)
                            <div class="flex items-center gap-3 p-3 rounded-2xl border
                                {{ $range->status === 'active' ? 'bg-red-50 border-red-200' : ($range->status === 'confirmed' ? 'bg-orange-50 border-orange-200' : 'bg-yellow-50 border-yellow-200') }}">
                                <span class="text-lg flex-shrink-0">
                                    {{ $range->status === 'active' ? '🔴' : ($range->status === 'confirmed' ? '🟠' : '🟡') }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($range->start_date)->format('d M Y') }}
                                        &nbsp;→&nbsp;
                                        {{ \Carbon\Carbon::parse($range->end_date)->format('d M Y') }}
                                    </p>
                                    <p class="text-xs mt-0.5
                                        {{ $range->status === 'active' ? 'text-red-600' : ($range->status === 'confirmed' ? 'text-orange-600' : 'text-yellow-700') }}">
                                        {{ $range->status === 'active' ? 'Sedang aktif disewa' : ($range->status === 'confirmed' ? 'Sudah dikonfirmasi' : 'Menunggu konfirmasi') }}
                                        &nbsp;·&nbsp;
                                        {{ \Carbon\Carbon::parse($range->start_date)->diffInDays(\Carbon\Carbon::parse($range->end_date)) }} hari
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Next available --}}
                    @if($nextAvailableDate)
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-blue-50 border border-blue-200">
                            <span class="text-xl flex-shrink-0">📅</span>
                            <div>
                                <p class="font-bold text-blue-800 text-sm">Tersedia kembali mulai</p>
                                <p class="text-blue-600 font-black text-lg">{{ $nextAvailableDate->format('d F Y') }}</p>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- If there are gaps before the next booking --}}
                @php
                    $today = \Carbon\Carbon::today();
                    $firstBooking = $bookedRanges->sortBy('start_date')->first();
                    $hasGapNow = $firstBooking && $today->lt(\Carbon\Carbon::parse($firstBooking->start_date));
                @endphp
                @if($hasGapNow)
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-50 border border-emerald-200 mt-2">
                        <span class="text-xl flex-shrink-0">✅</span>
                        <div>
                            <p class="font-bold text-emerald-800 text-sm">Tersedia sekarang hingga</p>
                            <p class="text-emerald-600 font-black text-lg">
                                {{ \Carbon\Carbon::parse($firstBooking->start_date)->subDay()->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-3 mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-medium">Keterangan:</p>
                <span class="flex items-center gap-1 text-xs text-gray-500">🔴 Aktif disewa</span>
                <span class="flex items-center gap-1 text-xs text-gray-500">🟠 Dikonfirmasi</span>
                <span class="flex items-center gap-1 text-xs text-gray-500">🟡 Pending</span>
            </div>
        </div>
    </div>

    {{-- ===== VEHICLE INFO ===== --}}
    <div class="lg:col-span-2 space-y-4 animate-slide-up animation-delay-100">
        <div class="glass-card rounded-3xl p-5">
            <h1 class="text-2xl font-black text-gray-900">{{ $vehicle->name }}</h1>
            <div class="flex items-center gap-2 mt-1.5 mb-3">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100">{{ $vehicle->type }}</span>
                <span class="text-gray-400 text-xs font-mono">{{ $vehicle->plate_number }}</span>
            </div>

            @if($vehicle->description)
                <p class="text-gray-600 text-sm leading-relaxed border-t border-gray-100 pt-3">{{ $vehicle->description }}</p>
            @endif

            <div class="mt-4 rounded-2xl p-4 text-center" style="background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(59,130,246,0.08));border:1px solid rgba(139,92,246,0.15)">
                <p class="text-xs text-gray-400 mb-0.5">Harga sewa per hari</p>
                <p class="font-black gradient-text text-3xl">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</p>
            </div>

            {{-- CTA --}}
            <div class="mt-4">
                @if($isMaintenance)
                    <div class="w-full py-3.5 rounded-2xl text-base font-black text-gray-400 text-center bg-gray-100 cursor-not-allowed">
                        🔧 Sedang Dalam Perawatan
                    </div>
                    <p class="text-center text-xs text-gray-400 mt-2">Kendaraan tidak dapat dipesan saat ini.</p>
                @else
                    <a href="{{ route('customer.bookings.create', $vehicle) }}"
                       class="ripple block w-full py-3.5 rounded-2xl text-base font-black text-white text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                       style="background:linear-gradient(135deg,#7C3AED,#2563EB);box-shadow:0 8px 32px rgba(124,58,237,0.3)">
                        🚗 Pesan Kendaraan
                    </a>
                    @if($isRented && $nextAvailableDate)
                        <p class="text-center text-xs text-orange-600 font-semibold mt-2">
                            📅 Pilih tanggal mulai {{ $nextAvailableDate->format('d M Y') }} atau cek kalender
                        </p>
                    @endif
                @endif
            </div>
        </div>

        @if($hasPhotos && count($allPhotos) > 1)
            <div class="glass-card rounded-2xl p-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">📸 Galeri Foto</p>
                <div class="flex items-center gap-3 text-sm">
                    <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-purple-500"></span>1 Eksterior</span>
                    <span class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-blue-500"></span>{{ count($allPhotos) - 1 }} Interior</span>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Klik foto untuk zoom</p>
            </div>
        @endif

        <div class="glass-card rounded-3xl p-5">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">📋 Spesifikasi</p>
            <div class="space-y-2.5">
                @foreach([
                    ['label'=>'Nama','value'=>$vehicle->name,'icon'=>'🚗'],
                    ['label'=>'Tipe','value'=>$vehicle->type,'icon'=>'🏷️'],
                    ['label'=>'Plat Nomor','value'=>$vehicle->plate_number,'icon'=>'🔢'],
                    ['label'=>'Harga/Hari','value'=>'Rp '.number_format($vehicle->price_per_day,0,',','.'),'icon'=>'💰'],
                ] as $spec)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <span class="flex items-center gap-2 text-xs text-gray-500"><span>{{ $spec['icon'] }}</span>{{ $spec['label'] }}</span>
                        <span class="text-xs font-bold text-gray-900 font-mono">{{ $spec['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Similar Vehicles --}}
@if($similarVehicles->count() > 0)
    <div class="mt-10 animate-slide-up animation-delay-200">
        <h3 class="text-lg font-black text-gray-900 mb-4">Kendaraan Serupa 🚗</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($similarVehicles as $sv)
                <a href="{{ route('customer.vehicles.show', $sv) }}" class="group glass-card rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    @if($sv->photo)
                        <img src="{{ asset('storage/'.$sv->photo) }}" class="w-full h-32 object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <div class="h-32 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center text-4xl">🚗</div>
                    @endif
                    <div class="p-3">
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-gray-900 text-sm">{{ $sv->name }}</p>
                            @if($sv->status === 'rented')
                                <span class="px-1.5 py-0.5 rounded-full text-xs font-bold text-orange-600 bg-orange-50">🕐</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded-full text-xs font-bold text-emerald-600 bg-emerald-50">✅</span>
                            @endif
                        </div>
                        <p class="text-purple-600 font-black text-sm mt-0.5">Rp {{ number_format($sv->price_per_day, 0, ',', '.') }}<span class="text-gray-400 font-normal text-xs">/hari</span></p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
@endsection
