@extends('layouts.admin')

@section('title', 'Detail Pemesanan #' . $booking->id)

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali</a>
    <div class="flex items-center justify-between mt-1">
        <h2 class="text-xl font-bold text-gray-900">Detail Pemesanan #{{ $booking->id }}</h2>
        @include('partials.booking-status-badge', ['status' => $booking->status])
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        {{-- Booking Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Pemesanan</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-400">Pelanggan</p><p class="font-medium">{{ $booking->user->name }}</p><p class="text-gray-400 text-xs">{{ $booking->user->email }}</p></div>
                <div><p class="text-gray-400">Telepon</p><p class="font-medium">{{ $booking->user->phone ?? '-' }}</p></div>
                <div><p class="text-gray-400">Kendaraan</p><p class="font-medium">{{ $booking->vehicle->name }}</p><p class="text-gray-400 text-xs">{{ $booking->vehicle->plate_number }}</p></div>
                <div><p class="text-gray-400">Tipe</p><p class="font-medium">{{ $booking->vehicle->type }}</p></div>
                <div><p class="text-gray-400">Tanggal Mulai</p><p class="font-medium">{{ $booking->start_date->format('d F Y') }}</p></div>
                <div><p class="text-gray-400">Tanggal Selesai</p><p class="font-medium">{{ $booking->end_date->format('d F Y') }}</p></div>
                <div><p class="text-gray-400">Durasi</p><p class="font-medium">{{ $booking->total_days }} hari</p></div>
                <div><p class="text-gray-400">Total Harga</p><p class="font-bold text-blue-600 text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                @if($booking->notes)
                    <div class="col-span-2"><p class="text-gray-400">Catatan</p><p class="font-medium">{{ $booking->notes }}</p></div>
                @endif
            </div>
        </div>

        {{-- Payment Proof --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Bukti Pembayaran</h3>
            @if($booking->payment && $booking->payment->proof_image)
                <div class="flex items-start gap-4">
                    <a href="{{ asset('storage/' . $booking->payment->proof_image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $booking->payment->proof_image) }}" class="w-40 h-32 object-cover rounded-xl border cursor-pointer hover:opacity-90 transition">
                    </a>
                    <div>
                        <p class="text-sm text-gray-500">Dikirim: {{ $booking->payment->payment_date ? \Carbon\Carbon::parse($booking->payment->payment_date)->format('d F Y') : '-' }}</p>
                        <p class="text-sm mt-1">Status: @include('partials.payment-status-badge', ['status' => $booking->payment->status])</p>
                        @if($booking->payment->verifiedBy)
                            <p class="text-xs text-gray-400 mt-1">Diverifikasi oleh {{ $booking->payment->verifiedBy->name }} pada {{ $booking->payment->verified_at?->format('d F Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-400 text-sm">Belum ada bukti pembayaran yang diupload.</p>
            @endif
        </div>

        {{-- Extensions --}}
        @if($booking->extensions->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Riwayat Perpanjangan</h3>
                <table class="w-full text-sm">
                    <thead class="text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="text-left pb-2">Tambahan Hari</th>
                            <th class="text-left pb-2">Biaya</th>
                            <th class="text-left pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($booking->extensions as $ext)
                            <tr>
                                <td class="py-2">{{ $ext->additional_days }} hari</td>
                                <td class="py-2">Rp {{ number_format($ext->additional_price, 0, ',', '.') }}</td>
                                <td class="py-2">
                                    @php $ec = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700']; $el = ['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','rejected'=>'Ditolak']; @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ec[$ext->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $el[$ext->status] ?? ucfirst($ext->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24 space-y-3">
            <h4 class="font-semibold text-gray-900">Aksi</h4>

            @if($booking->status === 'pending')
                <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST">
                    @csrf
                    <button class="w-full bg-blue-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition">✅ Konfirmasi Pemesanan</button>
                </form>
            @endif

            @if($booking->status === 'confirmed')
                @php
                    $startIsToday  = $booking->start_date->isToday();
                    $startIsPast   = $booking->start_date->isPast() && !$startIsToday;
                    $startIsFuture = $booking->start_date->isFuture();
                @endphp

                {{-- Auto-activation hint --}}
                @if($startIsToday || $startIsPast)
                    <div class="flex items-center gap-2 p-3 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                        <span class="text-base">🤖</span>
                        <span>
                            @if($startIsToday)
                                Akan <strong>aktif otomatis</strong> hari ini. Kamu juga bisa aktifkan manual sekarang.
                            @else
                                Tanggal mulai sudah lewat. Klik aktifkan untuk memulai sewa.
                            @endif
                        </span>
                    </div>
                @elseif($startIsFuture)
                    <div class="flex items-center gap-2 p-3 rounded-xl text-xs text-blue-700 bg-blue-50 border border-blue-200">
                        <span class="text-base">📅</span>
                        <span>Akan aktif otomatis pada <strong>{{ $booking->start_date->format('d M Y') }}</strong> pukul 00:05.</span>
                    </div>
                @endif

                <form action="{{ route('admin.bookings.activate', $booking) }}" method="POST">
                    @csrf
                    <button class="w-full bg-green-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-green-700 transition flex items-center justify-center gap-2">
                        🚗 Aktifkan Manual Sekarang
                    </button>
                </form>
            @endif

            @if($booking->status === 'active')
                @php $endIsToday = $booking->end_date->isToday(); @endphp

                @if($endIsToday)
                    <div class="flex items-center gap-2 p-3 rounded-xl text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200">
                        <span class="text-base">🤖</span>
                        <span>Sewa berakhir <strong>hari ini</strong>. Akan diselesaikan otomatis besok. Kamu juga bisa selesaikan manual.</span>
                    </div>
                @endif

                <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST">
                    @csrf
                    <button class="w-full bg-purple-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-purple-700 transition">🏁 Selesaikan Manual</button>
                </form>
            @endif

            @if(in_array($booking->status, ['pending', 'confirmed', 'active']))
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan pemesanan ini?')">
                    @csrf
                    <button class="w-full border border-red-300 text-red-600 py-2.5 rounded-xl text-sm font-medium hover:bg-red-50 transition">❌ Batalkan Pemesanan</button>
                </form>
            @endif

            <div class="pt-3 border-t border-gray-100 text-xs text-gray-400 space-y-1">
                <p>Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</p>
                <p>Diperbarui: {{ $booking->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
