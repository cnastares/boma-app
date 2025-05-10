<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('banner.enable_carousel', false);
        $this->migrator->add('banner.banner_data',[]);
        $this->migrator->add('banner.enable_autoplay',true);
        $this->migrator->add('banner.autoplay_interval',1500);
        $this->migrator->add('banner.enable_pagination_count',false);
    }
    public function down(): void
    {
        $this->migrator->delete('banner.enable_carousel');
        $this->migrator->delete('banner.banner_data');
        $this->migrator->delete('banner.enable_autoplay');
        $this->migrator->delete('banner.enable_pagination_count');
        $this->migrator->delete('banner.autoplay_interval');
    }
};
