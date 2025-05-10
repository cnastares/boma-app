<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('category-ad.image_height', '160');
        $this->migrator->add('category-ad.image_width', '300');
    }

    public function down(): void
    {
        $this->migrator->delete('category-ad.image_width');
        $this->migrator->delete('category-ad.image_height');
    }
};
