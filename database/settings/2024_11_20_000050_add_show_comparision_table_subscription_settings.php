
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('subscription.show_comparison_table',true);
    }

    public function down(): void
    {
        $this->migrator->delete('subscription.show_comparison_table');
    }
};
