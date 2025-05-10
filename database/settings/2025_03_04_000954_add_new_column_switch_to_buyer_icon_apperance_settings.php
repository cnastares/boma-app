<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.switch_to_buyer_icon', '');
    }

    public function down(): void
    {
        $this->migrator->delete('appearance.switch_to_buyer_icon');
    }
};
