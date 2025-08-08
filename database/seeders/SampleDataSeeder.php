<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample members
        $members = [
            [
                'membership_id' => 'MS001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+255 123 456 789',
                'address' => '123 Main Street, Dar es Salaam',
                'birth_date' => '1990-12-15',
                'join_date' => '2024-01-15',
                'membership_type' => 'premium',
                'status' => 'active',
                'total_visits' => 12,
                'total_spent' => 450000,
                'current_discount_rate' => 10.00,
                'last_visit_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'membership_id' => 'MS002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'phone' => '+255 987 654 321',
                'address' => '456 Oak Avenue, Dar es Salaam',
                'birth_date' => '1985-12-20',
                'join_date' => '2024-02-01',
                'membership_type' => 'vip',
                'status' => 'active',
                'total_visits' => 8,
                'total_spent' => 320000,
                'current_discount_rate' => 15.00,
                'last_visit_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($members as $member) {
            DB::table('members')->insert($member);
        }

        // Sample dining visits
        $visits = [
            [
                'member_id' => 1,
                'bill_amount' => 45000,
                'discount_amount' => 4500,
                'final_amount' => 40500,
                'discount_rate' => 10.00,
                'visited_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'member_id' => 1,
                'bill_amount' => 32000,
                'discount_amount' => 3200,
                'final_amount' => 28800,
                'discount_rate' => 10.00,
                'visited_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'member_id' => 2,
                'bill_amount' => 28000,
                'discount_amount' => 4200,
                'final_amount' => 23800,
                'discount_rate' => 15.00,
                'visited_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($visits as $visit) {
            DB::table('dining_visits')->insert($visit);
        }

        // Sample member presence
        $presence = [
            [
                'member_id' => 1,
                'date' => now()->toDateString(),
                'check_in_time' => '14:30:00',
                'status' => 'present',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'member_id' => 2,
                'date' => now()->toDateString(),
                'check_in_time' => '13:15:00',
                'status' => 'present',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($presence as $record) {
            DB::table('member_presence')->insert($record);
        }
    }
} 