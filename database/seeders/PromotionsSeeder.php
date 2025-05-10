<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $promotions = [
            [
                'id' => 1,
                'name' => ['en' => 'Featured Ad'],
                'description' => ['en' => 'Highlight your ad to get more attention'],
                'price' => 18.00,
                'image' => 'featured-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'id' => 2,
                'name' => ['en' => 'Spotlight Ad'],
                'description' => ['en' => 'Show your ad in a special spotlight section'],
                'price' => 25.00,
                'image' => 'spotlight-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'id' => 3,
                'name' => ['en' => 'Urgent Ad'],
                'description' => ['en' => 'Mark your ad as urgent to sell quickly'],
                'price' => 10.00,
                'image' => 'urgent-ad.svg',
                'duration' => 7, // 7 days
            ],
            [
                'id' => 4,
                'name' => ['en' => 'Website URL'],
                'description' => ['en' => 'Link your ad to your website'],
                'price' => 20.00,
                'image' => 'website-url.svg',
                'duration' => 30, // 30 days
            ],
        ];

        foreach ($promotions as $promotion) {
            $existingPromotion = DB::table('promotions')->find($promotion['id']);

            if ($existingPromotion) {
                // Update the existing promotion
                DB::table('promotions')->where('id', $promotion['id'])->update([
                    'name' => json_encode($promotion['name']),
                    'description' => json_encode($promotion['description']),
                    'price' => $promotion['price'],
                    'image' => $promotion['image'],
                    'duration' => $promotion['duration'],
                ]);
            } else {
                // Insert a new promotion
                DB::table('promotions')->insert([
                    'id' => $promotion['id'], // Including the ID in the insert
                    'name' => json_encode($promotion['name']),
                    'description' => json_encode($promotion['description']),
                    'price' => $promotion['price'],
                    'image' => $promotion['image'],
                    'duration' => $promotion['duration'],
                ]);
            }
        }
    }
}
