@extends('layouts.customer')

@section('title', 'Detail Pemesanan #' . $booking->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('customer.bookings.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke riwayat pemesanan</a>
    <div class="flex items-center justify-between mt-2">
        <h2 class="text-2xl font-bold text-gray-900">Detail Pemesanan #{{ $booking->id }}</h2>
        @include('partials.booking-status-badge', ['status' => $booking->status])
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Booking Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-lg">Informasi Pemesanan</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Kendaraan</p>
                    <p class="font-medium text-gray-900">{{ $booking->vehicle->name }}</p>
                    <p class="text-gray-500 text-xs">{{ $booking->vehicle->plate_number }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Tipe</p>
                    <p class="font-medium text-gray-900">{{ $booking->vehicle->type }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Tanggal Mulai</p>
                    <p class="font-medium text-gray-900">{{ $booking->start_date->format('d F Y') }}</p>
                    @if($booking->start_time)
                        <p class="text-purple-600 text-xs font-semibold mt-0.5">🕐 {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} WIB</p>
                    @endif
                </div>
                <div>
                    <p class="text-gray-400">Tanggal Selesai</p>
                    <p class="font-medium text-gray-900">{{ $booking->end_date->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Durasi</p>
                    <p class="font-medium text-gray-900">{{ $booking->total_days }} hari</p>
                </div>
                <div>
                    <p class="text-gray-400">Total Harga</p>
                    <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                </div>
                @if($booking->notes)
                    <div class="col-span-2">
                        <p class="text-gray-400">Catatan</p>
                        <p class="font-medium text-gray-900">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-lg">Pembayaran</h3>

            @if(!$booking->payment || !$booking->payment->proof_image)
                {{-- Upload Form --}}
                @if(in_array($booking->status, ['pending']))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                        <p class="text-yellow-800 text-sm font-medium">⚠️ Silakan upload bukti transfer untuk melanjutkan proses sewa.</p>
                        <p class="text-yellow-700 text-xs mt-1">Transfer ke rekening: BCA 1234567890 a.n. Rental Mobil Sekar</p>
                    </div>
                    <form action="{{ route('customer.payments.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @error('proof_image')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3 text-red-700 text-sm">{{ $message }}</div>
                        @enderror
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                            <input type="file" name="proof_image" accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Maks 2MB.</p>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm hover:bg-blue-700 transition font-medium">Upload Bukti Pembayaran</button>
                    </form>
                @endif
            @elseif($booking->payment->status === 'rejected')
                {{-- Re-upload --}}
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <p class="text-red-800 text-sm font-medium">❌ Bukti pembayaran ditolak. Silakan upload ulang.</p>
                    <a href="{{ asset('storage/' . $booking->payment->proof_image) }}" target="_blank" class="text-xs text-red-600 hover:underline">Lihat bukti sebelumnya</a>
                </div>
                <form action="{{ route('customer.payments.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Ulang Bukti Transfer</label>
                        <input type="file" name="proof_image" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm hover:bg-blue-700 transition font-medium">Upload Ulang</button>
                </form>
            @elseif($booking->payment->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <img src="{{ asset('storage/' . $booking->payment->proof_image) }}" alt="Bukti Transfer"
                             class="w-24 h-24 object-cover rounded-lg border cursor-pointer"
                             onclick="window.open('{{ asset('storage/' . $booking->payment->proof_image) }}', '_blank')">
                        <div>
                            <p class="text-yellow-800 font-medium text-sm">⏳ Bukti pembayaran sedang diverifikasi admin.</p>
                            <p class="text-yellow-600 text-xs mt-1">Dikirim: {{ $booking->payment->payment_date ? \Carbon\Carbon::parse($booking->payment->payment_date)->format('d F Y') : '-' }}</p>
                            <p class="text-xs text-yellow-500 mt-0.5">Klik gambar untuk memperbesar</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-xl">✅</div>
                        <div>
                            <p class="text-green-800 font-medium text-sm">Pembayaran terverifikasi</p>
                            <p class="text-green-600 text-xs">Diverifikasi pada {{ $booking->payment->verified_at?->format('d F Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Refund Status Section --}}
        @if($booking->refund)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4 text-lg">💸 Status Pengembalian Dana</h3>
                <div class="rounded-xl p-4 border {{ $booking->refund->status === 'completed' ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
                    <div class="flex items-start gap-4">
                        <div class="text-3xl">{{ $booking->refund->status === 'completed' ? '✅' : '⏳' }}</div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-sm">
                                {{ $booking->refund->status === 'completed' ? 'Dana Sudah Dikembalikan' : 'Pengembalian Dana Sedang Diproses' }}
                            </p>
                            <div class="mt-2 space-y-1 text-xs text-gray-600">
                                <p>Total dibayar: <span class="font-semibold">Rp {{ number_format($booking->refund->amount_paid, 0, ',', '.') }}</span></p>
                                <p>Persentase refund: <span class="font-semibold">{{ $booking->refund->refund_percentage }}%</span></p>
                                <p>Dana yang dikembalikan: <span class="font-bold text-green-700 text-sm">Rp {{ number_format($booking->refund->refund_amount, 0, ',', '.') }}</span></p>
                            </div>
                            @if($booking->refund->status === 'completed' && $booking->refund->proof_image)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1">Bukti Transfer dari Admin:</p>
                                    <a href="{{ asset('storage/' . $booking->refund->proof_image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $booking->refund->proof_image) }}"
                                             alt="Bukti Refund"
                                             class="w-32 h-32 object-cover rounded-lg border border-green-200 hover:opacity-80 transition cursor-pointer">
                                    </a>
                                    <p class="text-xs text-gray-400 mt-1">Diproses: {{ $booking->refund->processed_at?->format('d F Y H:i') }}</p>
                                </div>
                            @elseif($booking->refund->status === 'pending')
                                <p class="text-xs text-amber-600 mt-2">Admin sedang memproses transfer pengembalian dana. Mohon tunggu konfirmasi.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Cancel Button — hanya untuk confirmed + payment verified --}}
        @if($booking->status === 'confirmed' && $booking->payment?->status === 'verified')
            @php
                $pickup = $booking->pickup_datetime;
                $hoursLeft = now()->diffInMinutes($pickup, false) / 60;
                $refundPct = $hoursLeft < 2 ? 30 : 50;
                $amountPaid = $booking->payments->where('status','verified')->sum('amount');
                $refundNominal = $amountPaid * $refundPct / 100;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
                <h3 class="font-semibold text-red-700 mb-2 text-lg">⚠️ Batalkan Pemesanan</h3>
                <p class="text-sm text-gray-500 mb-1">Jika dibatalkan sekarang, Anda akan mendapat pengembalian dana:</p>
                <p class="text-lg font-black text-red-600 mb-1">{{ $refundPct }}% = Rp {{ number_format($refundNominal, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mb-4">
                    @if($hoursLeft < 2)
                        (Pembatalan < 2 jam sebelum jam pengambilan — refund 30%)
                    @else
                        (Pembatalan ≥ 2 jam sebelum jam pengambilan — refund 50%)
                    @endif
                </p>
                <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 transition">
                    Batalkan Pemesanan
                </button>
            </div>

            {{-- Cancel Confirmation Modal --}}
            <div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6)">
                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
                    <h4 class="font-black text-gray-900 text-lg mb-2">Konfirmasi Pembatalan</h4>
                    <p class="text-sm text-gray-500 mb-4">Anda akan menerima pengembalian dana sebesar <strong class="text-red-600">{{ $refundPct }}% (Rp {{ number_format($refundNominal, 0, ',', '.') }})</strong> dari total pembayaran. Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button onclick="document.getElementById('cancelModal').classList.add('hidden')"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Tidak, Kembali
                        </button>
                        <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 transition">
                                Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Extension Section — always shown when active --}}
        @if($booking->status === 'active')
            @php
                $extensionDeadline  = \Carbon\Carbon::parse($booking->end_date)->endOfDay()->subHours(2);
                $canExtend          = now()->lt($extensionDeadline);
                $minsLeft           = now()->diffInMinutes($extensionDeadline, false);
                $hoursLeft          = floor(abs($minsLeft) / 60);
                $minutesLeft        = abs($minsLeft) % 60;

                // Cek semua status extension yang ada
                $hasPending         = $booking->extensions->where('status', 'pending')->count() > 0;
                $hasConfirmed       = $booking->extensions->where('status', 'confirmed')->count() > 0;
                $lastExtension      = $booking->extensions->sortByDesc('created_at')->first();

                // Blokir tombol jika ada yang pending
                $buttonBlocked      = $hasPending;
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4 gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">⏰ Perpanjangan Sewa</h3>
                        @if(!$buttonBlocked && $canExtend)
                            <p class="text-xs text-gray-400 mt-0.5">
                                Batas pengajuan:
                                <span class="font-semibold text-green-600">{{ $extensionDeadline->format('d M Y H:i') }}</span>
                                ({{ $hoursLeft }}j {{ $minutesLeft }}m lagi)
                            </p>
                        @elseif(!$canExtend)
                            <p class="text-xs text-red-500 mt-0.5 font-medium">
                                Masa pengajuan sudah berakhir
                            </p>
                        @endif
                    </div>

                    {{-- ═══ TOMBOL PERPANJANGAN — 4 skenario ═══ --}}
                    @if($buttonBlocked)
                        {{-- SKENARIO 1: Ada pending → tunggu konfirmasi --}}
                        <div class="flex-shrink-0 flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200">
                            <span class="text-base">⏳</span>
                            <div>
                                <p class="text-xs font-bold text-amber-800">Menunggu konfirmasi</p>
                                <p class="text-xs text-amber-600">Admin sedang memproses</p>
                            </div>
                        </div>
                    @elseif(!$canExtend)
                        {{-- SKENARIO 2: Lewat batas waktu → disabled --}}
                        <div class="flex-shrink-0 group relative">
                            <button type="button" disabled
                                    class="px-4 py-2 rounded-xl text-sm font-bold text-gray-400 bg-gray-100 cursor-not-allowed border border-gray-200">
                                🔒 Waktu Habis
                            </button>
                            <div class="absolute right-0 top-10 w-64 p-3 rounded-xl text-xs text-white shadow-xl z-10 hidden group-hover:block pointer-events-none"
                                 style="background: rgba(30,30,30,0.95);">
                                Pengajuan hanya bisa dilakukan hingga <strong>2 jam sebelum sewa berakhir</strong>.
                            </div>
                        </div>
                    @else
                        {{-- SKENARIO 3 & 4: Bisa ajukan (pertama kali ATAU setelah sebelumnya rejected/confirmed) --}}
                        <a href="{{ route('customer.extensions.create', $booking) }}"
                           class="flex-shrink-0 ripple px-4 py-2 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-0.5"
                           style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 16px rgba(16,185,129,0.3);">
                            + Ajukan Perpanjangan
                        </a>
                    @endif
                    {{-- ════════════════════════════════════════ --}}
                </div>

                {{-- Info jika sudah pernah confirmed sebelumnya --}}
                @if($hasConfirmed && !$hasPending)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center gap-2">
                        <span class="text-base">ℹ️</span>
                        <p class="text-blue-800 text-xs">
                            Perpanjangan sebelumnya sudah dikonfirmasi. Kamu bisa mengajukan perpanjangan lagi jika masih ingin memperpanjang.
                        </p>
                    </div>
                @endif

                {{-- Warning mendekati batas waktu --}}
                @if(!$buttonBlocked && $canExtend && $minsLeft <= 120 && $minsLeft > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 flex items-center gap-2">
                        <span class="text-xl">⚠️</span>
                        <p class="text-amber-800 text-xs font-medium">
                            Segera! Masa pengajuan perpanjangan berakhir dalam
                            <strong>{{ $hoursLeft > 0 ? $hoursLeft.'j ' : '' }}{{ $minutesLeft }}m</strong>.
                        </p>
                    </div>
                @endif

                {{-- Extensions table --}}
                @if($booking->extensions->count() > 0)
                    <table class="w-full text-sm mt-2">
                        <thead class="text-gray-500 text-xs uppercase border-b border-gray-100">
                            <tr>
                                <th class="text-left pb-2">Tambahan</th>
                                <th class="text-left pb-2">Biaya</th>
                                <th class="text-left pb-2">Status</th>
                                <th class="text-left pb-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($booking->extensions as $ext)
                                <tr>
                                    <td class="py-2 font-medium">{{ $ext->additional_days }} hari</td>
                                    <td class="py-2 text-gray-700">Rp {{ number_format($ext->additional_price, 0, ',', '.') }}</td>
                                    <td class="py-2">
                                        @php
                                            $extColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                                            $extLabels = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'rejected' => 'Ditolak'];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $extColors[$ext->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $extLabels[$ext->status] ?? ucfirst($ext->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-gray-400 text-xs">{{ $ext->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-400 text-sm mt-2">Belum ada pengajuan perpanjangan.</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">
            <h4 class="font-semibold text-gray-900 mb-3">Status Pemesanan</h4>
            <div class="space-y-2 text-sm">
                @php
                    $steps = ['pending' => 1, 'confirmed' => 2, 'active' => 3, 'completed' => 4, 'cancelled' => 0];
                    $currentStep = $steps[$booking->status] ?? 0;
                @endphp
                @foreach(['pending' => 'Menunggu Konfirmasi', 'confirmed' => 'Dikonfirmasi', 'active' => 'Aktif', 'completed' => 'Selesai'] as $s => $l)
                    @php $stepNum = $steps[$s]; @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            {{ $currentStep >= $stepNum && $booking->status !== 'cancelled' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $stepNum }}
                        </div>
                        <span class="{{ $currentStep >= $stepNum && $booking->status !== 'cancelled' ? 'text-gray-900 font-medium' : 'text-gray-400' }}">{{ $l }}</span>
                    </div>
                @endforeach
                @if($booking->status === 'cancelled')
                    <div class="flex items-center gap-3 mt-2">
                        <div class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs font-bold shrink-0">✕</div>
                        <span class="text-red-600 font-medium">Dibatalkan</span>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 space-y-1">
                <p>Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</p>
                <p>Diperbarui: {{ $booking->updated_at->format('d M Y H:i') }}</p>
                @if($booking->start_time)
                    <p class="text-purple-500 font-semibold">Jam jemput: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} WIB</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
