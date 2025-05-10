<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Addon extends Model
{
    use Sushi;


    /**
     * Check for a new version of the addon.
     *
     * @param string $appCode The app code of the addon.
     * @return array
     */
    public static function checkNewVersion($appCode)
    {
        try {
            $response = Http::post('https://supportapi.saasforest.com/api/v1/version-check', [
                'app_code' => $appCode
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to retrieve version info',
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function getRows()
    {
        $addons = [];
        $addonPath = base_path('app-modules');

        if (File::exists($addonPath) && File::isDirectory($addonPath)) {
            $index = 0;
            foreach (File::directories($addonPath) as $directory) {
                if (File::exists($directory . '/installed')) {
                    $moduleName = basename($directory);
                    $configPath = $directory . '/config/' . $moduleName . '.php';

                    if (File::exists($configPath)) {
                        $config = require $configPath;
                    } else {
                        $config = [
                            'version' => null,
                            'build_version' => null,
                            'min_main_version' => null,
                            'app_code'  => null,
                        ];
                    }

                    $versionCheck = static::checkNewVersion($config['app_code'] ?? null);

                    $updateAvailable = 'uptodate';

                    $downloadLink = null;

                    if (isset($versionCheck['success']) && $versionCheck['success'] === true && isset($versionCheck['data'])) {
                        $updateAvailable = ($versionCheck['data']['buildVersion'] != $config['build_version'])
                            ? 'available'
                            : 'uptodate';
                        $downloadLink = $versionCheck['data']['download_link'];
                    }

                    if ($downloadLink) {
                        $addons[] = [
                            'id' => $index++,
                            'name' => $moduleName,
                            'path' => $directory,
                            'version' => $config['version'] ?? null,
                            'build_version' => $config['build_version'] ?? null,
                            'min_main_version' => $config['min_main_version'] ?? null,
                            'app_code' => $config['app_code'] ?? null,
                            'update_available' => $updateAvailable,
                            'download_link' => $downloadLink
                        ];
                    }
                }
            }
        }

        return $addons;
    }
}
