<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AutoUpdateBookings;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Jalankan auto-update setiap kali halaman ini dibuka
        // Sebagai fallback jika scheduler tidak berjalan
        AutoUpdateBookings::run();

        $bookings = Booking::with(['user', 'vehicle', 'payment'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'vehicle', 'payment.verifiedBy', 'extensions']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pemesanan ini tidak dapat dikonfirmasi.');
        }

        if (!$booking->payment || $booking->payment->status !== 'verified') {
            return back()->with('error', 'Pembayaran belum diverifikasi. Konfirmasi pembayaran terlebih dahulu.');
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Pemesanan berhasil dikonfirmasi.');
    }

    public function activate(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Pemesanan harus berstatus dikonfirmasi untuk diaktifkan.');
        }

        $booking->update(['status' => 'active']);
        $booking->vehicle->update(['status' => 'rented']);

        return back()->with('success', 'Pemesanan berhasil diaktifkan. Status kendaraan diubah menjadi disewa.');
    }

    public function complete(Booking $booking)
    {
        if ($booking->status !== 'active') {
            return back()->with('error', 'Pemesanan harus berstatus aktif untuk diselesaikan.');
        }

        $booking->update(['status' => 'completed']);
        $booking->vehicle->update(['status' => 'available']);

        return back()->with('success', 'Pemesanan selesai. Status kendaraan diubah menjadi tersedia.');
    }

    public function cancel(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed', 'active'])) {
            return back()->with('error', 'Pemesanan ini tidak dapat dibatalkan.');
        }

        $wasActive = in_array($booking->status, ['active', 'confirmed']);

        $booking->update(['status' => 'cancelled']);

        if ($wasActive) {
            $booking->vehicle->update(['status' => 'available']);
        }

        return back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
