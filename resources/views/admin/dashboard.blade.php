@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

{{-- ===== STAT CARDS ===== --}}
@php
    $statCards = [
        ['icon'=>'🚗', 'label'=>'Total Kendaraan',   'value'=>$totalVehicles,   'bg'=>'linear-gradient(135deg,#7C3AED,#6D28D9)', 'shadow'=>'rgba(124,58,237,0.35)'],
        ['icon'=>'👥', 'label'=>'Total Pelanggan',    'value'=>$totalCustomers,  'bg'=>'linear-gradient(135deg,#3B82F6,#06B6D4)', 'shadow'=>'rgba(59,130,246,0.35)'],
        ['icon'=>'📋', 'label'=>'Pemesanan Aktif',    'value'=>$activeBookings,  'bg'=>'linear-gradient(135deg,#F59E0B,#EF4444)', 'shadow'=>'rgba(245,158,11,0.35)'],
        ['icon'=>'💰', 'label'=>'Pendapatan Bulan',  'value'=>'Rp '.number_format($monthlyRevenue/1000000,1).'jt', 'bg'=>'linear-gradient(135deg,#10B981,#059669)', 'shadow'=>'rgba(16,185,129,0.35)'],
    ];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($statCards as $i => $s)
        <div class="relative overflow-hidden rounded-3xl p-5 text-white card-hover animate-slide-up"
             style="background: {{ $s['bg'] }}; box-shadow: 0 10px 40px {{ $s['shadow'] }}; animation-delay: {{ $i*80 }}ms;">
            <div class="absolute -right-3 -top-3 text-5xl opacity-20">{{ $s['icon'] }}</div>
            <p class="text-white/70 text-xs font-semibold uppercase tracking-wider mb-2">{{ $s['label'] }}</p>
            <p class="text-2xl sm:text-3xl font-black">{{ $s['value'] }}</p>
            <div class="mt-3 text-2xl">{{ $s['icon'] }}</div>
        </div>
    @endforeach
</div>

{{-- ===== PENDING CARDS — Real-time via AJAX ===== --}}
{{-- Wrapper dengan id — diisi ulang oleh JS saat ada perubahan data --}}
<div id="pending-cards-grid"
     class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6 transition-opacity duration-300">
    @include('admin.partials.pending-cards')
</div>

{{-- ===== QUICK ACTIONS + TODAY STATS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 animate-slide-up animation-delay-400">
        <h3 class="font-black text-gray-900 mb-4">Aksi Cepat ⚡</h3>
        <div class="grid grid-cols-2 gap-3">
            @foreach([
                ['href'=>route('admin.vehicles.create'),                        'icon'=>'➕','label'=>'Tambah Kendaraan', 'grad'=>'#7C3AED,#6D28D9'],
                ['href'=>route('admin.payments.index',['status'=>'pending']),    'icon'=>'💳','label'=>'Verifikasi Bayar', 'grad'=>'#10B981,#059669'],
                ['href'=>route('admin.extensions.index',['status'=>'pending']),  'icon'=>'⏰','label'=>'Konfirm Perpanjangan','grad'=>'#F59E0B,#D97706'],
                ['href'=>route('admin.reports.index'),                           'icon'=>'📈','label'=>'Laporan',          'grad'=>'#3B82F6,#2563EB'],
            ] as $a)
                <a href="{{ $a['href'] }}" class="group flex items-center gap-3 p-3 rounded-2xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md border border-gray-100 hover:border-purple-100">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-lg flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                         style="background: linear-gradient(135deg, {{ $a['grad'] }});">{{ $a['icon'] }}</div>
                    <span class="text-xs font-bold text-gray-700">{{ $a['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-3 animate-slide-up animation-delay-500">
        {{-- Today card --}}
        <div class="rounded-3xl p-5 text-white" style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
            <div class="flex items-center justify-between mb-3">
                <p class="font-black text-lg">📊 Hari Ini</p>
                <span class="text-white/60 text-xs">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center">
                    <p class="text-2xl font-black">{{ $todayBookings }}</p>
                    <p class="text-white/70 text-xs mt-0.5">Pemesanan</p>
                </div>
                <div class="text-center border-x border-white/20">
                    <p class="text-2xl font-black" x-text="counts.payments">{{ $notifCounts['payments'] }}</p>
                    <p class="text-white/70 text-xs mt-0.5">Bayar Pending</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-black" x-text="counts.extensions">{{ $notifCounts['extensions'] }}</p>
                    <p class="text-white/70 text-xs mt-0.5">Perpanjangan</p>
                </div>
            </div>
        </div>

        {{-- Real-time indicator --}}
        <div class="bg-white rounded-2xl px-5 py-3 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse flex-shrink-0"></div>
            <div class="flex-1">
                <p class="text-xs font-bold text-gray-700">Pembaruan Real-Time Aktif</p>
                <p class="text-xs text-gray-400">Data diperbarui otomatis setiap 10 detik</p>
            </div>
            <span class="text-xs text-gray-400 flex-shrink-0">10s</span>
        </div>
    </div>
</div>
@endsection
