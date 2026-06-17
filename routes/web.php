<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Owner;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Customer routes
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer', 'verified'])->group(function () {
    Route::get('/dashboard', [Customer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/vehicles', [Customer\VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{vehicle}', [Customer\VehicleController::class, 'show'])->name('vehicles.show');

    Route::get('/bookings', [Customer\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{vehicle}', [Customer\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [Customer\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [Customer\BookingController::class, 'show'])->name('bookings.show');

    Route::post('/payments/{booking}/upload', [Customer\PaymentController::class, 'upload'])->name('payments.upload');

    Route::get('/bookings/{booking}/extension/create', [Customer\ExtensionController::class, 'create'])->name('extensions.create');
    Route::post('/bookings/{booking}/extension', [Customer\ExtensionController::class, 'store'])->name('extensions.store');
    Route::get('/extensions', [Customer\ExtensionController::class, 'index'])->name('extensions.index');

    Route::post('/bookings/{booking}/cancel', [Customer\BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/profile', [Customer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Customer\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [Customer\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications/counts', [Admin\DashboardController::class, 'notificationCounts'])->name('notifications.counts');
    Route::get('/dashboard/cards', [Admin\DashboardController::class, 'liveCards'])->name('dashboard.cards');

    Route::resource('/vehicles', Admin\VehicleController::class);
    Route::post('/vehicles/{id}/restore', [Admin\VehicleController::class, 'restore'])->name('vehicles.restore');

    Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [Admin\BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/activate', [Admin\BookingController::class, 'activate'])->name('bookings.activate');
    Route::post('/bookings/{booking}/complete', [Admin\BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [Admin\BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [Admin\PaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/extensions', [Admin\ExtensionController::class, 'index'])->name('extensions.index');
    Route::post('/extensions/{extension}/confirm', [Admin\ExtensionController::class, 'confirm'])->name('extensions.confirm');
    Route::post('/extensions/{extension}/reject', [Admin\ExtensionController::class, 'reject'])->name('extensions.reject');

    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [Admin\ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [Admin\ReportController::class, 'exportExcel'])->name('reports.excel');

    Route::get('/refunds', [Admin\RefundController::class, 'index'])->name('refunds.index');
    Route::post('/refunds/{refund}/process', [Admin\RefundController::class, 'process'])->name('refunds.process');
});

// Owner routes
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [Owner\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [Owner\ReportController::class, 'exportPdf'])->name('reports.pdf');
});

require __DIR__ . '/auth.php';
