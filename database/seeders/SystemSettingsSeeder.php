<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'welcome_email_subject',
                'value' => 'Welcome to Membership MS - Your Premium Dining Experience!',
                'type' => 'string',
                'description' => 'Subject line for welcome emails sent to new members'
            ],
            [
                'key' => 'welcome_email_template',
                'value' => json_encode([
                    'subject' => 'Welcome to Membership MS - Your Premium Dining Experience!',
                    'body' => "Dear [Member Name],\n\nWelcome to Membership MS! We're excited to have you as part of our premium dining community.\n\nYour membership details:\n- Membership ID: [MS001]\n- Join Date: [Date]\n- Current Discount: [5%]\n\nStart earning rewards with every visit. The more you dine, the more you save!\n\nBest regards,\nThe Membership MS Team"
                ]),
                'type' => 'json',
                'description' => 'Welcome email template'
            ],
            [
                'key' => 'birthday_email_subject',
                'value' => 'Happy Birthday! Special Discount Just for You!',
                'type' => 'string',
                'description' => 'Subject line for birthday emails'
            ],
            [
                'key' => 'birthday_email_template',
                'value' => json_encode([
                    'subject' => 'Happy Birthday! Special Discount Just for You!',
                    'body' => "Dear [Member Name],\n\nHappy Birthday! 🎉\n\nWe hope your special day is filled with joy and wonderful moments. As a valued member of our dining community, we'd like to offer you a special birthday discount.\n\nVisit us this month and enjoy an extra 15% off your bill (in addition to your regular member discount)!\n\nYour birthday gift awaits you at [Restaurant Name].\n\nBest wishes,\nThe Membership MS Team"
                ]),
                'type' => 'json',
                'description' => 'Birthday email template'
            ],
            [
                'key' => 'auto_welcome_email',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Automatically send welcome emails to new members'
            ],
            [
                'key' => 'auto_birthday_email',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Automatically send birthday emails'
            ],
            [
                'key' => 'discount_rules',
                'value' => json_encode([
                    ['visits' => [1, 5], 'discount' => 5],
                    ['visits' => [6, 10], 'discount' => 10],
                    ['visits' => [11, 20], 'discount' => 15],
                    ['visits' => [21, 999], 'discount' => 20]
                ]),
                'type' => 'json',
                'description' => 'Discount rules based on visit count'
            ],
            [
                'key' => 'restaurant_name',
                'value' => 'Membership MS Restaurant',
                'type' => 'string',
                'description' => 'Restaurant name for emails and receipts'
            ],
            [
                'key' => 'currency',
                'value' => 'TZS',
                'type' => 'string',
                'description' => 'Currency used in the system'
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insert($setting);
        }
    }
} 