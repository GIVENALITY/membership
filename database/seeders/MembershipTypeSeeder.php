<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user || !$user->hotel_id) {
            return;
        }

        $membershipTypes = [
            [
                'hotel_id' => $user->hotel_id,
                'name' => 'Basic',
                'description' => 'Perfect for occasional diners who want to enjoy some benefits',
                'price' => 50000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '5% base discount on all meals',
                    'Access to member-only events',
                    'Priority reservations',
                    'Monthly newsletter'
                ],
                'max_visits_per_month' => 10,
                'discount_rate' => 5.00,
                'discount_progression' => [
                    ['visits' => 5, 'discount' => 8.0],
                    ['visits' => 10, 'discount' => 10.0],
                    ['visits' => 15, 'discount' => 12.0],
                    ['visits' => 20, 'discount' => 15.0],
                ],
                'points_required_for_discount' => 5,
                'has_special_birthday_discount' => true,
                'birthday_discount_rate' => 20.00,
                'has_consecutive_visit_bonus' => true,
                'consecutive_visits_for_bonus' => 5,
                'consecutive_visit_bonus_rate' => 15.00,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hotel_id' => $user->hotel_id,
                'name' => 'Premium',
                'description' => 'Great for regular diners who want enhanced benefits',
                'price' => 100000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '10% base discount on all meals',
                    'Free dessert on birthday',
                    'Exclusive member events',
                    'Priority table reservations',
                    'Quarterly member appreciation dinner'
                ],
                'max_visits_per_month' => 20,
                'discount_rate' => 10.00,
                'discount_progression' => [
                    ['visits' => 3, 'discount' => 12.0],
                    ['visits' => 7, 'discount' => 15.0],
                    ['visits' => 12, 'discount' => 18.0],
                    ['visits' => 18, 'discount' => 20.0],
                ],
                'points_required_for_discount' => 3,
                'has_special_birthday_discount' => true,
                'birthday_discount_rate' => 25.00,
                'has_consecutive_visit_bonus' => true,
                'consecutive_visits_for_bonus' => 3,
                'consecutive_visit_bonus_rate' => 20.00,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hotel_id' => $user->hotel_id,
                'name' => 'VIP',
                'description' => 'Ultimate dining experience with maximum benefits',
                'price' => 200000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '15% base discount on all meals',
                    'Complimentary appetizer with every visit',
                    'Exclusive VIP events and tastings',
                    'Personal dining concierge',
                    'Annual member appreciation gala',
                    'Special chef\'s table experiences'
                ],
                'max_visits_per_month' => null, // Unlimited
                'discount_rate' => 15.00,
                'discount_progression' => [
                    ['visits' => 2, 'discount' => 18.0],
                    ['visits' => 5, 'discount' => 22.0],
                    ['visits' => 10, 'discount' => 25.0],
                    ['visits' => 15, 'discount' => 30.0],
                ],
                'points_required_for_discount' => 2,
                'has_special_birthday_discount' => true,
                'birthday_discount_rate' => 35.00,
                'has_consecutive_visit_bonus' => true,
                'consecutive_visits_for_bonus' => 2,
                'consecutive_visit_bonus_rate' => 25.00,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($membershipTypes as $type) {
            DB::table('membership_types')->insert($type);
        }
    }
} 