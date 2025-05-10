<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('watermark.enable_watermark', false);
        $this->migrator->add('watermark.position', 'bottom-right');
        $this->migrator->add('watermark.watermark_image', '');
    }

    public function down(): void
    {
        $this->migrator->delete('watermark.enable_watermark');
        $this->migrator->delete('watermark.position');
        $this->migrator->delete('watermark.watermark_image');
    }
};
