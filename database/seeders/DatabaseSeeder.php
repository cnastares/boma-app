<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if (current_marketplace() === 'vehicle_rental') {
            // Call the DemoDataSeeder from the VehicleRentalMarketplace namespace directly
            $this->call('Adfox\VehicleRentalMarketplace\Database\Seeders\DatabaseSeeder');
        } else {
            $this->call([
                PromotionsSeeder::class,
                PagesSeeder::class,
                CurrencySeeder::class,
                PriceTypesSeeder::class,
                AdConditionsSeeder::class,
                LanguagesSeeder::class,
                PackageSeeder::class,
                FooterSeeder::class,
                CountriesTableSeeder::class,
                StatesTableSeeder::class,
                CitiesTableSeeder::class,
                FieldTemplateSeeder::class,
            ]);
            if(app('filament')->hasPlugin('subscription')){
                $this->call([
                    PlanFeaturesSeeder::class
                ]);
            }
            if(app('filament')->hasPlugin('appearance')){
                $this->call([BannerSeeder::class]);
            }
        }

    }
}
