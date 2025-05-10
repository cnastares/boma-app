<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.meta_title', 'AdFox - Buy and Sell Anything, Anywhere, Anytime');
        $this->migrator->add('seo.meta_description', 'AdFox is your go-to marketplace for buying and selling goods locally and nationally. Simple, fast, and efficient. Discover great deals today!');
        $this->migrator->add('seo.google_analytics_code', null);
        $this->migrator->add('seo.enable_sitemap', false);
        $this->migrator->add('seo.ogimage', '');
        $this->migrator->add('seo.twitter_username', '');
        $this->migrator->add('seo.facebook_page_id', '');
        $this->migrator->add('seo.facebook_app_id', '');
    }

    public function down(): void
    {
        $this->migrator->delete('seo.meta_title');
        $this->migrator->delete('seo.meta_description');
        $this->migrator->delete('seo.google_analytics_code');
        $this->migrator->delete('seo.enable_sitemap');
        $this->migrator->delete('seo.ogimage');
        $this->migrator->delete('seo.twitter_username');
        $this->migrator->delete('seo.facebook_page_id');
        $this->migrator->delete('seo.facebook_app_id');
    }
};
