<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('external-ad.enable', false);  // Default to no moderation
        $this->migrator->add('external-ad.value', "");  // Default to no moderation
        $this->migrator->add('external-ad.ad_top_spacing', 8);  // Default to no moderation
        $this->migrator->add('external-ad.ad_right_spacing', 8);  // Default to no moderation
        $this->migrator->add('external-ad.ad_bottom_spacing', 8);  // Default to no moderation
        $this->migrator->add('external-ad.ad_left_spacing', 8);  // Default to no moderation

    }
    public function down(): void
    {
        $this->migrator->delete('external-ad.enable');
        $this->migrator->delete('external-ad.value');
        $this->migrator->delete('external-ad.ad_top_spacing');
        $this->migrator->delete('external-ad.ad_right_spacing');
        $this->migrator->delete('external-ad.ad_bottom_spacing');
        $this->migrator->delete('external-ad.ad_left_spacing');
    }
};
