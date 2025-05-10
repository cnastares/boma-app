<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('message.enable_make_offer', true);
    }

    public function down(): void
    {
        $this->migrator->delete('message.enable_make_offer');
    }
};
