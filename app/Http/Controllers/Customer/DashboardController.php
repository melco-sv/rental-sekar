<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $activeBookings = Booking::where('user_id', $userId)
            ->whereIn('status', ['active', 'confirmed'])
            ->count();

        $pendingPayments = Booking::where('user_id', $userId)
            ->whereHas('payment', fn ($q) => $q->where('status', 'pending'))
            ->count();

        $completedBookings = Booking::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $recentBookings = Booking::where('user_id', $userId)
            ->with(['vehicle', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'activeBookings',
            'pendingPayments',
            'completedBookings',
            'recentBookings'
        ));
    }
}
