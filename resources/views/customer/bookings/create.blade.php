@extends('layouts.customer')
@section('title', 'Buat Pemesanan')

@section('content')

{{-- ===== MODAL SYARAT & KETENTUAN ===== --}}
<div id="skModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-slide-up">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100" style="background:linear-gradient(135deg,#7C3AED,#2563EB);">
            <p class="text-white/80 text-xs font-semibold uppercase tracking-wider mb-1">Sebelum melanjutkan</p>
            <h3 class="text-white font-black text-xl">📋 Syarat & Ketentuan Sewa</h3>
        </div>
        <div class="px-6 py-4 max-h-80 overflow-y-auto space-y-4 text-sm text-gray-700">
            <div class="flex gap-3">
                <span class="text-xl flex-shrink-0">🕐</span>
                <div>
                    <p class="font-bold text-gray-900">Perhitungan Waktu Sewa</p>
                    <p class="text-gray-500 text-xs mt-1">Sewa dihitung per 24 jam dari jam pengambilan yang Anda pilih. Contoh: diambil pukul 09:00 → wajib dikembalikan pukul 09:00 hari berikutnya (sesuai jumlah hari).</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="text-xl flex-shrink-0">🚫</span>
                <div>
                    <p class="font-bold text-gray-900">Kebijakan Pembatalan</p>
                    <p class="text-gray-500 text-xs mt-1">Pembatalan <strong>≥ 2 jam</strong> sebelum jam pengambilan: pengembalian dana <strong>50%</strong> dari total pembayaran.</p>
                    <p class="text-gray-500 text-xs mt-1">Pembatalan <strong>< 2 jam</strong> sebelum jam pengambilan: pengembalian dana hanya <strong>30%</strong> dari total pembayaran.</p>
                    <p class="text-gray-500 text-xs mt-1">Pembatalan hanya dapat dilakukan saat status pemesanan <strong>Dikonfirmasi</strong> (sebelum kendaraan diambil).</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="text-xl flex-shrink-0">👨‍✈️</span>
                <div>
                    <p class="font-bold text-gray-900">Driver & Biaya</p>
                    <p class="text-gray-500 text-xs mt-1">Harga sewa sudah <strong>termasuk driver</strong> dan biaya jasa driver. Biaya bahan bakar (bensin) ditanggung sepenuhnya oleh penyewa.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="text-xl flex-shrink-0">⏰</span>
                <div>
                    <p class="font-bold text-gray-900">Keterlambatan Pengembalian</p>
                    <p class="text-gray-500 text-xs mt-1">Keterlambatan pengembalian kendaraan akan dikenakan biaya tambahan sesuai tarif per jam yang berlaku.</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <label class="flex items-start gap-3 cursor-pointer mb-4">
                <input type="checkbox" id="skAgree" class="mt-0.5 w-4 h-4 accent-purple-600 flex-shrink-0">
                <span class="text-xs text-gray-600">Saya telah membaca dan menyetujui seluruh syarat & ketentuan di atas.</span>
            </label>
            <button id="skAgreeBtn" onclick="closeSKModal()" disabled
                    class="w-full py-3 rounded-2xl text-sm font-black text-white transition-all duration-200 opacity-50 cursor-not-allowed"
                    style="background:linear-gradient(135deg,#7C3AED,#2563EB);">
                Setuju & Lanjutkan Pemesanan →
            </button>
        </div>
    </div>
</div>

<div class="mb-6 animate-slide-up">
    <a href="{{ route('customer.vehicles.show', $vehicle) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-purple-500 hover:text-purple-700 transition-colors mb-2">
        ← Kembali ke detail kendaraan
    </a>
    <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Pilih Tanggal Sewa 📅</h2>
    <p class="text-gray-500 text-sm mt-1">Pilih rentang tanggal yang kamu inginkan pada kalender di bawah</p>
</div>

{{-- Hidden actual form inputs --}}
<input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
<input type="hidden" name="end_date"   id="end_date"   value="{{ old('end_date') }}">

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-5 animate-slide-down">
        <div class="flex items-center gap-2 mb-1"><span class="text-lg">⚠️</span><p class="font-bold text-red-700 text-sm">Kesalahan:</p></div>
        <ul class="space-y-0.5">
            @foreach($errors->all() as $e)
                <li class="text-red-600 text-xs ml-7">• {{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-5 flex items-center gap-2 animate-slide-down">
        <span class="text-xl">❌</span>
        <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<form action="{{ route('customer.bookings.store') }}" method="POST" id="bookingForm">
@csrf
<input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
<input type="hidden" name="start_date" id="start_date_input">
<input type="hidden" name="end_date"   id="end_date_input">

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ===== CALENDAR SECTION ===== --}}
    {{-- order-2: di mobile tampil kedua, lg:order-1: di desktop tampil pertama (kiri) --}}
    <div class="order-2 lg:order-1 lg:col-span-3 animate-slide-up animation-delay-100">
        <div class="glass-card rounded-3xl p-5 sm:p-6">

            {{-- Keterangan legenda --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan:</span>
                <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
                    <span class="w-4 h-4 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 inline-block"></span> Dipilih
                </span>
                <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
                    <span class="w-4 h-4 rounded-full bg-purple-100 inline-block border border-purple-300"></span> Rentang
                </span>
                <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
                    <span class="w-4 h-4 rounded-full bg-red-100 inline-block border border-red-300"></span> Sudah dipesan
                </span>
                <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
                    <span class="w-4 h-4 rounded-full bg-gray-100 inline-block border border-gray-300"></span> Tidak tersedia
                </span>
            </div>

            {{-- Flatpickr inline calendar container --}}
            <div id="datepicker-container" class="w-full"></div>

            {{-- Selected range display --}}
            <div id="range-display" class="hidden mt-5 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-2xl p-3 text-center border border-purple-200" style="background: linear-gradient(135deg, rgba(139,92,246,0.05), rgba(59,130,246,0.05))">
                        <p class="text-xs text-gray-400 mb-1">📅 Mulai</p>
                        <p id="display-start" class="font-black text-purple-700 text-sm">-</p>
                    </div>
                    <div class="rounded-2xl p-3 text-center border border-blue-200" style="background: linear-gradient(135deg, rgba(59,130,246,0.05), rgba(6,182,212,0.05))">
                        <p class="text-xs text-gray-400 mb-1">📅 Selesai</p>
                        <p id="display-end" class="font-black text-blue-700 text-sm">-</p>
                    </div>
                </div>

                {{-- Jam pengambilan --}}
                <div class="rounded-2xl p-4 border border-purple-200 mb-3" style="background:linear-gradient(135deg,rgba(139,92,246,0.05),rgba(59,130,246,0.05))">
                    <label class="block text-xs font-bold text-purple-700 uppercase tracking-wider mb-2">🕐 Jam Pengambilan</label>
                    <select name="start_time" id="start_time_input"
                            class="w-full px-4 py-2.5 bg-white border border-purple-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all">
                        <option value="">-- Pilih jam --</option>
                        @foreach(['06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00'] as $t)
                            <option value="{{ $t }}" {{ old('start_time') == $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1.5">Waktu sewa 24 jam dihitung dari jam ini</p>
                </div>

                {{-- Price summary --}}
                <div id="price-summary" class="rounded-2xl p-4 border border-purple-200" style="background: linear-gradient(135deg, rgba(139,92,246,0.08), rgba(59,130,246,0.08))">
                    <p class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-3">💰 Ringkasan Biaya</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Durasi Sewa</span>
                            <span id="calc-days" class="font-bold text-gray-900">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Harga Per Hari</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black pt-2 border-t border-purple-200">
                            <span class="gradient-text">Total Harga</span>
                            <span id="calc-total" class="gradient-text text-lg">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="glass-card rounded-3xl p-5 mt-4">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">📝 Catatan (Opsional)</label>
            <textarea name="notes" rows="3" placeholder="Tambahkan catatan atau permintaan khusus..."
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-all resize-none">{{ old('notes') }}</textarea>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submitBtn" disabled
                class="mt-4 w-full py-4 rounded-2xl text-base font-black text-white transition-all duration-300 opacity-50 cursor-not-allowed"
                style="background: linear-gradient(135deg, #7C3AED, #2563EB); box-shadow: 0 8px 32px rgba(124,58,237,0.3);">
            <span id="btnText">Pilih tanggal terlebih dahulu</span>
        </button>
        <p class="text-center text-xs text-gray-400 mt-2">Kamu akan diminta upload bukti bayar setelah ini</p>
    </div>

    {{-- ===== VEHICLE INFO ===== --}}
    {{-- order-1: di mobile tampil pertama (atas), lg:order-2: di desktop tampil kedua (kanan) --}}
    <div class="order-1 lg:order-2 lg:col-span-2 animate-slide-up animation-delay-200">

        {{-- ===== VEHICLE CARD =====
             Mobile: horizontal compact strip (foto kiri, info kanan)
             Desktop: full card sticky di kanan --}}

        {{-- Mobile: compact horizontal card --}}
        <div class="glass-card rounded-3xl overflow-hidden lg:hidden">
            <div class="flex items-stretch gap-0">
                {{-- Foto kiri --}}
                <div class="w-28 sm:w-36 flex-shrink-0 relative overflow-hidden">
                    @if($vehicle->photo)
                        <img src="{{ asset('storage/'.$vehicle->photo) }}" alt="{{ $vehicle->name }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-black/10"></div>
                    @else
                        <div class="w-full h-full flex items-center justify-center min-h-[100px]"
                             style="background: linear-gradient(135deg, #EDE9FE, #DBEAFE);">
                            <span class="text-4xl">🚗</span>
                        </div>
                    @endif
                </div>

                {{-- Info kanan --}}
                <div class="flex-1 p-4 flex flex-col justify-between">
                    <div>
                        <h3 class="font-black text-gray-900 text-base leading-tight">{{ $vehicle->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium text-purple-600 bg-purple-50">{{ $vehicle->type }}</span>
                            <span class="text-gray-400 text-xs font-mono">{{ $vehicle->plate_number }}</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-end justify-between">
                        <div>
                            <p class="text-xs text-gray-400">per hari</p>
                            <p class="font-black gradient-text text-xl leading-none">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</p>
                        </div>
                        @if($vehicle->status === 'available')
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">✅ Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-orange-700 bg-orange-50 border border-orange-100">🕐 Disewa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop: full vertical card sticky --}}
        <div class="glass-card rounded-3xl overflow-hidden hidden lg:block sticky top-24">
            @if($vehicle->photo)
                <div class="h-44 overflow-hidden relative">
                    <img src="{{ asset('storage/'.$vehicle->photo) }}" alt="{{ $vehicle->name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-3 left-4">
                        @if($vehicle->status === 'available')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(16,185,129,0.85);">✅ Tersedia</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background:rgba(249,115,22,0.85);">🕐 Sedang Disewa</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="h-44 flex items-center justify-center" style="background: linear-gradient(135deg, #EDE9FE, #DBEAFE);">
                    <span class="text-6xl animate-float">🚗</span>
                </div>
            @endif
            <div class="p-5">
                <h3 class="font-black text-gray-900 text-xl mb-1">{{ $vehicle->name }}</h3>
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium text-purple-600 bg-purple-50">{{ $vehicle->type }}</span>
                    <span class="text-gray-400 text-xs font-mono">{{ $vehicle->plate_number }}</span>
                </div>
                @if($vehicle->description)
                    <p class="text-gray-500 text-xs mb-4 leading-relaxed">{{ $vehicle->description }}</p>
                @endif
                <div class="rounded-2xl p-4 text-center" style="background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(59,130,246,0.08));border:1px solid rgba(139,92,246,0.15)">
                    <p class="text-xs text-gray-400 mb-0.5">Harga per hari</p>
                    <p class="font-black gradient-text text-2xl">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Booked dates list (collapsible) --}}
        @if($bookedRanges->count() > 0)
            <div class="glass-card rounded-2xl mt-4 overflow-hidden" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <span class="flex items-center gap-2">
                        🔴 Tanggal Tidak Tersedia
                        <span class="px-2 py-0.5 rounded-full text-xs font-black text-white bg-red-500">{{ $bookedRanges->count() }}</span>
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="px-4 pb-3 space-y-2 border-t border-gray-100">
                    @foreach($bookedRanges as $range)
                        <div class="flex items-center gap-2 text-xs py-1">
                            <span>{{ $range['status'] === 'active' ? '🔴' : ($range['status'] === 'confirmed' ? '🟠' : '🟡') }}</span>
                            <span class="font-semibold text-gray-700">
                                {{ \Carbon\Carbon::parse($range['start'])->format('d M Y') }} → {{ \Carbon\Carbon::parse($range['end'])->format('d M Y') }}
                            </span>
                            <span class="text-gray-400">({{ $range['status'] === 'active' ? 'Aktif' : ($range['status'] === 'confirmed' ? 'Confirmed' : 'Pending') }})</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="glass-card rounded-2xl p-4 mt-4 flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="text-sm font-bold text-emerald-700">Semua Tanggal Tersedia</p>
                    <p class="text-xs text-gray-400">Pilih tanggal manapun yang kamu inginkan</p>
                </div>
            </div>
        @endif
    </div>
</div>
</form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ===== FLATPICKR CUSTOM THEME ===== */
.flatpickr-calendar {
    font-family: 'Figtree', sans-serif !important;
    border-radius: 20px !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
    width: 100% !important;
    padding: 0 !important;
}
.flatpickr-calendar.inline {
    top: 0 !important;
    width: 100% !important;
}
/* Month header */
.flatpickr-months {
    padding: 0 0 12px 0;
}
.flatpickr-months .flatpickr-month {
    background: linear-gradient(135deg, #7C3AED, #2563EB) !important;
    border-radius: 16px !important;
    height: 48px !important;
    color: white !important;
}
.flatpickr-current-month {
    color: white !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    padding-top: 14px !important;
}
.flatpickr-current-month .flatpickr-monthDropdown-months {
    background: transparent !important;
    color: white !important;
    font-weight: 800 !important;
}
.flatpickr-current-month input.cur-year {
    color: white !important;
    font-weight: 800 !important;
}
.flatpickr-months .flatpickr-prev-month,
.flatpickr-months .flatpickr-next-month {
    color: white !important;
    fill: white !important;
    padding: 14px 12px !important;
    border-radius: 0 16px 16px 0 !important;
}
.flatpickr-months .flatpickr-prev-month {
    border-radius: 16px 0 0 16px !important;
}
.flatpickr-months .flatpickr-prev-month:hover,
.flatpickr-months .flatpickr-next-month:hover {
    background: rgba(255,255,255,0.2) !important;
}
.flatpickr-months .flatpickr-prev-month svg,
.flatpickr-months .flatpickr-next-month svg {
    fill: white !important;
}

/* Weekday headers */
.flatpickr-weekdays {
    background: transparent !important;
    padding: 4px 0 !important;
}
.flatpickr-weekday {
    color: #8B5CF6 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}

/* Day cells */
.flatpickr-day {
    border-radius: 12px !important;
    border: none !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #374151 !important;
    height: 38px !important;
    line-height: 38px !important;
    max-width: 38px !important;
    transition: all 0.15s ease !important;
}
.flatpickr-day:hover:not(.flatpickr-disabled):not(.selected):not(.startRange):not(.endRange) {
    background: rgba(139, 92, 246, 0.1) !important;
    color: #7C3AED !important;
    transform: scale(1.05) !important;
}

/* Today */
.flatpickr-day.today {
    border: 2px solid #8B5CF6 !important;
    font-weight: 800 !important;
    color: #7C3AED !important;
}
.flatpickr-day.today:before { display: none; }

/* Selected start & end */
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: linear-gradient(135deg, #7C3AED, #2563EB) !important;
    border-color: transparent !important;
    color: white !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 16px rgba(124,58,237,0.4) !important;
    border-radius: 12px !important;
}
.flatpickr-day.startRange { border-radius: 12px 0 0 12px !important; }
.flatpickr-day.endRange   { border-radius: 0 12px 12px 0 !important; }
.flatpickr-day.startRange.endRange { border-radius: 12px !important; }

/* In range */
.flatpickr-day.inRange {
    background: rgba(139, 92, 246, 0.12) !important;
    box-shadow: -5px 0 0 rgba(139,92,246,0.12), 5px 0 0 rgba(139,92,246,0.12) !important;
    color: #7C3AED !important;
    border-radius: 0 !important;
    font-weight: 600 !important;
}

/* Disabled / booked dates */
.flatpickr-day.flatpickr-disabled,
.flatpickr-day.flatpickr-disabled:hover {
    background: #FEE2E2 !important;
    color: #F87171 !important;
    text-decoration: line-through !important;
    cursor: not-allowed !important;
    border-radius: 12px !important;
    opacity: 1 !important;
    position: relative !important;
}
.flatpickr-day.flatpickr-disabled::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 4px;
    background: #F87171;
    border-radius: 50%;
}

/* Prev/next month days */
.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay {
    color: #D1D5DB !important;
}
.flatpickr-day.prevMonthDay.flatpickr-disabled,
.flatpickr-day.nextMonthDay.flatpickr-disabled {
    background: transparent !important;
    color: #E5E7EB !important;
}

/* Days container */
.dayContainer {
    gap: 2px !important;
    padding: 0 !important;
}
.flatpickr-days {
    border: none !important;
    width: 100% !important;
}
.flatpickr-days .dayContainer {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
}
.flatpickr-innerContainer {
    border: none !important;
}

/* Multi-month responsive */
.flatpickr-calendar.hasTime .flatpickr-time {
    border-radius: 0 0 16px 16px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
const pricePerDay  = {{ $vehicle->price_per_day }};
const bookedRanges = @json($bookedRanges);

// ===== SYARAT & KETENTUAN MODAL =====
const skAgreeCheck = document.getElementById('skAgree');
const skAgreeBtn   = document.getElementById('skAgreeBtn');
let skAgreed = sessionStorage.getItem('sk_agreed') === '1';

if (skAgreed) {
    document.getElementById('skModal').style.display = 'none';
}

skAgreeCheck.addEventListener('change', () => {
    skAgreeBtn.disabled = !skAgreeCheck.checked;
    skAgreeBtn.style.opacity = skAgreeCheck.checked ? '1' : '0.5';
    skAgreeBtn.style.cursor  = skAgreeCheck.checked ? 'pointer' : 'not-allowed';
});

function closeSKModal() {
    sessionStorage.setItem('sk_agreed', '1');
    skAgreed = true;
    document.getElementById('skModal').style.display = 'none';
    updateSubmitBtn();
}
// ======================================

// Elemen DOM
const startInput    = document.getElementById('start_date_input');
const endInput      = document.getElementById('end_date_input');
const startTimeInput = document.getElementById('start_time_input');
const rangeDisplay  = document.getElementById('range-display');
const calcDays      = document.getElementById('calc-days');
const calcTotal     = document.getElementById('calc-total');
const displayStart  = document.getElementById('display-start');
const displayEnd    = document.getElementById('display-end');
const submitBtn     = document.getElementById('submitBtn');
const btnText       = document.getElementById('btnText');

let selectedDays = 0;
let selectedTotal = 0;

startTimeInput.addEventListener('change', updateSubmitBtn);

function updateSubmitBtn() {
    const hasDate = startInput.value && endInput.value;
    const hasTime = startTimeInput.value !== '';
    if (hasDate && hasTime) {
        submitBtn.disabled        = false;
        submitBtn.style.opacity   = '1';
        submitBtn.style.cursor    = 'pointer';
        btnText.textContent       = '🚗 Pesan Sekarang (' + selectedDays + ' hari · Rp ' + selectedTotal.toLocaleString('id-ID') + ')';
    } else {
        submitBtn.disabled        = true;
        submitBtn.style.opacity   = '0.5';
        submitBtn.style.cursor    = 'not-allowed';
        btnText.textContent       = hasDate && !hasTime ? 'Pilih jam pengambilan' : 'Pilih tanggal terlebih dahulu';
    }
}

// ─── FIX TIMEZONE: gunakan tanggal LOKAL, bukan UTC ───────────────────────────
// date.toISOString() → UTC, menyebabkan off-by-one di timezone UTC+ (mis. UTC+7)
// Solusi: ambil komponen tanggal lokal secara eksplisit
function toYMD(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}
// ─────────────────────────────────────────────────────────────────────────────

function formatDateID(date) {
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Disable function menggunakan perbandingan string lokal — aman di semua timezone
function isDateBooked(date) {
    const dateStr = toYMD(date);
    return bookedRanges.some(r => dateStr >= r.start && dateStr <= r.end);
}

// Cek apakah rentang start-end melewati booking yang sudah ada
function rangeHasConflict(startStr, endStr) {
    return bookedRanges.some(r => startStr <= r.end && endStr >= r.start);
}

// Inisialisasi Flatpickr
const fp = flatpickr("#datepicker-container", {
    mode:       "range",
    inline:     true,
    minDate:    "today",
    dateFormat: "Y-m-d",
    // Gunakan fungsi disable — lebih aman daripada {from,to} object
    // karena Flatpickr bisa salah parse string ke UTC vs lokal
    disable:    [isDateBooked],
    locale:     "id",
    showMonths: window.innerWidth >= 640 ? 2 : 1,

    onDayCreate(dObj, dStr, fp, dayElem) {
        if (dayElem.classList.contains('flatpickr-disabled')) {
            dayElem.title = '❌ Sudah dipesan';
        }
    },

    onChange(selectedDates) {
        if (selectedDates.length === 2) {
            const [start, end] = selectedDates;
            const startStr = toYMD(start);
            const endStr   = toYMD(end);

            // Double-check server-side logic: tidak boleh ada range yang melewati booking
            if (rangeHasConflict(startStr, endStr)) {
                fp.clear();
                clearSelection();
                showConflictToast();
                return;
            }

            const days = Math.round((end - start) / 86400000);

            startInput.value = startStr;
            endInput.value   = endStr;
            selectedDays  = days;
            selectedTotal = days * pricePerDay;

            displayStart.textContent = formatDateID(start);
            displayEnd.textContent   = formatDateID(end);
            calcDays.textContent     = days + ' hari';
            calcTotal.textContent    = 'Rp ' + selectedTotal.toLocaleString('id-ID');

            rangeDisplay.classList.remove('hidden');
            rangeDisplay.classList.add('animate-slide-up');

            updateSubmitBtn();

        } else {
            clearSelection();
        }
    },
});

function clearSelection() {
    startInput.value = '';
    endInput.value   = '';
    selectedDays  = 0;
    selectedTotal = 0;
    rangeDisplay.classList.add('hidden');
    updateSubmitBtn();
}

function showConflictToast() {
    const div = document.createElement('div');
    div.className = 'fixed top-20 right-4 z-50 flex items-center gap-3 bg-white rounded-2xl shadow-xl border border-red-200 px-4 py-3 animate-slide-down w-80';
    div.style.boxShadow = '0 8px 32px rgba(239,68,68,0.2)';
    div.innerHTML = `
        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">🚫</div>
        <div>
            <p class="text-sm font-bold text-gray-900">Rentang Tidak Tersedia</p>
            <p class="text-xs text-gray-500 mt-0.5">Rentang tanggal melewati tanggal yang sudah dipesan.</p>
        </div>
    `;
    document.body.appendChild(div);
    setTimeout(() => {
        div.style.transition = 'all 0.4s ease';
        div.style.opacity = '0';
        div.style.transform = 'translateX(100%)';
        setTimeout(() => div.remove(), 400);
    }, 3500);
}

// Responsive: update jumlah bulan saat resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        const months = window.innerWidth >= 640 ? 2 : 1;
        if (fp.config.showMonths !== months) {
            fp.set('showMonths', months);
        }
    }, 200);
});

// Restore old values jika ada (dari validation failure)
@if(old('start_date') && old('end_date'))
    fp.setDate(["{{ old('start_date') }}", "{{ old('end_date') }}"], true);
@endif
</script>
@endpush
