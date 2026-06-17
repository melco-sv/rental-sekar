@extends('layouts.admin')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Laporan Transaksi</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.pdf', request()->query()) }}" target="_blank"
           class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition font-medium">📄 Export PDF</a>
        <a href="{{ route('admin.reports.excel', request()->query()) }}"
           class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition font-medium">📊 Export Excel</a>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bulan</label>
            <select name="month" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tahun</label>
            <select name="year" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
    </div>
</form>

{{-- Summary --}}
@php
    $totalExtensionRevenue = $bookings->sum(fn($b) => $b->payments->where('status','verified')->sum('amount') - ($b->payments->where('status','verified')->sortBy('id')->first()?->amount ?? 0));
    $extensionCount = $bookings->filter(fn($b) => $b->extensions->where('status','confirmed')->count() > 0)->count();
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-gray-500 text-sm">Total Transaksi Selesai</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $bookings->count() }}</p>
        @if($extensionCount > 0)
            <p class="text-xs text-purple-500 mt-1">{{ $extensionCount }} termasuk perpanjangan</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-gray-500 text-sm">Total Pendapatan</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Termasuk semua pembayaran perpanjangan</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-gray-500 text-sm">Avg. per Transaksi</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">
            Rp {{ $bookings->count() > 0 ? number_format($totalRevenue / $bookings->count(), 0, ',', '.') : '0' }}
        </p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">No</th>
                    <th class="px-5 py-3 text-left">Pelanggan</th>
                    <th class="px-5 py-3 text-left">Kendaraan</th>
                    <th class="px-5 py-3 text-left">Tanggal Sewa</th>
                    <th class="px-5 py-3 text-left">Hari</th>
                    <th class="px-5 py-3 text-left">Perpanjangan</th>
                    <th class="px-5 py-3 text-left">Total Dibayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $i => $booking)
                    @php
                        $confirmedExts   = $booking->extensions->where('status', 'confirmed');
                        $extDays         = $confirmedExts->sum('additional_days');
                        $extPrice        = $confirmedExts->sum('additional_price');
                        $verifiedTotal   = $booking->payments->where('status','verified')->sum('amount');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $booking->user->name }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $booking->vehicle->name }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $booking->total_days }} hari</td>
                        <td class="px-5 py-3">
                            @if($confirmedExts->count() > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100">
                                    +{{ $extDays }}h · Rp {{ number_format($extPrice/1000, 0)}}k
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-bold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            @if($verifiedTotal > 0 && $verifiedTotal != $booking->total_price)
                                <p class="text-xs text-gray-400">Dibayar: Rp {{ number_format($verifiedTotal, 0, ',', '.') }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada data transaksi untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
