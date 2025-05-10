<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnPoliciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $returnPolicies = [
            [
                'id' => Str::uuid(),
                'policy_name' => '30-Day Return Policy',
                'description' => 'Our 30-Day Return Policy allows you to return most items within 30 days of receipt. Items must be in their original condition, with tags and packaging intact.',
                'duration' => 30,
                'exceptions' => 'Non-returnable items include gift cards, downloadable software, and final sale items.',
            ],
            [
                'id' => Str::uuid(),
                'policy_name' => '60-Day Return Policy',
                'description' => 'Our 60-Day Return Policy provides an extended period for returns. Returns must be made within 60 days of receipt and items must be unused, in original packaging.',
                'duration' => 60,
                'exceptions' => 'Items marked as final sale cannot be returned.',
            ],
            [
                'id' => Str::uuid(),
                'policy_name' => 'Return Policy for Defective Items',
                'description' => 'If you receive a defective item, you may return it within 30 days for a full refund or exchange. Please provide proof of defect and contact customer service.',
                'duration' => 30,
                'exceptions' => 'Returns for defective items require proof of defect. Non-defective returns are subject to our standard policy.',
            ],
        ];

        // Insert data into the return_policies table
        DB::table('return_policies')->insert($returnPolicies);
    }
}
