<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fieldsData = [
            'Smartphones' => [
                ['name' => 'Brand', 'type' => 'text'],
                ['name' => 'Release Date', 'type' => 'date'],
                ['name' => 'Network', 'type' => 'select', 'options' => ['4G', '5G']]
            ],
            'Mobile Accessories' => [
                ['name' => 'Accessory Type', 'type' => 'select', 'options' => ['Charger', 'Headphones', 'Cover']],
                ['name' => 'Compatibility', 'type' => 'text']
            ],
        ];

        foreach ($fieldsData as $categoryName => $fields) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) {
                continue; // Skip if the category is not found
            }

            foreach ($fields as $index => $fieldData) {
                $field = Field::create([
                    'name' => $fieldData['name'],
                    'type' => $fieldData['type'],
                    'options' => $fieldData['options'] ?? null,
                ]);

                // Link the field with the category in the CategoryField table
                CategoryField::create([
                    'category_id' => $category->id,
                    'field_id' => $field->id,
                    'order' => $index + 1
                ]);
            }
        }
    }
}
