<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\BlogSeo;
use App\Models\BlogComment;
use App\Models\PostCategory;
use Illuminate\Support\Facades\File;

class BlogSeeder extends Seeder
{
    public function run()
    {
        $blogPosts = [
            [
                'title' => 'Maximizing Your Sales on Online Classifieds',
                'slug' => 'maximizing-sales-online-classifieds',
                'content' => 'This blog post can discuss tips and strategies for sellers to effectively use classified ad platforms like OLX to maximize their sales. Topics can include creating compelling listings, taking high-quality photos, pricing strategies, and communicating with potential buyers.',
                'reading_time' => 5,
                'published_at' => now(),
                'seo_title' => 'Tips to Maximize Sales on Classified Ads',
                'seo_description' => 'Learn effective strategies for selling on classified ad platforms...',
                'image' => '1.jpg',
                'category'=>'Technology'
            ],
            [
                'title' => 'The Ultimate Guide to Buying Second-Hand Products Safely',
                'slug' => 'buying-second-hand-products-safely',
                'content' => 'Focus on providing readers with guidance on how to make safe and smart purchases when buying second-hand items. Cover aspects like verifying product authenticity, checking product condition, safe payment methods, and arranging safe meetups.',
                'reading_time' => 6,
                'published_at' => now(),
                'seo_title' => 'Safe Buying Guide for Second-Hand Products',
                'seo_description' => 'Essential tips for verifying product authenticity and safety in second-hand purchases...',
                'image' => '2.jpg',
                'category'=>'Lifestyle'
            ],
            [
                'title' => 'Sustainable Living: The Benefits of Buying Used Items',
                'slug' => 'sustainable-living-benefits',
                'content' => 'This article can explore the environmental and economic benefits of buying used items instead of new ones. It can touch on topics like reducing waste, supporting the circular economy, and the joy of finding unique items.',
                'reading_time' => 7,
                'published_at' => now(),
                'seo_title' => 'Environmental Benefits of Buying Used Items',
                'seo_description' => 'Discover how buying used items contributes to sustainable living and a circular economy...',
                'image' => '3.jpg',
                'category'=>'Lifestyle'
            ],
            [
                'title' => 'How to Spot Great Deals on Classified Ad Platforms',
                'slug' => 'spotting-great-deals-classified-ads',
                'content' => 'Provide insights and tips on how to find the best deals when browsing through classified ads. Discuss how to use search filters effectively, the best times to look for deals, and how to negotiate prices.',
                'reading_time' => 5,
                'published_at' => now(),
                'seo_title' => 'Finding the Best Deals on Classified Ads',
                'seo_description' => 'Learn how to use search filters effectively and spot great bargains on classified ad platforms...',
                'image' => '4.jpg',
                'category'=>'Technology'
            ],
        ];


        foreach ($blogPosts as $data) {
            $category=PostCategory::where('name->en',$data['category'])->first();
            $blogPost = BlogPost::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'],
                'reading_time' => $data['reading_time'],
                'published_at' => $data['published_at'],
                'category_id'=>$category?->id
            ]);

            BlogSeo::create([
                'post_id' => $blogPost->id,
                'title' => $data['seo_title'],
                'description' => $data['seo_description'],
            ]);

            $sourceFilePath = public_path('demo/blogs/' . $data['image']);
            $destinationDir = public_path('demo/images/blogs/');
            $destinationFilePath = $destinationDir . $data['image'];

            // Ensure the destination directory exists
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            // Check if the source image exists before copying
            if (File::exists($sourceFilePath)) {
                File::copy($sourceFilePath, $destinationFilePath);
                $blogPost->addMedia($destinationFilePath)->toMediaCollection('blogs');
            } else {
                \Log::error("Source file not found: {$sourceFilePath}");
            }


            $comments = [
                ['name' => 'John Doe', 'email' => 'john@example.com', 'comment' => 'Great article!', 'status' => 'approved'],
                ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'comment' => 'Very informative, thanks!', 'status' => 'approved'],
                ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'comment' => 'This helped me a lot, thank you!', 'status' => 'approved'],
            ];

            foreach ($comments as $commentData) {
                BlogComment::create(array_merge($commentData, [
                    'post_id' => $blogPost->id,
                    'user_agent' => 'Mozilla/5.0'
                ]));
            }

        }
    }
}
