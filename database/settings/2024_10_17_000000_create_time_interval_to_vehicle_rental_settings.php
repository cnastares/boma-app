<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('vehicle-rental.time_interval', 30);
    }

    public function down(): void
    {
        $this->migrator->delete('vehicle-rental.time_interval');
    }
};
