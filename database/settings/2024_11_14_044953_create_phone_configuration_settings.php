<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('phone.enable_phone', true);
        $this->migrator->add('phone.enable_number_reveal_duplicate', false);
        $this->migrator->add('phone.enable_login_user_number_reveal', true);
    }

    public function down(): void
    {
        $this->migrator->delete('phone.enable_phone');
        $this->migrator->delete('phone.enable_number_reveal_duplicate');
        $this->migrator->delete('phone.enable_login_user_number_reveal');
    }
};
