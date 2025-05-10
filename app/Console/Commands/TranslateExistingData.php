<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Field;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TranslateExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:existing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert existing non-multilingual data into multilingual format using Spatie Translatable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale=config('app.locale');
        $this->translateCategoryData($locale);
        $this->translateFieldData($locale);
        $this->translateFieldGroupData($locale);
    }

    /**
     * Translate existing category data
     *
     * @return void
     */
    private function translateCategoryData($locale)
    {
        $existingCategories =  DB::table('categories')->get();
        foreach ($existingCategories as $key => $category) {
            // Check if the 'name' field is already in JSON format
            if (!$this->isJson($category->name)) {
                DB::table('categories')->where('id',$category->id)->update([
                    'name' => [$locale => $category->name]
                ]);
            }
            // Check if the 'description' field is already in JSON format
            if (!$this->isJson($category->description)) {
                DB::table('categories')->where('id',$category->id)->update([
                    'description' => [$locale => $category->description]
                ]);
            }
        }
        $this->info('Categories converted to JSON format successfully.');
    }

        /**
     * Translate existing field data
     *
     * @return void
     */
    private function translateFieldData($locale)
    {
        $existingFields =DB::table('fields')->get();
        foreach ($existingFields as $key => $field) {
            // Check if the 'name' field is already in JSON format
            if ($this->isJson($field->name)) {
                $this->info("Field ID {$field->id} name is already in JSON format.");
                continue;
            }
            DB::table('fields')->where('id',$field->id)->update([
                'name' => [$locale => $field->name]
            ]);
        }
        $this->info('Fields names converted to JSON format successfully.');
    }

            /**
     * Translate existing field group data
     *
     * @return void
     */
    private function translateFieldGroupData($locale)
    {
        $existingFieldGroups = DB::table('field_groups')->get();
        foreach ($existingFieldGroups as $key => $fieldGroup) {
            // Check if the 'name' field is already in JSON format
            if ($this->isJson($fieldGroup->name)) {
                $this->info("Field Group ID {$fieldGroup->id} name is already in JSON format.");
                continue;
            }
            DB::table('field_groups')->where('id',$fieldGroup->id)->update([
                'name' => [$locale => $fieldGroup->name]
            ]);
        }
        $this->info('Field Groups names converted to JSON format successfully.');
    }

    /**
     * Check if a string is a valid JSON.
     *
     * @param  string  $string
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
