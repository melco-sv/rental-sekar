<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Extension;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $admin  = User::where('email', 'admin@scar.com')->first();
        $budi   = User::where('email', 'budi@example.com')->first();
        $siti   = User::where('email', 'siti@example.com')->first();
        $avanza = Vehicle::where('plate_number', 'B 1234 ABC')->first();
        $jazz   = Vehicle::where('plate_number', 'B 5678 DEF')->first();
        $brio   = Vehicle::where('plate_number', 'B 7890 MNO')->first();

        // Budi — Completed booking (7 days ago to 4 days ago)
        $booking1 = Booking::create([
            'user_id'     => $budi->id,
            'vehicle_id'  => $avanza->id,
            'start_date'  => Carbon::now()->subDays(7)->toDateString(),
            'end_date'    => Carbon::now()->subDays(4)->toDateString(),
            'total_days'  => 3,
            'total_price' => 3 * $avanza->price_per_day,
            'status'      => 'completed',
            'notes'       => 'Perjalanan ke Bandung.',
        ]);

        Payment::create([
            'booking_id'  => $booking1->id,
            'amount'      => $booking1->total_price,
            'payment_date' => Carbon::now()->subDays(7)->toDateString(),
            'status'      => 'verified',
            'verified_by' => $admin->id,
            'verified_at' => Carbon::now()->subDays(7),
        ]);

        // Budi — Active booking (today to +3 days)
        $booking2 = Booking::create([
            'user_id'     => $budi->id,
            'vehicle_id'  => $jazz->id,
            'start_date'  => Carbon::now()->toDateString(),
            'end_date'    => Carbon::now()->addDays(3)->toDateString(),
            'total_days'  => 3,
            'total_price' => 3 * $jazz->price_per_day,
            'status'      => 'active',
            'notes'       => 'Keperluan bisnis.',
        ]);

        Payment::create([
            'booking_id'   => $booking2->id,
            'amount'       => $booking2->total_price,
            'payment_date' => Carbon::now()->toDateString(),
            'status'       => 'verified',
            'verified_by'  => $admin->id,
            'verified_at'  => Carbon::now(),
        ]);

        $jazz->update(['status' => 'rented']);

        // Siti — Confirmed booking (tomorrow to +5 days)
        $booking3 = Booking::create([
            'user_id'     => $siti->id,
            'vehicle_id'  => $brio->id,
            'start_date'  => Carbon::now()->addDay()->toDateString(),
            'end_date'    => Carbon::now()->addDays(5)->toDateString(),
            'total_days'  => 4,
            'total_price' => 4 * $brio->price_per_day,
            'status'      => 'confirmed',
            'notes'       => 'Liburan keluarga.',
        ]);

        Payment::create([
            'booking_id'   => $booking3->id,
            'amount'       => $booking3->total_price,
            'payment_date' => Carbon::now()->toDateString(),
            'status'       => 'verified',
            'verified_by'  => $admin->id,
            'verified_at'  => Carbon::now(),
        ]);
    }
}
