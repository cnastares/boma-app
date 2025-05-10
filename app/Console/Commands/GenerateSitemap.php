<?php
// app/Console/Commands/GenerateSitemap.php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Page;
use Spatie\Sitemap\Sitemap;
use Illuminate\Console\Command;
use App\Settings\SEOSettings;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate a sitemap';

    public function handle()
    {
        $sitemapEnabled = app(SEOSettings::class)->enable_sitemap;

        // You may need to integrate or replace this line with your settings or configuration check.
        if ($sitemapEnabled) { // You should replace this with your configuration check like settings('seo')->is_sitemap
            $ads = Ad::all();
            $categories = Category::all();
            $pages = Page::all();

            Sitemap::create()
                ->add($ads)
                ->add($categories)
                ->add($pages)
                ->writeToFile(public_path('sitemap.xml'));
        }
    }
}

