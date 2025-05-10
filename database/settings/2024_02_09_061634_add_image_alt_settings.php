<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad.allow_image_alt_tags', false);
        $this->migrator->add('ad.user_verification_required', false);
    }

    public function down(): void
    {
        $this->migrator->delete('ad.allow_image_alt_tags');
        $this->migrator->delete('ad.user_verification_required');
    }
};
