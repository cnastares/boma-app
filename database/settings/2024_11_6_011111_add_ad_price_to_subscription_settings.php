<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('subscription.ad_price', 2.0); // Price per additional ad
        $this->migrator->add('subscription.featured_ad_price', 2.0); // Price per featured ad
        $this->migrator->add('subscription.urgent_ad_price', 2.0); // Price per urgent ad
        $this->migrator->add('subscription.spotlight_ad_price', 2.0); // Price per spotlight ad
        $this->migrator->add('subscription.website_url_price', 2.0); // Price per website URL
        $this->migrator->add('subscription.max_adjustable_count', 15); 
        $this->migrator->add('subscription.ipinfo_key', 'a15d5719db1bf2'); 

    }
    public function down(): void
    {
        $this->migrator->delete('subscription.ad_price');
        $this->migrator->delete('subscription.featured_ad_price');
        $this->migrator->delete('subscription.urgent_ad_price');
        $this->migrator->delete('subscription.spotlight_ad_price');
        $this->migrator->delete('subscription.website_url_price');
        $this->migrator->delete('subscription.max_adjustable_count');
        $this->migrator->delete('subscription.ipinfo_key');
    }
};
