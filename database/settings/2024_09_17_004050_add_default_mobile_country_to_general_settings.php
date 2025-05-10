
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.default_mobile_country','US');
    }

    public function down(): void
    {
        $this->migrator->delete('general.default_mobile_country');
    }
};

