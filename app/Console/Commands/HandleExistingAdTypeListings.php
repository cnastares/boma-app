<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdType;
use App\Models\Category;
use Illuminate\Console\Command;

class HandleExistingAdTypeListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad-type:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default AdType to existing Categories and Ads.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure default AdType exists
        $adType = AdType::firstOrCreate(
            ['is_default' => true],
            [
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
                'upload_image_options' => [[]],
            ]
        );

        // Update categories with default AdType
        Category::whereNull('ad_type_id')->update(['ad_type_id' => $adType?->id]);

        // Update ads with default AdType
        Ad::whereNull('ad_type_id')->update(['ad_type_id' => $adType?->id]);

        $this->info('Default AdType assigned to all categories and ads.');
    }
}
