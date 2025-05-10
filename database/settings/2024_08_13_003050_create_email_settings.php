
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('email.display_logo', false);  // Default to no moderation
    }

    public function down(): void
    {
        $this->migrator->delete('email.display_logo');
    }
};
