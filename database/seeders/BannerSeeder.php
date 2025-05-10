<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Banner 1',
                'image' => '/banner/banner-1.jpg',
                'order'=>1
            ],
            [
                'name' => 'Banner 2',
                'image' => '/banner/banner-2.jpg',
                'order'=>2
            ],
            [
                'name' => 'Banner 3',
                'image' => '/banner/banner-3.jpg',
                'order'=>3
            ],
            [
                'name' => 'Banner 4',
                'image' => '/banner/banner-4.png',
                'order'=>4
            ],
            [
                'name' => 'Banner 5',
                'image' => '/banner/banner-5.png',
                'order'=>5
            ],
        ];
        foreach ($data as $banner) {
            Banner::firstOrCreate($banner);
        }
    }
}
