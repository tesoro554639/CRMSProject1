<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $salesUsers = User::where('role', 'sales')->get();
        $statuses = ['active', 'inactive'];
        $assignmentStatuses = ['pending', 'approved', 'rejected'];

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Anna', 'William', 'Mary', 'Richard', 'Jennifer', 'Thomas', 'Linda', 'Charles', 'Patricia', 'Daniel', 'Barbara', 'Matthew', 'Elizabeth', 'Anthony', 'Susan', 'Mark', 'Jessica', 'Steven', 'Karen', 'Paul', 'Nancy', 'Andrew', 'Betty', 'Joshua', 'Helen', 'Kenneth', 'Sandra', 'Kevin', 'Donna', 'Brian', 'Carol', 'George'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores'];

        for ($i = 1; $i <= 41; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName.'.'.$lastName.$i.'@company.com');

            Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => '+1'.rand(100, 999).'-'.rand(100, 999).'-'.rand(1000, 9999),
                'company' => $firstName.' '.$lastNames[array_rand($lastNames)].' Corp',
                'address' => rand(1, 999).' '.['Main St', 'Oak Ave', 'Park Blvd', 'Market St', 'Commerce Dr'][array_rand(['Main St', 'Oak Ave', 'Park Blvd', 'Market St', 'Commerce Dr'])].', Suite '.rand(100, 500),
                'status' => $statuses[array_rand($statuses)],
                'assigned_user_id' => $salesUsers->random()->id,
                'assignment_status' => $assignmentStatuses[array_rand($assignmentStatuses)],
            ]);
        }
    }
}
