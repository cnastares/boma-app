<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SEOSettings extends Settings
{
    public string $meta_title;
    public string $meta_description;
    public ?string $google_analytics_code; 
    public bool $enable_sitemap = false;
    public string $ogimage;
    public string $twitter_username;
    public string $facebook_page_id;
    public string $facebook_app_id;

    public static function group(): string
    {
        return 'seo';
    }
}
