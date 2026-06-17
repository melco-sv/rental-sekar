{{-- Pending Bookings --}}
<div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center text-base flex-shrink-0">📋</div>
            <div>
                <h3 class="font-black text-gray-900 text-sm leading-tight">Pemesanan Baru</h3>
                <p class="text-xs text-gray-400">Menunggu konfirmasi</p>
            </div>
        </div>
        @if($notifCounts['bookings'] > 0)
            <span class="px-2 py-0.5 rounded-full text-xs font-black text-white bg-blue-500">
                {{ $notifCounts['bookings'] }} pending
            </span>
        @else
            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-gray-400 bg-gray-100">Bersih ✓</span>
        @endif
    </div>

    <div class="flex-1 divide-y divide-gray-50 overflow-hidden">
        @forelse($pendingBookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking) }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-blue-50/50 transition-colors group">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #3B82F6, #2563EB);">
                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-xs truncate">{{ $booking->user->name }}</p>
                    <p class="text-gray-400 text-xs truncate">{{ $booking->vehicle->name }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-500">{{ $booking->start_date->format('d M') }}</p>
                    <svg class="w-3 h-3 text-gray-300 group-hover:text-blue-400 transition-colors ml-auto mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <span class="text-3xl mb-2">✅</span>
                <p class="text-xs font-medium">Semua sudah dikonfirmasi</p>
            </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}"
           class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors flex items-center justify-between">
            Lihat semua pemesanan pending <span>→</span>
        </a>
    </div>
</div>

{{-- Pending Payments --}}
<div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center text-base flex-shrink-0">💳</div>
            <div>
                <h3 class="font-black text-gray-900 text-sm leading-tight">Bukti Pembayaran</h3>
                <p class="text-xs text-gray-400">Menunggu verifikasi</p>
            </div>
        </div>
        @if($notifCounts['payments'] > 0)
            <span class="px-2 py-0.5 rounded-full text-xs font-black text-white bg-green-500">
                {{ $notifCounts['payments'] }} pending
            </span>
        @else
            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-gray-400 bg-gray-100">Bersih ✓</span>
        @endif
    </div>

    <div class="flex-1 divide-y divide-gray-50 overflow-hidden">
        @forelse($pendingPayments as $payment)
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-green-50/50 transition-colors group">
                @if($payment->proof_image)
                    <img src="{{ asset('storage/'.$payment->proof_image) }}"
                         class="w-9 h-9 rounded-xl object-cover border border-gray-200 flex-shrink-0">
                @else
                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0 text-sm">💳</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-xs truncate">{{ $payment->booking->user->name }}</p>
                    <p class="text-gray-400 text-xs truncate">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>
                <svg class="w-3 h-3 text-gray-300 group-hover:text-green-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <span class="text-3xl mb-2">✅</span>
                <p class="text-xs font-medium">Semua sudah diverifikasi</p>
            </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
           class="text-xs font-bold text-green-600 hover:text-green-800 transition-colors flex items-center justify-between">
            Lihat semua pembayaran pending <span>→</span>
        </a>
    </div>
</div>

{{-- Pending Extensions --}}
<div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center text-base flex-shrink-0">⏰</div>
            <div>
                <h3 class="font-black text-gray-900 text-sm leading-tight">Perpanjangan Sewa</h3>
                <p class="text-xs text-gray-400">Menunggu konfirmasi</p>
            </div>
        </div>
        @if($notifCounts['extensions'] > 0)
            <span class="px-2 py-0.5 rounded-full text-xs font-black text-white bg-orange-500">
                {{ $notifCounts['extensions'] }} pending
            </span>
        @else
            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-gray-400 bg-gray-100">Bersih ✓</span>
        @endif
    </div>

    <div class="flex-1 divide-y divide-gray-50 overflow-hidden">
        @forelse($pendingExtensions as $ext)
            <a href="{{ route('admin.extensions.index') }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-orange-50/50 transition-colors group">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                    {{ strtoupper(substr($ext->booking->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-xs truncate">{{ $ext->booking->user->name }}</p>
                    <p class="text-gray-400 text-xs truncate">
                        +{{ $ext->additional_days }} hari · {{ $ext->booking->vehicle->name }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-orange-600 font-bold">+{{ number_format($ext->additional_price/1000, 0)}}k</p>
                    <svg class="w-3 h-3 text-gray-300 group-hover:text-orange-400 transition-colors ml-auto mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <span class="text-3xl mb-2">✅</span>
                <p class="text-xs font-medium">Tidak ada perpanjangan pending</p>
            </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.extensions.index', ['status' => 'pending']) }}"
           class="text-xs font-bold text-orange-600 hover:text-orange-800 transition-colors flex items-center justify-between">
            Lihat semua perpanjangan pending <span>→</span>
        </a>
    </div>
</div>

{{-- Pending Refunds --}}
<div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center text-base flex-shrink-0">💸</div>
            <div>
                <h3 class="font-black text-gray-900 text-sm leading-tight">Pengembalian Dana</h3>
                <p class="text-xs text-gray-400">Menunggu transfer balik</p>
            </div>
        </div>
        @if($notifCounts['cancellations'] > 0)
            <span class="px-2 py-0.5 rounded-full text-xs font-black text-white bg-red-500">
                {{ $notifCounts['cancellations'] }} pending
            </span>
        @else
            <span class="px-2 py-0.5 rounded-full text-xs font-bold text-gray-400 bg-gray-100">Bersih ✓</span>
        @endif
    </div>

    <div class="flex-1 divide-y divide-gray-50 overflow-hidden">
        @forelse($pendingRefunds as $refund)
            <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-red-50/50 transition-colors group">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                    {{ strtoupper(substr($refund->booking->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 text-xs truncate">{{ $refund->booking->user->name }}</p>
                    <p class="text-gray-400 text-xs truncate">{{ $refund->booking->vehicle->name }} · {{ $refund->refund_percentage }}%</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-red-600 font-bold">Rp {{ number_format($refund->refund_amount/1000, 0) }}k</p>
                    <svg class="w-3 h-3 text-gray-300 group-hover:text-red-400 transition-colors ml-auto mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <span class="text-3xl mb-2">✅</span>
                <p class="text-xs font-medium">Tidak ada pengembalian dana pending</p>
            </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}"
           class="text-xs font-bold text-red-600 hover:text-red-800 transition-colors flex items-center justify-between">
            Lihat semua pengembalian dana pending <span>→</span>
        </a>
    </div>
</div>
