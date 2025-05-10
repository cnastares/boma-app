<?php

namespace App\Filament\Resources\Settings\LanguageResource\Pages;

use App\Filament\Resources\Settings\LanguageResource;
use App\Models\Language;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;


class TranslateLanguage extends Page
{
    public $language;
    public $data;
    public $q;

    protected static string $resource = LanguageResource::class;

    protected static string $view = 'filament.resources.settings.language-resource.pages.translate-language';

    public function mount($record)
    {
       // Get and set language
       $this->language = Language::where('id', $record)->firstOrFail();

    }

    public function getTitle(): string | Htmlable
    {
        return 'Edit translation for ' . $this->language->title;
    }


    public function getTranslationProperty()
    {
        // Get translation
        $items = include lang_path($this->language->lang_code . "/messages.php");
        // Check if has a query and filter accordingly
        if ($this->q) {
            $items = array_filter(\Arr::dot($items), function ($item) {
                return stripos($item, $this->q) !== false;
            });
        }
        $this->data = $items;
        return \Arr::dot($items);
    }


    public function updatedData($value, $key)
    {
        try {
            $path = lang_path($this->language->lang_code . "/messages.php");

            // Avoid potential harmful content
            $clean = str_replace(['"', ';', '\\'], ' ', $value);
            $cleanedValue = trim(preg_replace('/\s+/', ' ', str_replace("'", '’', $clean)));

            // Read the current content
            $translations = include $path;
            \Arr::set($translations,$key, $cleanedValue);
            // Serialize and write back to the file
            $output = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            file_put_contents($path, $output, LOCK_EX);

             // Clear opcache to make sure the updated file is used
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

        } catch (\Throwable $th) {
            // Handle exception: consider logging or displaying an error notification.
        }
    }

}
