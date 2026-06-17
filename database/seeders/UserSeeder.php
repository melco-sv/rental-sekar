<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Administrator',
            'email'             => 'admin@scar.com',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'phone'             => '081234567890',
            'address'           => 'Jl. Admin No. 1, Jakarta',
            'id_card_number'    => null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Owner Sekar',
            'email'             => 'owner@scar.com',
            'password'          => Hash::make('owner123'),
            'role'              => 'owner',
            'phone'             => '081234567891',
            'address'           => 'Jl. Owner No. 2, Jakarta',
            'id_card_number'    => null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'           => 'Budi Santoso',
            'email'          => 'budi@example.com',
            'password'       => Hash::make('password'),
            'role'           => 'customer',
            'phone'          => '085678901234',
            'address'        => 'Jl. Sudirman No. 10, Jakarta Pusat',
            'id_card_number' => '3271234567890001',
        ]);

        User::create([
            'name'           => 'Siti Rahayu',
            'email'          => 'siti@example.com',
            'password'       => Hash::make('password'),
            'role'           => 'customer',
            'phone'          => '087890123456',
            'address'        => 'Jl. Merdeka No. 5, Bandung',
            'id_card_number' => '3271234567890002',
        ]);
    }
}
