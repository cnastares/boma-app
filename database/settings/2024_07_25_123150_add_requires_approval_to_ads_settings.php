
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad.admin_approval_required', false);  // Default to no moderation
    }

    public function down(): void
    {
        $this->migrator->delete('ad.admin_approval_required');
    }
};
