<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('subscription.status', false);
        $this->migrator->add('subscription.free_ad_limit', 3);
        $this->migrator->add('subscription.stripe_webhook_secret', '');
    }

    public function down(): void
    {
        $this->migrator->delete('subscription.status');
        $this->migrator->delete('subscription.free_ad_limit');
        $this->migrator->delete('subscription.stripe_webhook_secret');
    }
};
