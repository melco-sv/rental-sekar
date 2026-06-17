<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        $extensions = Extension::with(['booking.user', 'booking.vehicle'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.extensions.index', compact('extensions'));
    }

    public function confirm(Extension $extension)
    {
        if ($extension->status !== 'pending') {
            return back()->with('error', 'Pengajuan perpanjangan ini sudah diproses.');
        }

        $booking    = $extension->booking()->with('vehicle')->first();
        $newEndDate = Carbon::parse($booking->end_date)->addDays((int) $extension->additional_days);
        $checkStart = Carbon::parse($booking->end_date)->addDay()->toDateString();

        $hasConflict = !$booking->vehicle->isAvailableFor($checkStart, $newEndDate->toDateString(), $booking->id);

        if ($hasConflict) {
            $extension->update(['status' => 'rejected']);
            return back()->with('error', 'Perpanjangan otomatis ditolak karena kendaraan sudah ada pemesanan pada tanggal tersebut.');
        }

        // ── Konfirmasi extension ────────────────────────────────────
        $extension->update(['status' => 'confirmed']);

        // ── Update booking: tanggal, durasi, total harga ────────────
        $booking->update([
            'end_date'    => $newEndDate->toDateString(),
            'total_days'  => $booking->total_days + (int) $extension->additional_days,
            'total_price' => $booking->total_price + $extension->additional_price,
        ]);

        // ── Catat ke tabel payments agar masuk laporan pendapatan ───
        // Bukti bayar diambil dari extension.payment_proof
        Payment::create([
            'booking_id'   => $booking->id,
            'amount'       => $extension->additional_price,
            'payment_date' => today()->toDateString(),
            'proof_image'  => $extension->payment_proof,
            'status'       => 'verified',
            'verified_by'  => auth()->id(),
            'verified_at'  => now(),
        ]);
        // ───────────────────────────────────────────────────────────

        return back()->with('success', 'Perpanjangan sewa berhasil dikonfirmasi. Pembayaran sebesar Rp ' . number_format($extension->additional_price, 0, ',', '.') . ' telah dicatat ke laporan.');
    }

    public function reject(Extension $extension)
    {
        if ($extension->status !== 'pending') {
            return back()->with('error', 'Pengajuan perpanjangan ini sudah diproses.');
        }

        $extension->update(['status' => 'rejected']);

        return back()->with('success', 'Pengajuan perpanjangan ditolak.');
    }
}
