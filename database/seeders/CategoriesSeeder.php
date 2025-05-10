<?php

namespace Database\Seeders;

use App\Models\AdType;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(AdTypeSeeder::class);

        $categories = [
            'Mobile Phones' => ['Smartphones', 'Feature Phones', 'Mobile Accessories', 'SIM Cards'],
            'Bikes' => ['Sports Bikes', 'Scooters', 'Cruiser Bikes', 'Bike Accessories'],
            'Electronics & Appliances' => ['Home Appliances', 'Kitchen Appliances', 'Computers & Laptops', 'Cameras & Accessories', 'TV & Video', 'Audio & Music Equipment'],
            'Furniture' => ['Bedroom Furniture', 'Living Room Furniture', 'Dining Room Furniture', 'Outdoor Furniture', 'Office Furniture'],
            'Fashion' => ['Men\'s Clothing', 'Women\'s Clothing', 'Kids\' Clothing', 'Footwear', 'Accessories'],
            'Books, Sports & Hobbies' => ['Fiction Books', 'Non-fiction Books', 'Educational Books', 'Sports Equipment', 'Music Instruments', 'Art & Collectibles', 'Fitness & Gym Equipment'],
            'Real Estate' => ['Residential for Sale', 'Residential for Rent', 'Commercial for Sale', 'Commercial for Rent', 'Land & Plots'],
            'Jobs' => ['Full-time Jobs', 'Part-time Jobs', 'Internships', 'Freelance Jobs', 'Government Jobs'],
            'Service' => ['Home Services', 'Automotive Services', 'Health & Wellness Services', 'Event Services', 'Learning & Education Services', 'Business Services'],
            'Pets' => ['Dogs & Puppies', 'Cats & Kittens', 'Birds', 'Fish & Aquariums', 'Pet Food & Accessories', 'Pet Services']
        ];

        $descriptions = [
            'Mobile Phones' => 'Find, sell, or trade a variety of mobile devices and accessories',
            'Bikes' => 'Explore or sell bikes, and related accessories.',
            'Electronics & Appliances' => 'Connect with buyers or sellers for all your electronic needs and appliance upgrades.',
            'Furniture' => 'Trade in pre-loved furniture or find new pieces to personalize your space.',
            'Fashion' => 'Buy or sell trendy clothes, accessories, and footwear for everyone.',
            'Books, Sports & Hobbies' => 'Buy or sell books, sports gear, hobbies, and collectibles.',
            'Real Estate' => 'Buy, sell, or rent properties. Explore houses, apartments, plots, and commercial spaces.',
            'Jobs' => 'Browse job listings across sectors, or post vacancies to find the right candidate.',
            'Service' => 'Browse or offer local services, from home repairs to fitness classes',
            'Pets' => 'Adopt a pet, sell pet supplies, or connect with local pet services.'
        ];

        $mainCategoryOrder = 1;

        foreach ($categories as $mainCategory => $subCategories) {

            $directory = "categories/{$mainCategory}.svg";

            $adTypeId= null;

            //Multiple AdTypes
            // if(in_array($mainCategory, ['Mobile Phones', 'Fashion', 'Electronics & Appliances'])) {
            //     $adTypeId = AdType::where('name', 'Shopping Hub')->first()->id;
            // } elseif(in_array($mainCategory, ['Real Estate', 'Fashion'])) {
            //     $adTypeId = AdType::where('name', 'Real Estate')->first()->id;
            // } elseif(in_array($mainCategory, ['Bikes'])) {
            //     $adTypeId = AdType::where('name', 'Transport & Rentals')->first()->id;
            // } elseif(in_array($mainCategory, ['Jobs'])) {
            //     $adTypeId = AdType::where('name', 'Work & Employment')->first()->id;
            // } else {
            //     $adTypeId = AdType::where('name', 'Consumer Products')->first()->id;
            // }

            //Single AdType
            $adType = AdType::where('slug', 'classified')->first();
            $adTypeId= $adType?->id ?? null;

            $parent = Category::create([
                'ad_type_id' => $adTypeId,
                'name' => $mainCategory,
                'slug' => Str::slug(Str::limit($mainCategory, 50)),
                'icon' => $directory,
                'description' => $descriptions[$mainCategory],
                'is_visible' => true,
                'order' => $mainCategoryOrder
            ]);

            $sourceFilePath = public_path("demo/categories/{$mainCategory}.svg");
            $destinationDir = public_path("demo/images/categories/");
            $destinationFilePath = $destinationDir . "{$mainCategory}.svg";

            // Ensure the destination directory exists
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true);
            }

            // Check if the SVG file exists before copying
            if (File::exists($sourceFilePath)) {
                File::copy($sourceFilePath, $destinationFilePath);
                $parent->addMedia($destinationFilePath)->toMediaCollection('category_icons');
            } else {
                \Log::error("Source file not found: {$sourceFilePath}");
            }

            $mainCategoryOrder++;

            $subCategoryOrder = 1;

            foreach ($subCategories as $subCategory) {
                Category::create([
                    'ad_type_id' => $adTypeId,
                    'name' => $subCategory,
                    'slug' => Str::slug(Str::limit($subCategory, 50)),
                    'parent_id' => $parent->id,
                    'is_visible' => true,
                    'order' => $subCategoryOrder
                ]);

                $subCategoryOrder++;
            }
        }

    }
}
