@extends('layouts.customer')

@section('title', 'Riwayat Pemesanan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Riwayat Pemesanan</h2>
    <a href="{{ route('customer.vehicles.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">+ Buat Pemesanan</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($bookings->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Kendaraan</th>
                        <th class="px-6 py-3 text-left">Tanggal Sewa</th>
                        <th class="px-6 py-3 text-left">Durasi</th>
                        <th class="px-6 py-3 text-left">Total</th>
                        <th class="px-6 py-3 text-left">Pembayaran</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $booking->vehicle->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $booking->vehicle->plate_number }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $booking->start_date->format('d M Y') }}<br>
                                <span class="text-gray-400 text-xs">s/d {{ $booking->end_date->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $booking->total_days }} hari</td>
                            <td class="px-6 py-4 font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($booking->payment)
                                    @include('partials.payment-status-badge', ['status' => $booking->payment->status])
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @include('partials.booking-status-badge', ['status' => $booking->status])
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customer.bookings.show', $booking) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-3">📋</p>
            <p class="text-lg">Belum ada riwayat pemesanan.</p>
            <a href="{{ route('customer.vehicles.index') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Buat pemesanan pertama Anda</a>
        </div>
    @endif
</div>
@endsection
