@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Verifikasi Pembayaran</h2>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
        @if(request('status'))
            <a href="{{ route('admin.payments.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Reset</a>
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
                    <th class="px-5 py-3 text-left">Jumlah</th>
                    <th class="px-5 py-3 text-left">Bukti</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $payment->booking->user->name }}</p>
                            <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-blue-600 hover:underline text-xs">Pemesanan #{{ $payment->booking_id }}</a>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ $payment->booking->vehicle->name }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if($payment->proof_image)
                                <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $payment->proof_image) }}" class="w-16 h-12 object-cover rounded-lg border hover:opacity-80 transition cursor-pointer">
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">Belum diupload</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @include('partials.payment-status-badge', ['status' => $payment->status])
                        </td>
                        <td class="px-5 py-4">
                            @if($payment->status === 'pending' && $payment->proof_image)
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-700 transition">Verifikasi</button>
                                    </form>
                                    <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Tolak pembayaran ini?')">
                                        @csrf
                                        <button class="bg-red-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-600 transition">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">Tidak ada data pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
</div>
@endsection
