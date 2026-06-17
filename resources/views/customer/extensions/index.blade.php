@extends('layouts.customer')
@section('title', 'Perpanjangan Sewa')

@section('content')
<div class="mb-6 animate-slide-up">
    <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Perpanjangan Sewa ⏰</h2>
    <p class="text-gray-500 mt-1 text-sm">Perpanjang masa sewa kendaraan yang sedang aktif</p>
</div>

{{-- ===== UNIT YANG BISA DIPERPANJANG ===== --}}
<div class="mb-8">
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Sewa Aktif Kamu</h3>

    @if($activeBookings->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($activeBookings as $booking)
                @php
                    $hoursLeft   = floor(abs($booking->mins_left) / 60);
                    $minutesLeft = abs($booking->mins_left) % 60;
                    $daysLeft    = $booking->end_date->diffInDays(now(), false);
                @endphp

                <div class="glass-card rounded-3xl overflow-hidden transition-all duration-300
                            {{ $booking->can_extend ? 'hover:-translate-y-1 hover:shadow-lg' : 'opacity-75' }}
                            animate-slide-up">

                    {{-- Vehicle photo strip --}}
                    <div class="relative h-32 overflow-hidden">
                        @if($booking->vehicle->photo)
                            <img src="{{ asset('storage/'.$booking->vehicle->photo) }}"
                                 alt="{{ $booking->vehicle->name }}"
                                 class="w-full h-full object-cover {{ !$booking->can_extend ? 'grayscale' : '' }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #EDE9FE, #DBEAFE);">
                                <span class="text-5xl">🚗</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                        {{-- Days left badge --}}
                        <div class="absolute top-3 right-3">
                            @php $absdays = abs((int)$daysLeft); @endphp
                            @if($daysLeft <= 0)
                                {{-- Masih aktif, berakhir hari ini atau mendatang --}}
                                @php $realDaysLeft = $booking->end_date->diffInDays(now(), false) * -1; @endphp
                                @if($realDaysLeft <= 1)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black text-white" style="background:rgba(239,68,68,0.85)">
                                        🔥 Berakhir hari ini
                                    </span>
                                @elseif($realDaysLeft <= 3)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black text-white" style="background:rgba(249,115,22,0.85)">
                                        ⏳ {{ (int)$realDaysLeft }} hari lagi
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(16,185,129,0.85)">
                                        {{ (int)$realDaysLeft }} hari lagi
                                    </span>
                                @endif
                            @endif
                        </div>

                        {{-- Vehicle name overlay --}}
                        <div class="absolute bottom-3 left-4">
                            <p class="text-white font-black text-base leading-tight">{{ $booking->vehicle->name }}</p>
                            <p class="text-white/70 text-xs">{{ $booking->vehicle->type }} · {{ $booking->vehicle->plate_number }}</p>
                        </div>
                    </div>

                    {{-- Info & action --}}
                    <div class="p-4">
                        {{-- Booking dates --}}
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                            <span>📅</span>
                            <span class="font-medium text-gray-700">{{ $booking->start_date->format('d M Y') }}</span>
                            <span class="text-gray-300">→</span>
                            <span class="font-medium text-gray-700">{{ $booking->end_date->format('d M Y') }}</span>
                            <span class="text-gray-400">({{ $booking->total_days }} hari)</span>
                        </div>

                        {{-- Harga per hari --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-400">Harga perpanjangan</p>
                                <p class="font-black gradient-text text-base">Rp {{ number_format($booking->vehicle->price_per_day, 0, ',', '.') }}<span class="text-xs text-gray-400 font-normal">/hari</span></p>
                            </div>
                            <a href="{{ route('customer.bookings.show', $booking) }}" class="text-xs text-purple-500 hover:text-purple-700 font-semibold">
                                Detail pemesanan →
                            </a>
                        </div>

                        {{-- Status & tombol aksi --}}
                        @if($booking->has_pending)
                            {{-- Ada pengajuan pending --}}
                            <div class="flex items-center gap-2 p-3 rounded-2xl bg-amber-50 border border-amber-200">
                                <span class="text-lg">⏳</span>
                                <div>
                                    <p class="text-xs font-bold text-amber-800">Pengajuan sedang diproses</p>
                                    <p class="text-xs text-amber-600">Tunggu konfirmasi dari admin</p>
                                </div>
                            </div>

                        @elseif(!$booking->can_extend && $booking->mins_left <= 0)
                            {{-- Lewat deadline --}}
                            <div class="flex items-center gap-2 p-3 rounded-2xl bg-gray-50 border border-gray-200">
                                <span class="text-lg">🔒</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-600">Waktu pengajuan habis</p>
                                    <p class="text-xs text-gray-400">Melewati batas 2 jam sebelum selesai</p>
                                </div>
                            </div>

                        @else
                            {{-- Bisa diperpanjang --}}
                            @if($booking->mins_left <= 120 && $booking->mins_left > 0)
                                <div class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mb-2">
                                    <span>⚠️</span>
                                    <span>Segera! Batas pengajuan {{ $hoursLeft > 0 ? $hoursLeft.'j ' : '' }}{{ $minutesLeft }}m lagi</span>
                                </div>
                            @elseif($booking->mins_left > 0)
                                <p class="text-xs text-gray-400 mb-2">
                                    Batas: {{ $booking->extension_deadline->format('d M Y H:i') }}
                                </p>
                            @endif

                            <a href="{{ route('customer.extensions.create', $booking) }}"
                               class="ripple block w-full text-center py-3 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                               style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 16px rgba(16,185,129,0.3);">
                                ⏰ Perpanjang Sewa Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Tidak ada booking aktif --}}
        <div class="glass-card rounded-3xl text-center py-14 animate-fade-in">
            <div class="text-5xl mb-3 animate-float">🚗</div>
            <p class="font-bold text-gray-700 text-lg mb-1">Tidak ada sewa aktif</p>
            <p class="text-gray-400 text-sm mb-4">Kamu belum memiliki kendaraan yang sedang disewa saat ini.</p>
            <a href="{{ route('customer.vehicles.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
               style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                🚗 Cari Kendaraan
            </a>
        </div>
    @endif
</div>

{{-- ===== RIWAYAT PERPANJANGAN ===== --}}
@if($extensions->count() > 0)
    <div class="animate-slide-up animation-delay-200">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Riwayat Perpanjangan</h3>

        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-5 py-3 text-left">Kendaraan</th>
                            <th class="px-5 py-3 text-left">Tambahan</th>
                            <th class="px-5 py-3 text-left">Biaya</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($extensions as $ext)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-bold text-gray-900 text-sm">{{ $ext->booking->vehicle->name }}</p>
                                    <a href="{{ route('customer.bookings.show', $ext->booking) }}"
                                       class="text-purple-500 hover:text-purple-700 text-xs font-medium">
                                        Pemesanan #{{ $ext->booking->id }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-700 font-medium">{{ $ext->additional_days }} hari</td>
                                <td class="px-5 py-3 font-bold text-gray-900">Rp {{ number_format($ext->additional_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $colors = ['pending'=>'bg-amber-100 text-amber-700','confirmed'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                                        $labels = ['pending'=>'⏳ Menunggu','confirmed'=>'✅ Dikonfirmasi','rejected'=>'❌ Ditolak'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $colors[$ext->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $labels[$ext->status] ?? ucfirst($ext->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $ext->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($extensions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $extensions->links() }}
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
