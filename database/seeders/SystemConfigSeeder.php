<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        SystemConfig::firstOrCreate(
            ['id' => 1],
            [
                'app_name' => 'ClientPulse',
                'currency_code' => '$',
                'company_email' => 'contact@clientpulse.io',
                'company_phone' => '+1 (555) 123-4567',
                'company_address' => '123 Business Avenue, Suite 500, New York, NY 10001',
                'default_lead_status' => 'new',
                'default_lead_priority' => 'medium',
                'reset_link_expiry' => 60,
            ]
        );
    }
}
