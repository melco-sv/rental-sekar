<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $dailyRevenue = Payment::where('status', 'verified')
            ->whereDate('verified_at', today())
            ->sum('amount');

        $monthlyRevenue = Payment::where('status', 'verified')
            ->whereMonth('verified_at', now()->month)
            ->whereYear('verified_at', now()->year)
            ->sum('amount');

        $yearlyRevenue = Payment::where('status', 'verified')
            ->whereYear('verified_at', now()->year)
            ->sum('amount');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = (float) Payment::where('status', 'verified')
                ->whereMonth('verified_at', $i)
                ->whereYear('verified_at', now()->year)
                ->sum('amount');
        }

        $recentBookings = Booking::with(['user', 'vehicle', 'payment'])
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $totalBookings     = Booking::where('status', 'completed')->count();
        $totalActiveRentals = Booking::whereIn('status', ['active', 'confirmed'])->count();

        return view('owner.dashboard', compact(
            'dailyRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'monthlyData',
            'recentBookings',
            'totalBookings',
            'totalActiveRentals'
        ));
    }
}
