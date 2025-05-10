<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['lang_code' => 'en', 'country' => 'US', 'title' => 'English (US)', 'is_visible' => true, 'rtl' => false],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
