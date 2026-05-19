<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'admin')->get();
        $customers = Customer::all();
        $leads = Lead::all();
        $activityTypes = ['call', 'email', 'meeting', 'note'];

        $descriptions = [
            'Initial call to discuss requirements. Client showed interest in our premium tier.',
            'Follow-up email sent with pricing details and case studies.',
            'In-person meeting at client office. Great chemistry established.',
            'Phone call to address questions about implementation timeline.',
            'Sent product demo video and brochure via email.',
            'Met with decision maker to present proposal.',
            'Quick check-in call to ensure all questions are answered.',
            'Email exchange regarding contract terms and conditions.',
            'Virtual meeting to walk through features and pricing.',
            'Left voicemail with callback number for follow-up.',
            'Discussed renewal terms and potential upsell opportunities.',
            'Sent meeting summary and next steps document.',
            'Reviewed contract with legal team.',
            'Addressed technical questions from IT department.',
            'Presented ROI analysis to executive team.',
        ];

        for ($i = 1; $i <= 80; $i++) {
            $type = $activityTypes[array_rand($activityTypes)];
            $isLead = rand(0, 1);

            if ($isLead && $leads->count() > 0) {
                $relatedId = $leads->random()->id;
                $relatedType = 'lead_id';
            } elseif ($customers->count() > 0) {
                $relatedId = $customers->random()->id;
                $relatedType = 'customer_id';
            } else {
                continue;
            }

            Activity::create([
                $relatedType => $relatedId,
                'user_id' => $users->random()->id,
                'activity_type' => $type,
                'description' => $descriptions[array_rand($descriptions)],
                'activity_date' => now()->subDays(rand(0, 60)),
            ]);
        }
    }
}
