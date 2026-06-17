@extends('layouts.admin')

@section('title', 'Pengembalian Dana')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Pengembalian Dana</h2>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 text-green-700 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-red-700 text-sm">{{ session('error') }}</div>
@endif

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Diproses</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
        @if(request('status'))
            <a href="{{ route('admin.refunds.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Reset</a>
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
                    <th class="px-5 py-3 text-left">Dibayar</th>
                    <th class="px-5 py-3 text-left">Persentase</th>
                    <th class="px-5 py-3 text-left">Nominal Refund</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($refunds as $refund)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $refund->booking->user->name }}</p>
                            <a href="{{ route('admin.bookings.show', $refund->booking) }}" class="text-blue-600 hover:underline text-xs">Pemesanan #{{ $refund->booking_id }}</a>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ $refund->booking->vehicle->name }}</td>
                        <td class="px-5 py-4 text-gray-700">Rp {{ number_format($refund->amount_paid, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $refund->refund_percentage }}%</td>
                        <td class="px-5 py-4 font-bold text-red-600">Rp {{ number_format($refund->refund_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if($refund->status === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Menunggu Diproses</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($refund->status === 'completed')
                                <div class="flex items-center gap-2">
                                    @if($refund->proof_image)
                                        <a href="{{ asset('storage/' . $refund->proof_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $refund->proof_image) }}" class="w-14 h-10 object-cover rounded-lg border hover:opacity-80 transition cursor-pointer">
                                        </a>
                                    @endif
                                    <span class="text-gray-400 text-xs">{{ $refund->processed_at?->format('d M Y') }}</span>
                                </div>
                            @else
                                <button type="button"
                                        onclick="document.getElementById('uploadModal{{ $refund->id }}').classList.remove('hidden')"
                                        class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-blue-700 transition">
                                    Upload Bukti Transfer
                                </button>

                                <div id="uploadModal{{ $refund->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6)">
                                    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
                                        <h4 class="font-black text-gray-900 text-base mb-1">Upload Bukti Transfer</h4>
                                        <p class="text-xs text-gray-500 mb-4">
                                            Transfer <strong class="text-red-600">Rp {{ number_format($refund->refund_amount, 0, ',', '.') }}</strong>
                                            ke {{ $refund->booking->user->name }}, lalu upload bukti transfernya.
                                        </p>
                                        <form action="{{ route('admin.refunds.process', $refund) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="proof_image" accept="image/*" required
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs mb-4 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700">
                                            <div class="flex gap-3">
                                                <button type="button"
                                                        onclick="document.getElementById('uploadModal{{ $refund->id }}').classList.add('hidden')"
                                                        class="flex-1 py-2 rounded-xl border border-gray-200 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                                    Batal
                                                </button>
                                                <button type="submit" class="flex-1 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                                                    Simpan & Selesai
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada data pengembalian dana.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $refunds->links() }}
    </div>
</div>
@endsection
