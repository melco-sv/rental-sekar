@extends('layouts.admin')

@section('title', 'Manajemen Kendaraan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-900">Manajemen Kendaraan</h2>
    <a href="{{ route('admin.vehicles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition font-medium">+ Tambah Kendaraan</a>
</div>

{{-- Search & Filter --}}
<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / plat nomor..."
               class="flex-1 min-w-48 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.vehicles.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Kendaraan</th>
                    <th class="px-5 py-3 text-left">Tipe</th>
                    <th class="px-5 py-3 text-left">Plat Nomor</th>
                    <th class="px-5 py-3 text-left">Harga/Hari</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50 {{ $vehicle->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($vehicle->photo)
                                    <img src="{{ asset('storage/' . $vehicle->photo) }}" class="w-12 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-12 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-xl">🚗</div>
                                @endif
                                <span class="font-medium text-gray-900">{{ $vehicle->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $vehicle->type }}</td>
                        <td class="px-5 py-4 font-mono text-gray-700">{{ $vehicle->plate_number }}</td>
                        <td class="px-5 py-4 text-gray-900">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if($vehicle->trashed())
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full font-medium">Dihapus</span>
                            @elseif($vehicle->status === 'available')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">Tersedia</span>
                            @elseif($vehicle->status === 'rented')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">Disewa</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">Perawatan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if($vehicle->trashed())
                                    <form action="{{ route('admin.vehicles.restore', $vehicle->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-green-600 hover:underline text-xs">Pulihkan</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus kendaraan {{ $vehicle->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">Tidak ada data kendaraan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
