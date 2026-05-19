<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            LeadSeeder::class,
            ActivitySeeder::class,
            FollowUpSeeder::class,
            SystemConfigSeeder::class,
        ]);
    }
}
