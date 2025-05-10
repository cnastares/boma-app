<?php

namespace App\Console\Commands;

use App\Models\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MergeTranslationMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:translation-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge the new translation keys to other language files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get english translation file
        $path = lang_path('en/messages.php');
        if (File::exists($path)) {
            $defaultTranslations = require $path;
            $langPath = lang_path();  //get lang path
            $languageDirectories = $this->scanLanguages();

            //filter language code with the database language
            $languages = Language::whereIn('lang_code', $languageDirectories)->whereNot('lang_code', 'en')->pluck('lang_code');
            foreach ($languages as $langCode) {
                $langFolder = $langPath . '/' . $langCode;
                if (is_dir($langFolder)) {
                    $langFile = $langFolder . '/messages.php'; //get lang file

                    if (File::exists($langFile)) {
                        $oldTranslations = require $langFile; //get content of lang file
                        if (is_array($defaultTranslations) && is_array($oldTranslations)) {
                            // Merge the default translation with outdated translations
                            $mergedMessages = $this->MergeTranslation($defaultTranslations, $oldTranslations);
                        }
                        File::put($langFile, '<?php return ' . var_export($mergedMessages, true) . ';');
                        // Clear opcache to make sure the updated file is used
                        if (function_exists('opcache_reset')) {
                            opcache_reset();
                        }
                        $this->info("Merged messages for {$langCode}.");
                    }
                }
            }
        }
    }

    /**
     * Get directories inside lang folders
     *
     * @return array
     */
    private function scanLanguages(): array
    {
        $filtered = ['.', '..'];

        $dirs = [];
        $d = dir(lang_path());
        while (($entry = $d->read()) !== false) {
            if (is_dir(lang_path() . '/' . $entry) && !in_array($entry, $filtered)) {
                $dirs[] = $entry;
            }
        }

        return $dirs;
    }

    /**
     * Merge the new translation keys to other language files
     *
     * @param  array $defaultTranslations
     * @param array $oldTranslations
     * @return array updated translation keys
     */
    protected function MergeTranslation($defaultTranslations, $oldTranslations)
    {
        foreach ($defaultTranslations as $key => $value) {
            if (!\Arr::exists($oldTranslations, $key)) {
                if (is_array($value)) {
                    $oldTranslations[$key] = $this->MergeTranslation($value, $oldTranslations[$key] ?? []);
                } else {
                    $oldTranslations[$key] = $value;
                }
            }else{
                if (is_array($value)) {
                    $oldTranslations[$key] = $this->MergeTranslation($value, $oldTranslations[$key] ?? []);
                }
            }
        }
        return $oldTranslations;
    }
}
