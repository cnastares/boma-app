<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('user.can_edit_registered_email',true);
    }
    public function down(): void
    {
        $this->migrator->delete('user.can_edit_registered_email');
    }
};
