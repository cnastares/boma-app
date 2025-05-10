<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ui_customization.ad_detail_page', ['enable_location_below_title' => true]);
    }

    public function down(): void
    {
        $this->migrator->delete('ui_customization.ad_detail_page');
    }
};
