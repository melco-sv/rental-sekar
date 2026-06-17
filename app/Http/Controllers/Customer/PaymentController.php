<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function upload(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'proof_image.required' => 'Bukti pembayaran harus diupload.',
            'proof_image.image'    => 'File harus berupa gambar.',
            'proof_image.mimes'    => 'Format gambar harus jpg, jpeg, atau png.',
            'proof_image.max'      => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = $request->file('proof_image')->store('payments', 'public');

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'proof_image'  => $path,
                'payment_date' => now()->toDateString(),
                'status'       => 'pending',
                'amount'       => $booking->total_price,
            ]
        );

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }
}
