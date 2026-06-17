@extends('layouts.customer')
@section('title', 'Dashboard')

@section('content')
{{-- Header --}}
<div class="mb-8 animate-slide-up">
    <p class="text-xs font-semibold text-purple-500 uppercase tracking-widest mb-1">{{ now()->format('l, d F Y') }}</p>
    <h2 class="text-2xl sm:text-3xl font-black text-gray-900">
        Halo, <span class="gradient-text">{{ explode(' ', auth()->user()->name)[0] }}</span> 👋
    </h2>
    <p class="text-gray-500 mt-1 text-sm">Semangat hari ini! Cek aktivitas sewa kendaraanmu.</p>
</div>

{{-- Stat Cards — selalu 3 kolom (compact di mobile) --}}
@php
    $statCards = [
        ['value'=>$activeBookings,   'label'=>'Aktif',    'labelFull'=>'Pemesanan Aktif',      'icon'=>'🚗', 'bg'=>'linear-gradient(135deg,#7C3AED,#6D28D9)', 'shadow'=>'rgba(124,58,237,0.35)'],
        ['value'=>$pendingPayments,  'label'=>'Verifikasi','labelFull'=>'Menunggu Verifikasi',  'icon'=>'⏳', 'bg'=>'linear-gradient(135deg,#F59E0B,#D97706)', 'shadow'=>'rgba(245,158,11,0.35)'],
        ['value'=>$completedBookings,'label'=>'Selesai',  'labelFull'=>'Selesai',              'icon'=>'✅', 'bg'=>'linear-gradient(135deg,#10B981,#059669)', 'shadow'=>'rgba(16,185,129,0.35)'],
    ];
@endphp
<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
    @foreach($statCards as $i => $s)
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl p-3 sm:p-5 text-white card-hover animate-slide-up"
             style="animation-delay: {{ $i*100 }}ms; background: {{ $s['bg'] }}; box-shadow: 0 8px 24px {{ $s['shadow'] }};">
            {{-- Background icon (tersembunyi di mobile kecil) --}}
            <div class="absolute -right-3 -top-3 text-4xl sm:text-5xl opacity-20 hidden sm:block">{{ $s['icon'] }}</div>

            {{-- Nilai --}}
            <p class="text-2xl sm:text-4xl font-black leading-none mb-0.5 sm:mb-1">{{ $s['value'] }}</p>

            {{-- Label pendek di mobile, label panjang di sm+ --}}
            <p class="text-xs sm:hidden font-semibold opacity-90 leading-tight">{{ $s['label'] }}</p>
            <p class="text-sm font-bold opacity-90 hidden sm:block">{{ $s['labelFull'] }}</p>

            {{-- Icon di bawah hanya sm+ --}}
            <div class="text-2xl mt-2 opacity-80 hidden sm:block">{{ $s['icon'] }}</div>
        </div>
    @endforeach
</div>

{{-- Quick Actions --}}
<div class="glass-card rounded-3xl p-5 mb-8 animate-slide-up animation-delay-300">
    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Aksi Cepat ⚡</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['href'=>route('customer.vehicles.index'),'icon'=>'🚗','label'=>'Cari Kendaraan','grad'=>'from-violet-500 to-purple-600'],
            ['href'=>route('customer.bookings.index'),'icon'=>'📋','label'=>'Riwayat Sewa','grad'=>'from-blue-500 to-cyan-500'],
            ['href'=>route('customer.extensions.index'),'icon'=>'⏰','label'=>'Perpanjangan','grad'=>'from-amber-400 to-orange-500'],
            ['href'=>route('customer.profile.edit'),'icon'=>'👤','label'=>'Edit Profil','grad'=>'from-pink-500 to-rose-500'],
        ] as $action)
            <a href="{{ $action['href'] }}" class="group flex flex-col items-center gap-2 p-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg text-center"
               style="background: linear-gradient(135deg, rgba(139,92,246,0.05), rgba(59,130,246,0.05)); border: 1px solid rgba(139,92,246,0.1);">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl text-white transition-transform duration-300 group-hover:scale-110 bg-gradient-to-br {{ $action['grad'] }}">
                    {{ $action['icon'] }}
                </div>
                <span class="text-xs font-semibold text-gray-700">{{ $action['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

{{-- Recent Bookings --}}
<div class="glass-card rounded-3xl overflow-hidden animate-slide-up animation-delay-400">
    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
        <div>
            <h3 class="font-black text-gray-900">Pemesanan Terbaru</h3>
            <p class="text-xs text-gray-400 mt-0.5">Aktivitas sewa terakhirmu</p>
        </div>
        <a href="{{ route('customer.bookings.index') }}" class="text-xs font-bold text-purple-600 hover:text-purple-800 transition-colors flex items-center gap-1">
            Lihat semua <span>→</span>
        </a>
    </div>

    @if($recentBookings->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($recentBookings as $booking)
                <a href="{{ route('customer.bookings.show', $booking) }}"
                   class="flex items-center gap-4 px-6 py-4 hover:bg-purple-50/50 transition-colors group">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl flex-shrink-0"
                         style="background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(59,130,246,0.1));">🚗</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $booking->vehicle->name }}</p>
                        <p class="text-gray-400 text-xs mt-0.5">
                            {{ $booking->start_date->format('d M') }} → {{ $booking->end_date->format('d M Y') }}
                            · {{ $booking->total_days }} hari
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-gray-900 text-sm">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        @php
                            $statusConfig = [
                                'pending'   => ['text'=>'Menunggu','class'=>'text-amber-700 bg-amber-50 border-amber-200'],
                                'confirmed' => ['text'=>'Dikonfirmasi','class'=>'text-blue-700 bg-blue-50 border-blue-200'],
                                'active'    => ['text'=>'Aktif','class'=>'text-emerald-700 bg-emerald-50 border-emerald-200'],
                                'completed' => ['text'=>'Selesai','class'=>'text-gray-700 bg-gray-50 border-gray-200'],
                                'cancelled' => ['text'=>'Dibatalkan','class'=>'text-red-700 bg-red-50 border-red-200'],
                            ];
                            $sc = $statusConfig[$booking->status] ?? ['text'=>ucfirst($booking->status),'class'=>'text-gray-700 bg-gray-50 border-gray-200'];
                        @endphp
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-bold border {{ $sc['class'] }}">{{ $sc['text'] }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-purple-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-14">
            <div class="text-5xl mb-3 animate-float">🚗</div>
            <p class="font-bold text-gray-700 mb-1">Belum ada pemesanan</p>
            <p class="text-gray-400 text-sm mb-4">Yuk, mulai sewa kendaraan pertamamu!</p>
            <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
               style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                🚗 Lihat Kendaraan
            </a>
        </div>
    @endif
</div>
@endsection
