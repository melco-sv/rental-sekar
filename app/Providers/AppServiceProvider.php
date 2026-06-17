<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Extension;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Kirim notifCounts ke semua view layouts/admin.blade.php
        View::composer('layouts.admin', function ($view) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                $view->with('notifCounts', ['bookings' => 0, 'payments' => 0, 'extensions' => 0, 'cancellations' => 0]);
                return;
            }

            $view->with('notifCounts', [
                'bookings'      => Booking::where('status', 'pending')->count(),
                'payments'      => Payment::where('status', 'pending')->whereNotNull('proof_image')->count(),
                'extensions'    => Extension::where('status', 'pending')->count(),
                'cancellations' => Refund::where('status', 'pending')->count(),
            ]);
        });
    }
}
