<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => ['en' => 'Starter'],
                'slug' => 'starter',
                'description' => ['en' => 'Get started with a limited budget and 10 ads per month.'],
                'is_active' => true,
                'price' => 10,
                'currency' => 'USD',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'stripe_product_id'=>'prod_QLBXJ2AmB2mRe3',
                'price_id' => 'price_1PUVAYGgztvb8gyCNxfNFqgS', // Replace with your actual Stripe price ID
                'features' => [
                    [
                        'name' => ['en' => 'Ad count'],
                        'type' => 'ad_count',
                        'value' => 5
                    ],
                    [
                        'name' => ['en' => 'Featured Ad'],
                        'type' => 'promotion',
                        'value' => 6,
                        'promotion_id' => 1
                    ],
                    [
                        'name' => ['en' => 'Spotlight Ad'],
                        'type' => 'promotion',
                        'value' => 3,
                        'promotion_id' => 2
                    ]
                ]
            ],
            [
                'name' => ['en' => 'Pro'],
                'slug' => 'pro',
                'description' => ['en' => 'Boost your reach with 50 ads per month for optimal exposure.'],
                'is_active' => true,
                'price' => 25,
                'currency' => 'USD',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'stripe_product_id'=>'prod_QPFsl8AVd1r1o1',
                'price_id' => 'price_1PYRMuGgztvb8gyCh5cxISMy', // Replace with your actual Stripe price ID
                'features' => [
                    [
                        'name' => ['en' => 'Ad count'],
                        'type' => 'ad_count',
                        'value' => 10
                    ],
                    [
                        'name' => ['en' => 'Urgent Ad'],
                        'type' => 'promotion',
                        'value' => 8,
                        'promotion_id' => 3
                    ],
                    [
                        'name' => ['en' => 'Website URL'],
                        'type' => 'promotion',
                        'value' => 5,
                        'promotion_id' => 4
                    ]
                ]
            ],
            [
                'name' => ['en' => 'Enterprise'],
                'slug' => 'enterprise',
                'description' => ['en' => 'Scale your campaigns with 100 ads per month for maximum impact.'],
                'is_active' => true,
                'price' => 50,
                'currency' => 'USD',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'stripe_product_id'=>'prod_QPFuo4Pw78RL0d',
                'price_id' => 'price_1PYROcGgztvb8gyCH8vj7N3G', // Replace with your actual Stripe price ID
                'features' => [
                    [
                        'name' => ['en' => 'Ad count'],
                        'type' => 'ad_count',
                        'value' => 25
                    ],
                    [
                        'name' => ['en' => 'Spotlight Ad'],
                        'type' => 'promotion',
                        'value' => 15,
                        'promotion_id' => 2
                    ],
                    [
                        'name' => ['en' => 'Featured Ad'],
                        'type' => 'promotion',
                        'value' => 18,
                        'promotion_id' => 1
                    ]
                ]
            ],
        ];
        foreach ($subscriptions as $key => $subscription) {
            $planFeatures = $subscription['features'];
            unset($subscription['features']);
            $slug = Plan::whereSlug($subscription['slug'])->first();
            if (!$slug) {
                $plan = Plan::create($subscription);
                if (count($planFeatures)) {
                    foreach ($planFeatures as $feature) {
                        PlanFeature::create(
                            [
                                ...$feature,
                                ...['plan_id' => $plan->id]
                            ]
                        );
                    }
                }
            }
        }

    }
}
