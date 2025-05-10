<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('subscription.enable_paypal', false);
    }

    public function down(): void
    {
        $this->migrator->delete('subscription.enable_paypal');
    }
};
