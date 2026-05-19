<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $salesUsers = User::where('role', 'sales')->get();
        $statuses = ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $sources = ['Website', 'Referral', 'Cold Call', 'LinkedIn', 'Trade Show', 'Advertisement', 'Social Media'];

        $names = [
            'Acme Corporation', 'TechStart Inc', 'Global Solutions', 'InnovateTech', 'Prime Industries',
            'Digital Dynamics', 'NextGen Systems', 'Alpha Enterprises', 'Beta Corp', 'Gamma Solutions',
            'Delta Technologies', 'Omega Consulting', 'Sigma Group', 'Vertex Inc', 'Apex Solutions',
            'Horizon Tech', 'Summit Industries', 'Peak Systems', 'Mountain View Corp', 'Valley Enterprises',
            'Oceanic Group', 'Pacific Solutions', 'Atlantic Tech', 'Arctic Inc', 'Northern Systems',
            'Southern Tech', 'Eastern Corp', 'Western Solutions', 'Central Industries', 'United Technologies',
            'International Corp', 'National Sales', 'Regional Systems', 'Local Enterprises', 'Global Trade',
            'Premium Services', 'Elite Solutions', 'Premier Inc', 'Elite Group', 'Premier Tech',
            'Advanced Systems', 'Modern Technologies', 'Future Corp', 'Next Generation', 'Tomorrow Solutions',
            'Smart Systems', 'Intelligent Tech', 'Digital Solutions', 'Cyber Corp', 'Network Systems',
            'DataTech Inc', 'Cloud Solutions', 'Web Services', 'App Development', 'Software Corp',
            'Tech Support', 'IT Solutions', 'Consulting Group', 'Advisory Services', 'Professional Corp',
        ];

        for ($i = 1; $i <= 61; $i++) {
            $status = $statuses[array_rand($statuses)];
            $name = $names[array_rand($names)].' '.$i;

            $lead = Lead::create([
                'name' => $name,
                'email' => 'lead'.$i.'@example.com',
                'phone' => '+1'.rand(100, 999).'-'.rand(100, 999).'-'.rand(1000, 9999),
                'source' => $sources[array_rand($sources)],
                'status' => $status,
                'priority' => $priorities[array_rand($priorities)],
                'expected_value' => rand(1000, 100000),
                'notes' => 'Initial contact made via '.$sources[array_rand($sources)].'. Client interested in our services.',
                'assigned_user_id' => $salesUsers->random()->id,
            ]);

            if ($status === 'lost') {
                $lead->update([
                    'lost_category' => ['Price', 'Competitor', 'No Response', 'Lost Interest', 'Timeline'][array_rand(['Price', 'Competitor', 'No Response', 'Lost Interest', 'Timeline'])],
                    'lost_reason' => 'Client decided to go with competitor offering.',
                    'lost_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
