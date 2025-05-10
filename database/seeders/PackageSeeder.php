<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\PackagePromotion;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::today();

        $packagesData = [
            [
                'name' => 'Starter',
                'duration' => 7, // 7 days
                'features' => 'Ideal for new users. Includes basic promotion options.',
                'is_default' => true,
                'is_category_count_enabled' => false,
                'is_enabled' => true,
                'adCounts' => [
                    ['quantity' => 1, 'price' => 10, 'offer_enabled' => true, 'offer_price' => 8, 'offer_start' => $today, 'offer_end' => $today->copy()->addDays(30), 'promotions' => [1, 2, 3, 4]],
                    ['quantity' => 5, 'price' => 45],
                    ['quantity' => 10, 'price' => 85, 'offer_enabled' => true, 'offer_price' => 75, 'offer_start' => $today, 'offer_end' => $today->copy()->addDays(30), 'promotions' => [1, 3]],
                    ['quantity' => 25, 'price' => 200, 'promotions' => [3]],
                    ['quantity' => 50, 'price' => 380],
                    ['quantity' => 100, 'price' => 750, 'promotions' => [1]],
                    ['quantity' => 200, 'price' => 1400, 'offer_enabled' => true, 'offer_price' => 1300, 'offer_start' => $today, 'offer_end' => $today->copy()->addDays(30), 'promotions' => [1, 3]],
                ],
            ],
            [
                'name' => 'Standard',
                'duration' => 15, // 30 days
                'features' => 'Perfect for regular users. More ads and increased visibility.',
                'is_default' => false,
                'is_category_count_enabled' => false,
                'is_enabled' => true,
                'adCounts' => [
                    ['quantity' => 1, 'price' => 20, 'offer_enabled' => true, 'offer_price' => 18, 'offer_start' => $today->copy()->addDays(10), 'offer_end' => $today->copy()->addDays(40), 'promotions' => [1, 2, 3, 4]],
                    ['quantity' => 5, 'price' => 90],
                    ['quantity' => 10, 'price' => 170, 'promotions' => [2]],
                    ['quantity' => 25, 'price' => 400],
                    ['quantity' => 50, 'price' => 750, 'offer_enabled' => true, 'offer_price' => 720, 'offer_start' => $today->copy()->addDays(10), 'offer_end' => $today->copy()->addDays(40), 'promotions' => [1]],
                    ['quantity' => 100, 'price' => 1450, 'promotions' => [3]],
                    ['quantity' => 200, 'price' => 2800, 'offer_enabled' => true, 'offer_price' => 2700, 'offer_start' => $today->copy()->addDays(10), 'offer_end' => $today->copy()->addDays(40), 'promotions' => [1, 2, 3]],
                ],
            ],
            [
                'name' => 'Premium',
                'duration' => 30, // 30 days
                'features' => 'Best for businesses. Maximum ads and promotions for high impact.',
                'is_default' => false,
                'is_category_count_enabled' => false,
                'is_enabled' => true,
                'adCounts' => [
                    ['quantity' => 1, 'price' => 30, 'offer_enabled' => true, 'offer_price' => 28, 'offer_start' => $today->copy()->addDays(20), 'offer_end' => $today->copy()->addDays(50), 'promotions' => [1, 2, 3, 4]],
                    ['quantity' => 5, 'price' => 140],
                    ['quantity' => 10, 'price' => 270, 'promotions' => [4]],
                    ['quantity' => 25, 'price' => 650],
                    ['quantity' => 50, 'price' => 1250, 'offer_enabled' => true, 'offer_price' => 1200, 'offer_start' => $today->copy()->addDays(20), 'offer_end' => $today->copy()->addDays(50), 'promotions' => [1, 3, 4]],
                    ['quantity' => 100, 'price' => 2400],
                    ['quantity' => 200, 'price' => 4600, 'offer_enabled' => true, 'offer_price' => 4500, 'offer_start' => $today->copy()->addDays(20), 'offer_end' => $today->copy()->addDays(50), 'promotions' => [1, 2, 3, 4]],
                ],
            ],
        ];

        foreach ($packagesData as $packageData) {
            $package = Package::create([
                'name' => $packageData['name'],
                'duration' => $packageData['duration'],
                'features' => $packageData['features'],
                'is_default' => $packageData['is_default'],
                'is_category_count_enabled' => $packageData['is_category_count_enabled'],
                'is_enabled' => $packageData['is_enabled'],
            ]);

            foreach ($packageData['adCounts'] as $adCountData) {
                foreach ($adCountData['promotions'] ?? [] as $promotionId) {
                    $packagePromotion = PackagePromotion::create([
                        'package_id' => $package->id,
                        'promotion_id' => $promotionId,
                    ]);

                    PackageItem::create([
                        'package_promotion_id' => $packagePromotion->id,
                        'quantity' => $adCountData['quantity'],
                        'price' => $adCountData['price'],
                        'offer_enabled' => $adCountData['offer_enabled'] ?? false,
                        'offer_price' => $adCountData['offer_price'] ?? null,
                        'offer_start' => $adCountData['offer_start'] ?? null,
                        'offer_end' => $adCountData['offer_end'] ?? null,
                    ]);
                }

                PackageItem::create([
                    'package_id' => $package->id,
                    'quantity' => $adCountData['quantity'],
                    'price' => $adCountData['price'],
                    'offer_enabled' => $adCountData['offer_enabled'] ?? false,
                    'offer_price' => $adCountData['offer_price'] ?? null,
                    'offer_start' => $adCountData['offer_start'] ?? null,
                    'offer_end' => $adCountData['offer_end'] ?? null,
                ]);
            }
        }
    }
}
