<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.ad_type_dropdown_enable', true);
    }
    
    public function down(): void
    {
        $this->migrator->delete('home.ad_type_dropdown_enable');
    }
};
