@extends('layouts.admin')

@section('title', 'Detail Pelanggan: ' . $user->name)

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.customers.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali</a>
    <h2 class="text-xl font-bold text-gray-900 mt-1">Detail Pelanggan</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-2">👤</div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $user->name }}</h3>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
            </div>
            <div class="space-y-3 text-sm border-t pt-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">Telepon</span>
                    <span class="text-gray-900">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">No. KTP</span>
                    <span class="text-gray-900 font-mono text-xs">{{ $user->id_card_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Alamat</span>
                    <p class="text-gray-900 mt-1">{{ $user->address ?? '-' }}</p>
                </div>
                <div class="flex justify-between pt-2 border-t">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Riwayat Pemesanan ({{ $bookingCount }})</h3>
            </div>
            @if($bookingCount > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-5 py-3 text-left">Kendaraan</th>
                                <th class="px-5 py-3 text-left">Tanggal</th>
                                <th class="px-5 py-3 text-left">Total</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($user->bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $booking->vehicle->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 text-xs">{{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3">@include('partials.booking-status-badge', ['status' => $booking->status])</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline text-xs">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 text-gray-400">Belum ada pemesanan.</div>
            @endif
        </div>
    </div>
</div>
@endsection
