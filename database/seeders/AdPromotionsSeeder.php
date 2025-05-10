<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\Promotion;

class AdPromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's assume we want to seed promotions for 50% of the ads.
        $ads = Ad::all()->random(Ad::count() * 0.5);
        $promotions = Promotion::all();

        foreach ($ads as $ad) {
            // Select a random promotion for this ad
            $promotion = $promotions->random();

            $startDate = now();
            $endDate = $startDate->copy()->addDays($promotion->duration);

            DB::table('ad_promotions')->insert([
                'ad_id' => $ad->id,
                'price' =>  $promotion->price,
                'promotion_id' => $promotion->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
