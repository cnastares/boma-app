
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('subscription.combine_subscriptions_and_orders',false);
    }

    public function down(): void
    {
        $this->migrator->delete('subscription.combine_subscriptions_and_orders');
    }
};
