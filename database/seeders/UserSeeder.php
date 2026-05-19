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
            'name' => 'Admin User',
            'email' => 'admin@clientpulse.io',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@clientpulse.io',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Sales User $i",
                'email' => "sales$i@clientpulse.io",
                'password' => Hash::make('password'),
                'role' => 'sales',
                'email_verified_at' => now(),
            ]);
        }
    }
}
