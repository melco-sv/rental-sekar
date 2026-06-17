@extends('layouts.customer')
@section('title', 'Cari Kendaraan')

@section('content')
<div class="mb-6 animate-slide-up">
    <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Cari Kendaraan 🚗</h2>
    <p class="text-gray-500 mt-1 text-sm">Temukan kendaraan yang pas buat perjalananmu</p>
</div>

{{-- Search & Filter --}}
<form method="GET" id="filterForm" class="glass-card rounded-3xl p-4 mb-6 animate-slide-up animation-delay-100">
    <div class="flex flex-col gap-3">
        {{-- Row 1: Search + Type --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-purple-400">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama kendaraan..."
                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
            </div>
            <select name="type" class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all">
                <option value="">Semua Tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        {{-- Row 2: Date range filter --}}
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1">📅 Tersedia mulai</label>
                <input type="date" name="filter_start" id="filter_start"
                       value="{{ $filterStart }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1">📅 Hingga</label>
                <input type="date" name="filter_end" id="filter_end"
                       value="{{ $filterEnd }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all">
            </div>
            <div class="flex gap-2 sm:self-end sm:pb-0">
                <button type="submit"
                        class="ripple px-5 py-3 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 whitespace-nowrap"
                        style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                    Cari ✨
                </button>
                @if(request()->hasAny(['search', 'type', 'filter_start', 'filter_end']))
                    <a href="{{ route('customer.vehicles.index') }}"
                       class="px-4 py-3 rounded-2xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Info banner saat filter tanggal aktif --}}
@if($filterStart && $filterEnd)
    <div class="bg-purple-50 border border-purple-200 rounded-2xl px-4 py-3 mb-5 flex items-center gap-3 animate-fade-in">
        <span class="text-xl flex-shrink-0">✅</span>
        <p class="text-purple-800 text-xs sm:text-sm font-medium">
            Menampilkan kendaraan yang <strong>tersedia</strong> untuk
            <strong>{{ \Carbon\Carbon::parse($filterStart)->format('d M Y') }}</strong>
            hingga <strong>{{ \Carbon\Carbon::parse($filterEnd)->format('d M Y') }}</strong>
            ({{ \Carbon\Carbon::parse($filterStart)->diffInDays(\Carbon\Carbon::parse($filterEnd)) }} hari)
        </p>
    </div>
@endif

{{-- Grid --}}
@if($vehicles->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        @foreach($vehicles as $i => $vehicle)
            @php
                // Hitung next available hanya saat mode default (ada relasi bookings)
                $nextAvailable = null;
                if (!$filterStart && $vehicle->relationLoaded('bookings') && $vehicle->status === 'rented') {
                    $activeBooking = $vehicle->bookings
                        ->whereIn('status', ['active', 'confirmed'])
                        ->sortByDesc('end_date')
                        ->first();
                    if ($activeBooking) {
                        $nextAvailable = \Carbon\Carbon::parse($activeBooking->end_date)->addDay();
                    }
                }
            @endphp

            <div class="vehicle-card glass-card rounded-3xl overflow-hidden animate-slide-up group"
                 style="animation-delay: {{ ($i % 6) * 80 }}ms">

                {{-- Photo --}}
                <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="block">
                    @if($vehicle->photo)
                        <div class="h-44 overflow-hidden relative">
                            <img src="{{ asset('storage/'.$vehicle->photo) }}" alt="{{ $vehicle->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            @if(count($vehicle->allPhotos()) > 1)
                                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-xs font-bold text-white" style="background:rgba(0,0,0,0.5);">
                                    📸 {{ count($vehicle->allPhotos()) }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="h-44 flex items-center justify-center relative overflow-hidden"
                             style="background: linear-gradient(135deg, #EDE9FE, #DBEAFE);">
                            <span class="text-6xl animate-float">🚗</span>
                        </div>
                    @endif
                </a>

                {{-- Info --}}
                <div class="p-5">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-black text-gray-900 text-base leading-tight">{{ $vehicle->name }}</h3>

                        {{-- Badge: hanya tersedia (hijau) atau tidak ada badge --}}
                        @if($filterStart)
                            {{-- Filter tanggal aktif: semua yang tampil pasti tersedia --}}
                            <span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">✅ Tersedia</span>
                        @elseif($vehicle->status === 'available')
                            <span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">✅ Tersedia</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium text-purple-600 bg-purple-50">{{ $vehicle->type }}</span>
                        <span class="text-gray-400 text-xs font-mono">{{ $vehicle->plate_number }}</span>
                    </div>

                    {{-- Info tersedia mulai (hanya saat tidak ada filter tanggal & kendaraan sedang disewa) --}}
                    @if(!$filterStart && $nextAvailable)
                        <div class="flex items-center gap-1.5 mb-2 px-2.5 py-1.5 rounded-xl bg-blue-50 border border-blue-100">
                            <span class="text-xs">📅</span>
                            <span class="text-xs text-blue-700 font-semibold">
                                Tersedia mulai {{ $nextAvailable->format('d M Y') }}
                            </span>
                        </div>
                    @endif

                    @if($vehicle->description)
                        <p class="text-gray-400 text-xs line-clamp-2 mb-3">{{ $vehicle->description }}</p>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400">per hari</p>
                            <p class="font-black text-purple-600 text-lg">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('customer.vehicles.show', $vehicle) }}"
                               class="px-3 py-2 rounded-xl text-xs font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 transition-all border border-purple-100">
                                Detail
                            </a>
                            <a href="{{ route('customer.bookings.create', $vehicle) }}"
                               class="ripple px-4 py-2 rounded-2xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5"
                               style="background: linear-gradient(135deg, #7C3AED, #2563EB);">
                                Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="animate-fade-in">{{ $vehicles->links() }}</div>
@else
    <div class="glass-card rounded-3xl text-center py-20 animate-fade-in">
        @if($filterStart && $filterEnd)
            <div class="text-6xl mb-4">📅</div>
            <p class="font-bold text-gray-700 text-lg mb-1">Tidak ada kendaraan tersedia</p>
            <p class="text-gray-400 text-sm mb-4">
                Tidak ada unit yang kosong untuk
                {{ \Carbon\Carbon::parse($filterStart)->format('d M') }} – {{ \Carbon\Carbon::parse($filterEnd)->format('d M Y') }}.
                Coba pilih rentang tanggal lain.
            </p>
        @else
            <div class="text-6xl mb-4 animate-float">🔍</div>
            <p class="font-bold text-gray-700 text-lg mb-1">Tidak ada kendaraan ditemukan</p>
            <p class="text-gray-400 text-sm mb-4">Coba ubah filter pencarian kamu</p>
        @endif
        <a href="{{ route('customer.vehicles.index') }}" class="text-purple-600 font-semibold hover:underline text-sm">Reset pencarian</a>
    </div>
@endif
@endsection

@push('scripts')
<script>
// Validasi: jika salah satu tanggal diisi, keduanya harus ada sebelum submit
const form        = document.getElementById('filterForm');
const startInput  = document.getElementById('filter_start');
const endInput    = document.getElementById('filter_end');

// Saat start berubah, update min end date
startInput.addEventListener('change', function () {
    if (this.value) {
        const next = new Date(this.value);
        next.setDate(next.getDate() + 1);
        endInput.min = next.toISOString().split('T')[0];
        if (endInput.value && endInput.value <= this.value) {
            endInput.value = '';
        }
    }
});

form.addEventListener('submit', function (e) {
    const s = startInput.value;
    const en = endInput.value;
    // Jika hanya satu diisi → kosongkan keduanya agar tidak dikirim setengah
    if ((s && !en) || (!s && en)) {
        e.preventDefault();
        startInput.value = '';
        endInput.value   = '';
        // Submit lagi tanpa filter tanggal
        form.submit();
    }
});
</script>
@endpush
