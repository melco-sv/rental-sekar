<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreExtensionRequest;
use App\Models\Booking;
use App\Models\Extension;
use Carbon\Carbon;

class ExtensionController extends Controller
{
    public function index()
    {
        // Booking aktif milik user ini — kandidat perpanjangan
        $activeBookings = \App\Models\Booking::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with(['vehicle', 'extensions'])
            ->orderBy('end_date')
            ->get()
            ->map(function ($booking) {
                $deadline   = \Carbon\Carbon::parse($booking->end_date)->endOfDay()->subHours(2);
                $canExtend  = now()->lt($deadline);
                $hasPending = $booking->extensions->where('status', 'pending')->count() > 0;
                $minsLeft   = now()->diffInMinutes($deadline, false);

                $booking->extension_deadline = $deadline;
                $booking->can_extend         = $canExtend && !$hasPending;
                $booking->has_pending        = $hasPending;
                $booking->mins_left          = $minsLeft;

                return $booking;
            });

        // Riwayat semua perpanjangan
        $extensions = Extension::whereHas('booking', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['booking.vehicle'])
            ->latest()
            ->paginate(8);

        return view('customer.extensions.index', compact('activeBookings', 'extensions'));
    }

    public function create(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        abort_if($booking->status !== 'active', 403, 'Perpanjangan hanya bisa diajukan untuk pemesanan yang sedang aktif.');

        // Blokir jika sudah ada perpanjangan yang pending
        if ($booking->extensions()->where('status', 'pending')->exists()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Kamu sudah memiliki pengajuan perpanjangan yang sedang menunggu konfirmasi admin. Harap tunggu sampai diproses.');
        }

        $booking->load('vehicle');

        return view('customer.extensions.create', compact('booking'));
    }

    public function store(StoreExtensionRequest $request, Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        abort_if($booking->status !== 'active', 403);

        // ─── SERVER-SIDE GUARD (cek ulang di store, tidak hanya di create) ────
        // Mencegah bypass lewat direct POST / dua tab browser / double submit
        if ($booking->extensions()->where('status', 'pending')->exists()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Pengajuan perpanjangan tidak bisa dilakukan. Kamu masih memiliki 1 pengajuan yang sedang menunggu konfirmasi.');
        }
        // ──────────────────────────────────────────────────────────────────────

        $additionalDays  = (int) $request->additional_days;
        $newEndDate      = Carbon::parse($booking->end_date)->addDays($additionalDays);
        $checkStart      = Carbon::parse($booking->end_date)->addDay()->toDateString();
        $additionalPrice = $additionalDays * $booking->vehicle->price_per_day;

        $path = $request->file('payment_proof')->store('extensions', 'public');

        $hasConflict = !$booking->vehicle->isAvailableFor($checkStart, $newEndDate->toDateString(), $booking->id);

        if ($hasConflict) {
            Extension::create([
                'booking_id'       => $booking->id,
                'additional_days'  => $additionalDays,
                'additional_price' => $additionalPrice,
                'status'           => 'rejected',
                'payment_proof'    => $path,
            ]);

            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Pengajuan perpanjangan ditolak otomatis karena kendaraan sudah dipesan pada tanggal perpanjangan yang diminta.');
        }

        Extension::create([
            'booking_id'       => $booking->id,
            'additional_days'  => $additionalDays,
            'additional_price' => $additionalPrice,
            'status'           => 'pending',
            'payment_proof'    => $path,
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Pengajuan perpanjangan berhasil dikirim. Menunggu konfirmasi admin.');
    }
}
