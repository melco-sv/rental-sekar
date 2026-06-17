<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['booking.user', 'booking.vehicle', 'verifiedBy'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $payment->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $payment->booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Pembayaran berhasil diverifikasi. Status pemesanan diubah menjadi dikonfirmasi.');
    }

    public function reject(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $payment->update(['status' => 'rejected']);

        return back()->with('success', 'Pembayaran ditolak. Customer dapat mengupload ulang bukti pembayaran.');
    }
}
