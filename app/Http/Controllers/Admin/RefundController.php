<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $refunds = Refund::with(['booking.user', 'booking.vehicle', 'processedBy'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.refunds.index', compact('refunds'));
    }

    public function process(Request $request, Refund $refund)
    {
        if ($refund->status === 'completed') {
            return back()->with('error', 'Pengembalian dana ini sudah diproses.');
        }

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'proof_image.required' => 'Bukti transfer harus diupload.',
            'proof_image.image'    => 'File harus berupa gambar.',
            'proof_image.mimes'    => 'Format harus jpg, jpeg, atau png.',
            'proof_image.max'      => 'Ukuran maksimal 2MB.',
        ]);

        $path = $request->file('proof_image')->store('refunds', 'public');

        $refund->update([
            'status'       => 'completed',
            'proof_image'  => $path,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Bukti transfer berhasil diupload. Pengembalian dana ditandai selesai.');
    }
}
