
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.europa_cookie_consent_enabled', false);
    }

    public function down(): void
    {
        $this->migrator->delete('general.europa_cookie_consent_enabled');
    }
};

