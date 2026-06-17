@extends('layouts.customer')

@section('title', 'Ajukan Perpanjangan Sewa')

@section('content')
<div class="mb-6">
    <a href="{{ route('customer.bookings.show', $booking) }}" class="text-blue-600 hover:underline text-sm">← Kembali ke detail pemesanan</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Ajukan Perpanjangan Sewa</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <form action="{{ route('customer.extensions.store', $booking) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5">
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tambahan Hari <span class="text-red-500">*</span></label>
                <input type="number" name="additional_days" id="additional_days" value="{{ old('additional_days', 1) }}" min="1" max="30"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('additional_days') border-red-500 @enderror">
                @error('additional_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">Maksimal 30 hari perpanjangan.</p>
            </div>

            {{-- Price Preview --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                <h4 class="font-semibold text-blue-900 mb-2">Perkiraan Biaya Tambahan</h4>
                <div class="flex justify-between text-sm text-blue-800 mb-1">
                    <span>Tambahan Hari</span>
                    <span id="ext-days">1 hari</span>
                </div>
                <div class="flex justify-between text-sm text-blue-800 mb-1">
                    <span>Harga Per Hari</span>
                    <span>Rp {{ number_format($booking->vehicle->price_per_day, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-blue-900 pt-2 border-t border-blue-200 mt-2">
                    <span>Total Tambahan</span>
                    <span id="ext-total">Rp {{ number_format($booking->vehicle->price_per_day, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    Tanggal selesai baru: <strong id="new-end-date">{{ $booking->end_date->addDay()->format('d F Y') }}</strong>
                </p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Pembayaran Tambahan <span class="text-red-500">*</span></label>
                <input type="file" name="payment_proof" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 @error('payment_proof') border-red-500 @enderror">
                @error('payment_proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Maks 2MB.</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 text-sm text-yellow-800">
                ⚠️ Pengajuan perpanjangan akan otomatis <strong>ditolak</strong> jika kendaraan sudah dipesan oleh pelanggan lain pada tanggal yang diminta.
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                Ajukan Perpanjangan
            </button>
        </form>
    </div>

    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h4 class="font-semibold text-gray-900 mb-4">Info Sewa Saat Ini</h4>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-400">Kendaraan</p>
                    <p class="font-medium text-gray-900">{{ $booking->vehicle->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Tanggal Mulai</p>
                    <p class="font-medium text-gray-900">{{ $booking->start_date->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Tanggal Selesai (Saat Ini)</p>
                    <p class="font-medium text-gray-900">{{ $booking->end_date->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Total Hari (Saat Ini)</p>
                    <p class="font-medium text-gray-900">{{ $booking->total_days }} hari</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const pricePerDay = {{ $booking->vehicle->price_per_day }};
    const currentEndDate = new Date('{{ $booking->end_date->toDateString() }}');

    const input = document.getElementById('additional_days');
    const extDays = document.getElementById('ext-days');
    const extTotal = document.getElementById('ext-total');
    const newEndDateEl = document.getElementById('new-end-date');

    function updatePreview() {
        const days = parseInt(input.value) || 0;
        const total = days * pricePerDay;
        extDays.textContent = days + ' hari';
        extTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');

        const newEnd = new Date(currentEndDate);
        newEnd.setDate(newEnd.getDate() + days);
        newEndDateEl.textContent = newEnd.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    input.addEventListener('input', updatePreview);
    updatePreview();
</script>
@endpush
