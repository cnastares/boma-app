<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update('pwa.icons', function (array $icons) {
            return [
                [
                    "src" => "/pwa/72x72.png",
                    "type" => "image/png",
                    "sizes" => "72x72"
                ],
                [
                    "src" => "/pwa/96x96.png",
                    "type" => "image/png",
                    "sizes" => "96x96"
                ],
                [
                    "src" => "/pwa/128x128.png",
                    "type" => "image/png",
                    "sizes" => "128x128"
                ],
                [
                    "src" => "/pwa/144x144.png",
                    "type" =>"image/png",
                    "sizes" => "144x144"
                ],
                [
                    "src" => "/pwa/152x152.png",
                    "type" => "image/png",
                    "sizes" => "152x152"
                ],
                [
                    "src" => "/pwa/192x192.png",
                    "type" => "image/png",
                    "sizes" => "192x192"
                ],
                [
                    "src" => "/pwa/384x384.png",
                    "type" => "image/png",
                    "sizes" => "384x384"
                ],
                [
                    "src" => "/pwa/512x512.png",
                    "type" => "image/png",
                    "sizes" => "512x512"
                ]
            ];
        });
    }

};
