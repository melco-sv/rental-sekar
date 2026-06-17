@extends('layouts.admin')

@section('title', 'Manajemen Pemesanan')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Manajemen Pemesanan</h2>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..."
               class="flex-1 min-w-48 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Semua Status</option>
            @foreach(['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'active' => 'Aktif', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.bookings.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Pelanggan</th>
                    <th class="px-5 py-3 text-left">Kendaraan</th>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-left">Total</th>
                    <th class="px-5 py-3 text-left">Pembayaran</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $booking->user->email }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            <p>{{ $booking->vehicle->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $booking->vehicle->plate_number }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $booking->start_date->format('d/m/Y') }}<br>→ {{ $booking->end_date->format('d/m/Y') }}<br>
                            <span class="text-gray-400">{{ $booking->total_days }} hari</span>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            @if($booking->payment)
                                @include('partials.payment-status-badge', ['status' => $booking->payment->status])
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @include('partials.booking-status-badge', ['status' => $booking->status])
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada pemesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
