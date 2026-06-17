<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'name'          => 'Toyota Avanza',
                'type'          => 'MPV',
                'plate_number'  => 'B 1234 ABC',
                'price_per_day' => 350000,
                'status'        => 'available',
                'description'   => 'Toyota Avanza 2022, 7 penumpang, AC, transmisi manual. Cocok untuk keluarga.',
            ],
            [
                'name'          => 'Honda Jazz',
                'type'          => 'Hatchback',
                'plate_number'  => 'B 5678 DEF',
                'price_per_day' => 300000,
                'status'        => 'available',
                'description'   => 'Honda Jazz 2021, 5 penumpang, AC, transmisi otomatis. Irit dan lincah di kota.',
            ],
            [
                'name'          => 'Toyota Fortuner',
                'type'          => 'SUV',
                'plate_number'  => 'B 9012 GHI',
                'price_per_day' => 600000,
                'status'        => 'available',
                'description'   => 'Toyota Fortuner 2023, 7 penumpang, 4WD, cocok untuk perjalanan jauh dan medan berat.',
            ],
            [
                'name'          => 'Mitsubishi L300',
                'type'          => 'Pickup',
                'plate_number'  => 'B 3456 JKL',
                'price_per_day' => 400000,
                'status'        => 'maintenance',
                'description'   => 'Mitsubishi L300 2020, kapasitas muatan 1 ton. Cocok untuk angkutan barang.',
            ],
            [
                'name'          => 'Honda Brio',
                'type'          => 'Hatchback',
                'plate_number'  => 'B 7890 MNO',
                'price_per_day' => 250000,
                'status'        => 'available',
                'description'   => 'Honda Brio 2022, 5 penumpang, AC, transmisi otomatis. Hemat bahan bakar.',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
