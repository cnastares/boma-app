<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class MergeLanguageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Dynamically retrieve the current locale
        $locale = 'en';
        $appLangPath = base_path("lang/$locale/messages.php");

        // Get the current marketplace type
        $currentMarketplace = is_ecommerce_active() ? 'eCommerce' : $this->getCurrentMarketplace();

        // Path for module language file
        $moduleLangPath = $this->getModuleLangPath($currentMarketplace, $locale);

        // Path for base language file
        $baseLangPath = base_path("base-lang/$locale/messages.php");

        // Merge languages in the correct order
        $this->mergeLanguages($baseLangPath, $moduleLangPath, $appLangPath);
    }

    private function mergeLanguages($baseLangPath, $moduleLangPath, $appLangPath)
    {
        // Load base language
        $baseLang = File::exists($baseLangPath) ? File::getRequire($baseLangPath) : [];

        // Load module language
        $moduleLang = File::exists($moduleLangPath) ? File::getRequire($moduleLangPath) : [];

        // Merge module language with base language, giving priority to module language
        $mergedLang = array_merge($baseLang, $moduleLang);

        // Check if app language file exists
        if (!File::exists($appLangPath)) {
            File::makeDirectory(dirname($appLangPath), 0755, true);
            // Create an empty app language file if it doesn't exist
            File::put($appLangPath, "<?php\n\nreturn [];\n");
        }

        $appLang = File::getRequire($appLangPath);

        // Identify missing keys
        $missingKeys = array_diff_key($mergedLang, $appLang);

        if (!empty($missingKeys)) {
            // Only add missing keys to the app language
            $finalLang = array_merge($appLang, $missingKeys);

            // Generate the PHP code that represents the array
            $newContent = "<?php\n\nreturn " . var_export($finalLang, true) . ";\n";

            // Write the updated content back to the app language file
            File::put($appLangPath, $newContent);
        }
    }

    private function getCurrentMarketplace()
    {
        return current_marketplace();
    }

    private function getModuleLangPath($marketplace, $locale)
    {
        switch ($marketplace) {
            case 'eCommerce':
                return base_path("app-modules/e-commerce/lang/$locale/messages.php");
            case 'vehicle_rental':
                return base_path("app-modules/vehicle-rental-marketplace/lang/$locale/messages.php");
            default:
                return '';
        }
    }
}
