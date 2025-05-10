<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesSeeder extends Seeder
{
    public function run()
    {
        $features = [
            // Listings
            ['slug' => 'number-of-images', 'name' => 'Number of Images', 'description' => 'Max number of images per listing.'],
            ['slug' => 'video-posting', 'name' => 'Video Posting', 'description' => 'Ability to post videos.'],
            ['slug' => 'ads', 'name' => 'Ads', 'description' => 'Enabled for posting ads.'],
            // User/Store Page Customization
            ['slug' => 'banner', 'name' => 'Banner', 'description' => 'Number of banners on user/store page.'],
            ['slug' => 'user-profile', 'name' => 'User Profile', 'description' => 'Profile description and information.'],
            ['slug' => 'user-rating', 'name' => 'User Rating', 'description' => 'User rating system.'],
            ['slug' => 'filter-options', 'name' => 'Filter Options', 'description' => 'Available filter options for users.'],
            // Advanced Marketing Tools
            ['slug' => 'seo', 'name' => 'SEO', 'description' => 'Availability of SEO tools.'],
            ['slug' => 'utm-parameters', 'name' => 'UTM Parameters', 'description' => 'Availability of UTM tracking.'],
            // Detailed Statistics
            ['slug' => 'number-of-visits', 'name' => 'Number of Visits', 'description' => 'Tracking number of visits.'],
            ['slug' => 'traffic-source', 'name' => 'Traffic Source', 'description' => 'Tracking source of traffic.'],
            ['slug' => 'average-view-time', 'name' => 'Average View Time', 'description' => 'Tracking average view time.'],
            ['slug' => 'number-of-favorites', 'name' => 'Number of Favorites', 'description' => 'Tracking favorites.'],
            ['slug' => 'contact-conversion-rate', 'name' => 'Contact Conversion Rate', 'description' => 'Tracking conversion rate.'],
            ['slug' => 'clicks-on-link-url', 'name' => 'Clicks on Link URL', 'description' => 'Tracking clicks on URLs.'],
            ['slug' => 'demographic-analysis', 'name' => 'Demographic Analysis', 'description' => 'Analysis of user demographics.'],
            ['slug' => 'product-engagement', 'name' => 'Product Engagement', 'description' => 'Tracking product engagement.'],
            ['slug' => 'product-performance-analysis', 'name' => 'Product Performance Analysis', 'description' => 'Performance tracking of products.'],
            // Performance Reports
            ['slug' => 'boost-analysis', 'name' => 'Boost Analysis', 'description' => 'Analysis of boosts.'],
            ['slug' => 'custom-reports', 'name' => 'Custom Reports', 'description' => 'Availability of custom reports.'],
            ['slug' => 'automated-alerts-and-insights', 'name' => 'Automated Alerts and Insights', 'description' => 'Automated alerts availability.'],
            // Chat
            ['slug' => 'monthly-interaction-limit', 'name' => 'Monthly Interaction Limit', 'description' => 'Limits on interactions per month.'],
            // Sales Automation Tools
            ['slug' => 'sales-automation-tools', 'name' => 'Sales Automation Tools', 'description' => 'Availability of sales automation tools.'],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }

}
