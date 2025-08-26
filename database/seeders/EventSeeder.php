<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Hotel;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing hotels with slugs
        $hotels = Hotel::all();
        foreach ($hotels as $hotel) {
            $slug = \Str::slug($hotel->name);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure unique slug
            while (Hotel::where('slug', $slug)->where('id', '!=', $hotel->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $hotel->update(['slug' => $slug]);
        }

        // Create sample events for each hotel
        foreach ($hotels as $hotel) {
            // Sample event 1 - Wine Tasting
            Event::create([
                'hotel_id' => $hotel->id,
                'title' => 'Wine Tasting Evening',
                'description' => 'Join us for an exclusive wine tasting experience featuring premium wines from around the world. Our sommelier will guide you through the tasting process and share fascinating stories about each wine.',
                'start_date' => Carbon::now()->addDays(7)->setTime(18, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(21, 0),
                'location' => 'Main Ballroom',
                'max_capacity' => 50,
                'price' => 75.00,
                'is_public' => true,
                'is_active' => true,
                'status' => 'published'
            ]);

            // Sample event 2 - Cooking Class
            Event::create([
                'hotel_id' => $hotel->id,
                'title' => 'Chef\'s Cooking Class',
                'description' => 'Learn to cook like a professional chef in our hands-on cooking class. You\'ll prepare a three-course meal using fresh, local ingredients and take home the recipes.',
                'start_date' => Carbon::now()->addDays(14)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(14, 0),
                'location' => 'Kitchen Studio',
                'max_capacity' => 20,
                'price' => 120.00,
                'is_public' => true,
                'is_active' => true,
                'status' => 'published'
            ]);

            // Sample event 3 - Business Conference
            Event::create([
                'hotel_id' => $hotel->id,
                'title' => 'Business Networking Conference',
                'description' => 'Connect with industry leaders and entrepreneurs at our annual business networking conference. Features keynote speakers, panel discussions, and networking sessions.',
                'start_date' => Carbon::now()->addDays(21)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(21)->setTime(17, 0),
                'location' => 'Conference Center',
                'max_capacity' => 100,
                'price' => 150.00,
                'is_public' => true,
                'is_active' => true,
                'status' => 'published'
            ]);

            // Sample event 4 - Free Community Event
            Event::create([
                'hotel_id' => $hotel->id,
                'title' => 'Community Art Exhibition',
                'description' => 'Celebrate local artists at our community art exhibition. Free admission for all. Light refreshments will be served.',
                'start_date' => Carbon::now()->addDays(5)->setTime(16, 0),
                'end_date' => Carbon::now()->addDays(5)->setTime(20, 0),
                'location' => 'Gallery Hall',
                'max_capacity' => null, // Unlimited
                'price' => 0.00,
                'is_public' => true,
                'is_active' => true,
                'status' => 'published'
            ]);

            // Sample event 5 - Private Event (not public)
            Event::create([
                'hotel_id' => $hotel->id,
                'title' => 'VIP Member Dinner',
                'description' => 'Exclusive dinner for VIP members only. Special menu prepared by our executive chef.',
                'start_date' => Carbon::now()->addDays(10)->setTime(19, 0),
                'end_date' => Carbon::now()->addDays(10)->setTime(22, 0),
                'location' => 'Private Dining Room',
                'max_capacity' => 30,
                'price' => 200.00,
                'is_public' => false,
                'is_active' => true,
                'status' => 'published'
            ]);
        }
    }
}
