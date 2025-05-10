<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\User;

class FavouriteAdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Let's say each user can favorite between 1 to 5 ads
            $adsToFavourite = rand(1, 5);

            $ads = Ad::inRandomOrder()->take($adsToFavourite)->pluck('id');

            foreach ($ads as $adId) {
                DB::table('favourite_ads')->insert([
                    'user_id' => $user->id,
                    'ad_id' => $adId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
