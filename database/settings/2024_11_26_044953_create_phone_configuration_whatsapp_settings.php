<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('phone.enable_whatsapp', true);
    }

    public function down(): void
    {
        $this->migrator->delete('phone.enable_whatsapp');
    }
};
