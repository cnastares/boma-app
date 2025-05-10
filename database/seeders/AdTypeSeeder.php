<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdType;

class AdTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Todo: Multiple ad types
        // $adTypes = [
        //     [
        //         'name' => 'Consumer Products',
        //         'slug' => 'consumer-products',
        //         'enable_title' => true,
        //         'enable_description' => true,
        //         'enable_price' => false,
        //         'enable_tags' => true,
        //         'allowed_comment' => true,
        //         'marketplace' => CLASSIFIED_MARKETPLACE,
        //         'marketplace_options' => [[]],
        //         'enable_filters' => true,
        //         'filter_options' => [[
        //             'enable_categories' => true,
        //             'enable_sort_by' => true,
        //             'enable_date_range' => false,
        //             'enable_price_range' => true,
        //             'enable_price_range_toggle' => true,
        //         ]],
        //         'allowed_upload_image' => true,
        //         'is_default' => true,
        //         'upload_image_options' => [[]],
        //     ],
        //     [
        //         'name' => 'Real Estate',
        //         'slug' => 'real-estate',
        //         'enable_title' => true,
        //         'enable_description' => true,
        //         'enable_price' => false,
        //         'enable_tags' => true,
        //         'allowed_comment' => true,
        //         'marketplace' => CLASSIFIED_MARKETPLACE,
        //         'marketplace_options' => [[]],
        //         'enable_filters' => true,
        //         'filter_options' => [[
        //             'enable_categories' => true,
        //             'enable_sort_by' => true,
        //             'enable_date_range' => false,
        //             'enable_price_range' => true,
        //             'enable_price_range_toggle' => true,
        //         ]],
        //         'allowed_upload_image' => true,
        //         'is_default' => false,
        //         'upload_image_options' => [[]],
        //     ],
        //     [
        //         'name' => 'Shopping Hub',
        //         'slug' => 'shopping-hub',
        //         'enable_title' => true,
        //         'enable_description' => true,
        //         'enable_price' => true,
        //         'enable_tags' => true,
        //         'allowed_comment' => true,
        //         'marketplace' => ONLINE_SHOP_MARKETPLACE,
        //         'marketplace_options' => [[
        //             'enable_sku' => true,
        //             'disable_cash_on_delivery' => true,
        //         ]],
        //         'enable_filters' => true,
        //         'filter_options' => [[
        //             'enable_categories' => true,
        //             'enable_sort_by' => true,
        //             'enable_date_range' => false,
        //             'enable_price_range' => true,
        //             'enable_price_range_toggle' => true,
        //         ]],
        //         'allowed_upload_image' => true,
        //         'is_default' => false,
        //         'upload_image_options' => [[]],
        //     ],
        //     [
        //         'name' => 'Transport & Rentals',
        //         'slug' => 'transport-rentals',
        //         'enable_title' => true,
        //         'enable_description' => true,
        //         'enable_price' => false,
        //         'enable_tags' => true,
        //         'allowed_comment' => true,
        //         'marketplace' => VEHICLE_RENTAL_MARKETPLACE,
        //         'marketplace_options' => [[]],
        //         'enable_filters' => true,
        //         'filter_options' => [[
        //             'enable_categories' => true,
        //             'enable_sort_by' => true,
        //             'enable_date_range' => true,
        //             'enable_price_range' => true,
        //             'enable_price_range_toggle' => true,
        //         ]],
        //         'allowed_upload_image' => true,
        //         'is_default' => false,
        //         'upload_image_options' => [[]],
        //     ],
        //     [
        //         'name' => 'Work & Employment',
        //         'slug' => 'work-employment',
        //         'enable_title' => true,
        //         'enable_description' => true,
        //         'enable_price' => false,
        //         'enable_tags' => true,
        //         'allowed_comment' => true,
        //         'marketplace' => CLASSIFIED_MARKETPLACE,
        //         'marketplace_options' => [[]],
        //         'enable_filters' => true,
        //         'filter_options' => [[
        //             'enable_categories' => true,
        //             'enable_sort_by' => true,
        //             'enable_date_range' => true,
        //             'enable_price_range' => true,
        //             'enable_price_range_toggle' => true,
        //         ]],
        //         'allowed_upload_image' => true,
        //         'is_default' => false,
        //         'upload_image_options' => [[]],
        //     ]
        // ];

        // foreach ($adTypes as $adType) {
        //     $adType = AdType::create($adType);
        // }

        //Single ad type
        $singleAdType = [
            'name' => 'Classified',
            'slug' => 'classified',
            'enable_title' => true,
            'enable_description' => true,
            'enable_price' => false,
            'enable_tags' => true,
            'allowed_comment' => true,
            'marketplace' => CLASSIFIED_MARKETPLACE,
            'marketplace_options' => [[]],
            'enable_filters' => true,
            'filter_options' => [[
                'enable_categories' => true,
                'enable_sort_by' => true,
                'enable_date_range' => false,
                'enable_price_range' => true,
                'enable_price_range_toggle' => true,
            ]],
            'allowed_upload_image' => true,
            'is_default' => true,
            'upload_image_options' => [[]],
            ];

            $singleAdType = AdType::create($singleAdType);


        // Category::all()->each(function ($category)  use ($adType) {
        //     $category->update([
        //         'ad_type_id' => $adType?->id
        //     ]);
        // });

        // Ad::all()->each(function ($ad)  use ($adType) {
        //     $ad->update([
        //         'ad_type_id' => $adType?->id
        //     ]);
        // });
    }
}
