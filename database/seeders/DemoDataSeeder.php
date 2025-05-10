<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Artisan;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if (current_marketplace() === 'vehicle_rental') {
            // Call the DemoDataSeeder from the VehicleRentalMarketplace namespace directly
            $this->call('Adfox\VehicleRentalMarketplace\Database\Seeders\DemoDataSeeder');
        } else {
            $this->call([
                UsersSeeder::class,
                CategoriesSeeder::class,
                AdsSeeder::class,
                FieldsSeeder::class,
                AdPromotionsSeeder::class,
                FavouriteAdsSeeder::class,
                // ReturnPoliciesTableSeeder::class
            ]);
            Artisan::call('convert:descriptions');
        }
    }
}
