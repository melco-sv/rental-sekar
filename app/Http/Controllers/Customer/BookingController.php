<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Vehicle;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['vehicle', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Vehicle $vehicle)
    {
        // Maintenance tidak bisa dipesan sama sekali
        if ($vehicle->status === 'maintenance') {
            return redirect()->route('customer.vehicles.index')
                ->with('error', 'Kendaraan ini sedang dalam perawatan dan tidak bisa dipesan.');
        }

        // Ambil semua booking aktif/mendatang untuk ditampilkan di form
        $bookedRanges = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('end_date', '>=', today())
            ->orderBy('start_date')
            ->get(['start_date', 'end_date', 'status'])
            ->map(fn ($b) => [
                'start'  => $b->start_date->format('Y-m-d'),
                'end'    => $b->end_date->format('Y-m-d'),
                'status' => $b->status,
            ]);

        return view('customer.bookings.create', compact('vehicle', 'bookedRanges'));
    }

    public function store(StoreBookingRequest $request)
    {
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        if ($vehicle->status === 'maintenance') {
            return back()->with('error', 'Kendaraan ini sedang dalam perawatan.');
        }

        if (!$vehicle->isAvailableFor($request->start_date, $request->end_date)) {
            return back()->withInput()
                ->with('error', 'Kendaraan tidak tersedia pada tanggal tersebut. Silakan pilih tanggal lain.');
        }

        $totalDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        if ($totalDays < 1) {
            return back()->withInput()->with('error', 'Minimal sewa 1 hari.');
        }

        $totalPrice = $totalDays * $vehicle->price_per_day;

        $booking = Booking::create([
            'user_id'     => auth()->id(),
            'vehicle_id'  => $vehicle->id,
            'start_date'  => $request->start_date,
            'start_time'  => $request->start_time,
            'end_date'    => $request->end_date,
            'total_days'  => $totalDays,
            'total_price' => $totalPrice,
            'status'      => 'pending',
            'notes'       => $request->notes,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount'     => $totalPrice,
            'status'     => 'pending',
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Pemesanan berhasil dibuat! Silakan upload bukti pembayaran.');
    }

    public function show(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);

        $booking->load(['vehicle', 'payment', 'extensions', 'refund']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Pemesanan tidak dapat dibatalkan pada status ini.');
        }

        $payment = $booking->payment;
        if (!$payment || $payment->status !== 'verified') {
            return back()->with('error', 'Pemesanan tidak dapat dibatalkan karena pembayaran belum terverifikasi.');
        }

        $pickup   = $booking->pickup_datetime;
        $hoursLeft = now()->diffInMinutes($pickup, false) / 60;

        $percentage = $hoursLeft < 2 ? 30 : 50;
        $amountPaid = (float) $booking->payments()->where('status', 'verified')->sum('amount');
        $refundAmount = $amountPaid * $percentage / 100;

        Refund::create([
            'booking_id'        => $booking->id,
            'amount_paid'       => $amountPaid,
            'refund_percentage' => $percentage,
            'refund_amount'     => $refundAmount,
            'status'            => 'pending',
        ]);

        $booking->update(['status' => 'cancelled']);
        $booking->vehicle->update(['status' => 'available']);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Pemesanan berhasil dibatalkan. Pengembalian dana ' . $percentage . '% (Rp ' . number_format($refundAmount, 0, ',', '.') . ') sedang diproses.');
    }
}
