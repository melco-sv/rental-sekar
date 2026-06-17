<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AutoUpdateBookings;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Extension;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        AutoUpdateBookings::run();

        $now = now();

        $totalVehicles  = Vehicle::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $activeBookings = Booking::whereIn('status', ['active', 'confirmed'])->count();
        $monthlyRevenue = Payment::where('status', 'verified')
            ->whereMonth('verified_at', $now->month)
            ->whereYear('verified_at', $now->year)
            ->sum('amount');

        $todayBookings = Booking::whereDate('created_at', today())->count();
        $notifCounts   = $this->getNotifCounts();

        return view('admin.dashboard', array_merge(
            $this->getPendingCards(),
            compact('totalVehicles', 'totalCustomers', 'activeBookings', 'monthlyRevenue', 'todayBookings', 'notifCounts')
        ));
    }

    public function liveCards()
    {
        return view('admin.partials.pending-cards', array_merge(
            $this->getPendingCards(),
            ['notifCounts' => $this->getNotifCounts()]
        ));
    }

    public function notificationCounts()
    {
        $c = $this->getNotifCounts();

        return response()->json([
            'pending_bookings'   => $c['bookings'],
            'pending_payments'   => $c['payments'],
            'pending_extensions' => $c['extensions'],
            'pending_refunds'    => $c['cancellations'],
        ]);
    }

    private function getPendingCards(): array
    {
        return [
            'pendingBookings' => Booking::with(['user:id,name', 'vehicle:id,name'])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get(),

            'pendingPayments' => Payment::with([
                    'booking:id,user_id,vehicle_id',
                    'booking.user:id,name',
                    'booking.vehicle:id,name',
                ])
                ->where('status', 'pending')
                ->whereNotNull('proof_image')
                ->latest()
                ->take(4)
                ->get(),

            'pendingExtensions' => Extension::with([
                    'booking:id,user_id,vehicle_id',
                    'booking.user:id,name',
                    'booking.vehicle:id,name',
                ])
                ->where('status', 'pending')
                ->latest()
                ->take(4)
                ->get(),

            'pendingRefunds' => Refund::with([
                    'booking:id,user_id,vehicle_id',
                    'booking.user:id,name',
                    'booking.vehicle:id,name',
                ])
                ->where('status', 'pending')
                ->latest()
                ->take(4)
                ->get(),
        ];
    }

    private function getNotifCounts(): array
    {
        return [
            'bookings'      => Booking::where('status', 'pending')->count(),
            'payments'      => Payment::where('status', 'pending')->whereNotNull('proof_image')->count(),
            'extensions'    => Extension::where('status', 'pending')->count(),
            'cancellations' => Refund::where('status', 'pending')->count(),
        ];
    }
}
