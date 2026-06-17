@extends('layouts.admin')

@section('title', 'Manajemen Perpanjangan Sewa')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Manajemen Perpanjangan Sewa</h2>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex gap-3">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Pelanggan</th>
                    <th class="px-5 py-3 text-left">Kendaraan</th>
                    <th class="px-5 py-3 text-left">Tambahan</th>
                    <th class="px-5 py-3 text-left">Biaya Tambahan</th>
                    <th class="px-5 py-3 text-left">Bukti</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($extensions as $ext)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $ext->booking->user->name }}</p>
                            <a href="{{ route('admin.bookings.show', $ext->booking) }}" class="text-blue-600 hover:underline text-xs">Pemesanan #{{ $ext->booking_id }}</a>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ $ext->booking->vehicle->name }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $ext->additional_days }} hari</td>
                        <td class="px-5 py-4 font-medium text-gray-900">Rp {{ number_format($ext->additional_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if($ext->payment_proof)
                                <a href="{{ asset('storage/' . $ext->payment_proof) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $ext->payment_proof) }}" class="w-16 h-12 object-cover rounded-lg border hover:opacity-80 transition">
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php $ec = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; $el = ['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','rejected'=>'Ditolak']; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $ec[$ext->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $el[$ext->status] ?? ucfirst($ext->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($ext->status === 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.extensions.confirm', $ext) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-700 transition">Konfirmasi</button>
                                    </form>
                                    <form action="{{ route('admin.extensions.reject', $ext) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Tolak perpanjangan ini?')">
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
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada pengajuan perpanjangan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $extensions->links() }}
    </div>
</div>
@endsection
