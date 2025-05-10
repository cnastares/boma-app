<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad_placements.before_footer', '');
        $this->migrator->add('ad_placements.after_header', '');
    }

    public function down(): void
    {
        $this->migrator->delete('ad_placements.before_footer');
        $this->migrator->delete('ad_placements.after_header');
    }
};
