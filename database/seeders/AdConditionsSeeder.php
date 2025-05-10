<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AdConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adConditions = [
            ['name' => ['en' => 'New']],
            ['name' => ['en' => 'Used - Like New']],
            ['name' => ['en' => 'Used - Good']],
            ['name' => ['en' => 'Used - Fair']],
        ];

        foreach ($adConditions as $index => $condition) {
            // You can use a unique identifier for each condition, such as an index or a specific key.
            $existingCondition = DB::table('ad_conditions')->where('id', $index + 1)->first();

            if ($existingCondition) {
                // Update the existing condition
                DB::table('ad_conditions')->where('id', $index + 1)->update([
                    'name' => json_encode($condition['name']),
                ]);
            } else {
                // Insert a new condition
                DB::table('ad_conditions')->insert([
                    'name' => json_encode($condition['name']),
                ]);
            }
        }
    }
}
