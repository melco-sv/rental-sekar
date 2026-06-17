@extends('layouts.owner')

@section('title', 'Dashboard Owner')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Dashboard Keuangan</h2>
    <p class="text-gray-500 text-sm mt-1">Ringkasan pendapatan Rental Mobil Sekar — {{ now()->format('d F Y') }}</p>
</div>

{{-- Revenue Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">📅</div>
            <div>
                <p class="text-xs text-gray-500">Pendapatan Hari Ini</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">🗓️</div>
            <div>
                <p class="text-xs text-gray-500">Pendapatan Bulan Ini</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-2xl">💰</div>
            <div>
                <p class="text-xs text-gray-500">Pendapatan Tahun {{ now()->year }}</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-3xl font-bold text-indigo-600">{{ $totalBookings }}</p>
        <p class="text-gray-500 text-sm mt-1">Total Sewa Selesai</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $totalActiveRentals }}</p>
        <p class="text-gray-500 text-sm mt-1">Kendaraan Aktif Disewa</p>
    </div>
</div>

{{-- Chart --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Grafik Pendapatan Bulanan {{ now()->year }}</h3>
        <a href="{{ route('owner.reports.index') }}" class="text-indigo-600 text-sm hover:underline">Lihat laporan lengkap</a>
    </div>
    <canvas id="revenueChart" height="100"></canvas>
</div>

{{-- Recent --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Transaksi Terakhir</h3>
    </div>
    @if($recentBookings->count() > 0)
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Kendaraan</th>
                    <th class="px-6 py-3 text-left">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentBookings as $booking)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $booking->user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $booking->vehicle->name }}</td>
                        <td class="px-6 py-3 font-semibold text-indigo-600">Rp {{ number_format(optional($booking->payment)->amount ?? $booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-10 text-gray-400">Belum ada transaksi selesai.</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($monthlyData),
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => 'Rp ' + (val/1000000).toFixed(1) + 'jt'
                    }
                }
            }
        }
    });
</script>
@endpush
