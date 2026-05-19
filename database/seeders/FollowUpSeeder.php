<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class FollowUpSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'admin')->get();
        $customers = Customer::all();
        $leads = Lead::all();

        $titles = [
            'Follow up on proposal',
            'Schedule demo call',
            'Send contract for review',
            'Check on decision timeline',
            'Address technical questions',
            'Review pricing options',
            'Confirm meeting next week',
            'Send additional documentation',
            'Discuss implementation plan',
            'Renewal discussion',
            'Check-in call',
            'Product update call',
            'Quarterly review meeting',
            'Contract negotiation',
            'ROI presentation follow-up',
        ];

        for ($i = 1; $i <= 40; $i++) {
            $isLead = rand(0, 1);
            $isOverdue = rand(0, 1);
            $isCompleted = rand(0, 1);

            if ($isLead && $leads->count() > 0) {
                $relatedId = $leads->random()->id;
                $relatedType = 'lead_id';
            } elseif ($customers->count() > 0) {
                $relatedId = $customers->random()->id;
                $relatedType = 'customer_id';
            } else {
                continue;
            }

            $dueDate = $isOverdue
                ? now()->subDays(rand(1, 15))->toDateString()
                : now()->addDays(rand(1, 30))->toDateString();

            FollowUp::create([
                $relatedType => $relatedId,
                'user_id' => $users->random()->id,
                'title' => $titles[array_rand($titles)],
                'description' => 'Detailed follow-up required for '.($isLead ? 'lead' : 'customer').' engagement.',
                'due_date' => $dueDate,
                'status' => $isCompleted ? 'completed' : 'pending',
            ]);
        }
    }
}
