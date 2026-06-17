<?php

namespace App\Actions;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;

class AutoUpdateBookings
{
    /**
     * Aktifkan booking yang sudah melewati start_date,
     * dan selesaikan booking yang sudah melewati end_date.
     *
     * Dipanggil dari: Artisan command (scheduler) + AdminDashboardController@index
     */
    public static function run(): array
    {
        $today = Carbon::today();

        // ── 1. Aktifkan: confirmed + start_date <= hari ini ────────
        $toActivate = Booking::where('status', 'confirmed')
            ->whereDate('start_date', '<=', $today)
            ->pluck('vehicle_id', 'id');

        if ($toActivate->isNotEmpty()) {
            Booking::whereIn('id', $toActivate->keys())->update(['status' => 'active']);
            Vehicle::whereIn('id', $toActivate->values()->unique())->update(['status' => 'rented']);
        }

        // ── 2. Selesaikan: active + end_date < hari ini ─────────────
        $toComplete = Booking::where('status', 'active')
            ->whereDate('end_date', '<', $today)
            ->pluck('vehicle_id', 'id');

        if ($toComplete->isNotEmpty()) {
            Booking::whereIn('id', $toComplete->keys())->update(['status' => 'completed']);

            // Kendaraan kembali tersedia hanya jika tidak ada booking lain yang aktif/confirmed
            $busyVehicleIds = Booking::whereIn('vehicle_id', $toComplete->values())
                ->whereIn('status', ['active', 'confirmed'])
                ->whereNotIn('id', $toComplete->keys())
                ->distinct()
                ->pluck('vehicle_id');

            $freeVehicleIds = $toComplete->values()->unique()->diff($busyVehicleIds);

            if ($freeVehicleIds->isNotEmpty()) {
                Vehicle::whereIn('id', $freeVehicleIds)->update(['status' => 'available']);
            }
        }

        return [
            'activated' => $toActivate->count(),
            'completed' => $toComplete->count(),
        ];
    }
}
