<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad.can_post_without_image', false);
        $this->migrator->add('ad.placeholder_image', '');
    }

    public function down(): void
    {
        $this->migrator->delete('ad.can_post_without_image');
        $this->migrator->delete('ad.placeholder_image');
    }
};
