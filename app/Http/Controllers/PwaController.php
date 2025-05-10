<?php
namespace App\Http\Controllers;

use App\Settings\PwaSettings;
use Illuminate\Support\Facades\Storage;

class PwaController extends Controller
{

    public function manifest(PwaSettings $settings)
    {
        //Getting icons for PWA
        $icons = $this->generatePwaIcons($settings->icons);
        //Get PWA setting data
        $manifest = [
            "name" => $settings->name,
            "short_name" => $settings->short_name,
            "start_url" => $settings->start_url,
            "display" => $settings->display,
            "background_color" => $settings->background_color,
            "theme_color" => $settings->theme_color,
            "description" => $settings->description,
            "icons" => $icons,
        ];
        // Encode the manifest as JSON
        $jsonContent = json_encode($manifest);

        // Set headers and return the JSON response
        return response($jsonContent)
            ->header('Content-Type', 'application/manifest+json');
    }

    /**
     * Generate icons for PWA
     *
     * @param [array] $icons
     * @return array
     */
    private function generatePwaIcons($icons)
    {
        //Map through each icon set src to be storage full URL
        return \Arr::mapWithKeys($icons, function (array $item, int $key) {
            $item['src'] = Storage::url($item['src']);
            return [$item];
        });
    }

}
