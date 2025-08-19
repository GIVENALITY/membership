<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\RestaurantSetting;

class RestaurantSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            // Set default enabled roles (admin, manager, frontdesk)
            RestaurantSetting::setValue(
                $hotel->id,
                'enabled_roles',
                ['admin', 'manager', 'frontdesk'],
                'json',
                'Enabled user roles for this restaurant'
            );

            // Set default enabled modules
            RestaurantSetting::setValue(
                $hotel->id,
                'enabled_modules',
                [
                    'physical_cards',
                    'virtual_cards',
                    'points_system',
                    'dining_management',
                    'reports_module',
                    'user_management'
                ],
                'json',
                'Enabled modules for this restaurant'
            );

            // Set default individual settings
            RestaurantSetting::setValue(
                $hotel->id,
                'receipt_required',
                false,
                'boolean',
                'Require receipt upload during checkout'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'physical_cards_enabled',
                true,
                'boolean',
                'Enable physical card tracking'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'virtual_cards_enabled',
                true,
                'boolean',
                'Enable virtual card generation'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'points_system_enabled',
                true,
                'boolean',
                'Enable points system'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'dining_management_enabled',
                true,
                'boolean',
                'Enable dining management'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'reports_enabled',
                true,
                'boolean',
                'Enable reports module'
            );

            RestaurantSetting::setValue(
                $hotel->id,
                'user_management_enabled',
                true,
                'boolean',
                'Enable user management'
            );
        }

        $this->command->info('Restaurant settings seeded successfully!');
    }
}
