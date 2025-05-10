<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Adding initial maintenance settings
        $this->migrator->add('maintenance.headline', 'Site Under Maintenance');
        $this->migrator->add('maintenance.message', 'We are currently performing maintenance. We will be back shortly.');
        $this->migrator->add('maintenance.secret', '');
    }

    public function down(): void
    {
        // Deleting maintenance settings
        $this->migrator->delete('maintenance.headline');
        $this->migrator->delete('maintenance.message');
        $this->migrator->delete('maintenance.secret');
    }
};
