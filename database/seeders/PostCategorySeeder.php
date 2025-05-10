<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Travel' ,
            'Fitness' ,
            'Food' ,
            'Fashion' ,
            'Lifestyle' ,
            'Technology' ,
        ];

        $descriptions = [
            'Travel' => 'Explore different destinations, share travel tips, and provide recommendations for must-see attractions around the world.',
            'Fitness' => 'Focus on healthy living, workout routines, nutrition guides, and inspiring success stories to motivate readers to stay fit and active.',
            'Food' => 'Share delicious recipes, cooking techniques, ingredient spotlights, and restaurant recommendations for food enthusiasts and home cooks.',
            'Fashion' => 'Showcase the latest trends, style guides, fashion news, and tips for creating stylish outfits for various occasions.',
            'Lifestyle' => 'Cover a broad range of topics including personal development, self-care, relationships, hobbies, and overall well-being to help readers lead a balanced life.',
            'Technology' => 'Discuss the latest gadgets, software reviews, tech news, how-to guides, and tips on optimizing digital devices for productivity and entertainment.',
        ];

        $categoryOrder = 1;

        foreach($categories as $category){
            $category=PostCategory::firstOrCreate([
                'slug' => Str::slug(Str::limit($category, 50))
            ],[
                'name' => $category,
                'description' => $descriptions[$category],
                'is_visible' => true,
                'order' => $categoryOrder
            ]);
            $categoryOrder++;
        }
    }
}
