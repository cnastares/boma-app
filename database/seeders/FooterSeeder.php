<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterSeeder extends Seeder
{
    public function run()
    {
        $footerSections = [
            [
                'title' => ['en' => 'Site Info'],
                'type' => 'predefined',
                'predefined_identifier' => 'site_with_social',
                'items' => []
            ],
            [
                'title' => ['en' => 'Popular Categories'],
                'type' => 'predefined',
                'predefined_identifier' => 'popular_category',
                'items' => []
            ],
            [
                'title' => ['en' => 'Company'],
                'type' => 'custom',
                'items' => [
                    ['name' => ['en' => 'About Us'], 'type' => 'page', 'page_id' => 1],
                    ['name' => ['en' => 'Blog'], 'type' => 'predefined', 'predefined_identifier' => 'blog'],
                    ['name' => ['en' => 'Careers'], 'type' => 'page', 'page_id' => 2],
                    ['name' => ['en' => 'Contact Us'], 'type' => 'predefined',  'predefined_identifier' => 'contact_us']
                ]
            ],
            [
                'title' => ['en' => 'Policy'],
                'type' => 'custom',
                'items' => [
                    ['name' => ['en' => 'Privacy Policy'], 'type' => 'page', 'page_id' => 4],
                    ['name' => ['en' => 'Terms of Use'], 'type' => 'page', 'page_id' => 3],
                ]
            ],
        ];

        foreach ($footerSections as $section) {
            $footerSectionId = DB::table('footer_sections')->insertGetId([
                'title' => json_encode($section['title']),
                'type' => $section['type'],
                'predefined_identifier' => $section['predefined_identifier'] ?? null,
                'order' => $section['order'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($section['items'] as $item) {
                DB::table('footer_items')->insert([
                    'footer_section_id' => $footerSectionId,
                    'name' => json_encode($item['name']),
                    'type' => $item['type'],
                    'predefined_identifier' => $item['predefined_identifier'] ?? null,
                    'page_id' => $item['page_id'] ?? null,
                    'url' => $item['url'] ?? null,
                    'order' => $item['order'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
