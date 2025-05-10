<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad.ad_duration', 30);  // Default to 30 days
        $this->migrator->add('ad.image_limit', 5);  // Default to 5 images per ad
        $this->migrator->add('ad.ad_moderation', false);  // Default to no moderation
        $this->migrator->add('ad.featured_ad_price', 10.00);  // Default price
        $this->migrator->add('ad.featured_ad_duration', 7);  // Default to 7 days
        $this->migrator->add('ad.spotlight_ad_price', 5.00);  // Default price
        $this->migrator->add('ad.spotlight_ad_duration', 7);  // Default to 7 days
        $this->migrator->add('ad.urgent_ad_price', 2.00);  // Default price
        $this->migrator->add('ad.urgent_ad_duration', 3);  // Default to 3 days
    }

    public function down(): void
    {
        $this->migrator->delete('ad.ad_duration');
        $this->migrator->delete('ad.image_limit');
        $this->migrator->delete('ad.ad_moderation');
        $this->migrator->delete('ad.featured_ad_price');
        $this->migrator->delete('ad.featured_ad_duration');
        $this->migrator->delete('ad.spotlight_ad_price');
        $this->migrator->delete('ad.spotlight_ad_duration');
        $this->migrator->delete('ad.urgent_ad_price');
        $this->migrator->delete('ad.urgent_ad_duration');
    }
};
