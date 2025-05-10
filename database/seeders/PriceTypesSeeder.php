<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priceTypes = [
            [
                'name' => ['en' => 'Specify Price - Enter a specific price for the item or service.'],
                'label'=>null
            ],
            [
                'name' => ['en' => 'Free - The item or service is being offered for free.'],
                'label'=> ['en' => 'Free']
            ],
            [
                'name' => ['en' => 'Contact for Pricing - Get in touch with the seller for pricing details.'],
                'label'=> ['en' => 'Please Contact']
            ],
            [
                'name' => ['en' => 'Swap/Trade - Open to swapping or trading for other items.'],
                'label'=> ['en' => 'Swap/Trade']
            ]
        ];

        foreach ($priceTypes as $index => $type) {
            $existingType = DB::table('price_types')->where('id', $index + 1)->first();

            if ($existingType) {
                // Update the existing type
                DB::table('price_types')->where('id', $index + 1)->update([
                    'name' => json_encode($type['name']),
                    'label' => json_encode($type['label']),
                ]);
            } else {
                // Insert a new type
                DB::table('price_types')->insert([
                    'name' => json_encode($type['name']),
                    'label' => json_encode($type['label']),
                ]);
            }
        }
    }
}
