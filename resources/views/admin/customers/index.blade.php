@extends('layouts.admin')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-gray-900">Manajemen Pelanggan</h2>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.customers.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Nama</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Telepon</th>
                    <th class="px-5 py-3 text-left">No. KTP</th>
                    <th class="px-5 py-3 text-left">Total Pemesanan</th>
                    <th class="px-5 py-3 text-left">Bergabung</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $customer->email }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600 font-mono text-xs">{{ $customer->id_card_number ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $customer->bookings_count }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:underline text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Tidak ada pelanggan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
</div>
@endsection
