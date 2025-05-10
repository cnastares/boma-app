<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad.free_ad_limit', 3);  // Default limit for free ads
        $this->migrator->add('ad.ad_renewal_days', 30);  // Default renewal period in days
    }

    public function down(): void
    {
        // Remove the added settings
        $this->migrator->delete('ad.free_ad_limit');
        $this->migrator->delete('ad.ad_renewal_days');
    }
};
