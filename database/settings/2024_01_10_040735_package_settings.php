<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('package.status', false);
        $this->migrator->add('package.free_ad_limit', 3);
        $this->migrator->add('package.ad_renewal_period', 'month');
    }

    public function down(): void
    {
        $this->migrator->delete('package.status');
        $this->migrator->delete('package.free_ad_limit');
        $this->migrator->delete('package.ad_renewal_period');
    }
};
