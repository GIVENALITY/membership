<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for occasional diners who want to enjoy some benefits',
                'price' => 50000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '5% discount on all meals',
                    'Free dessert on birthday month',
                    'Monthly newsletter with special offers'
                ],
                'max_visits_per_month' => 10,
                'discount_rate' => 5.00,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'description' => 'Great value for regular diners with enhanced benefits',
                'price' => 120000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '10% discount on all meals',
                    'Priority seating',
                    'Free appetizer on every visit',
                    'Birthday month: 15% discount',
                    'Quarterly member events'
                ],
                'max_visits_per_month' => 20,
                'discount_rate' => 10.00,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VIP',
                'description' => 'Ultimate dining experience with exclusive privileges',
                'price' => 250000,
                'billing_cycle' => 'yearly',
                'perks' => [
                    '15% discount on all meals',
                    'Reserved VIP seating area',
                    'Complimentary drinks with meals',
                    'Exclusive chef\'s table events',
                    'Personal dining concierge',
                    'Birthday month: 20% discount + free meal',
                    'Quarterly wine tasting events'
                ],
                'max_visits_per_month' => null, // Unlimited
                'discount_rate' => 15.00,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Basic',
                'description' => 'Flexible monthly membership for casual diners',
                'price' => 5000,
                'billing_cycle' => 'monthly',
                'perks' => [
                    '5% discount on all meals',
                    'Free dessert on birthday month'
                ],
                'max_visits_per_month' => 5,
                'discount_rate' => 5.00,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($membershipTypes as $type) {
            DB::table('membership_types')->insert($type);
        }
    }
} 