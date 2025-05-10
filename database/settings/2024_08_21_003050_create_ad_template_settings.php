
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ad-template.theme', 'classic_frame');
        $this->migrator->add('ad-template.max_line', '1');
    }

    public function down(): void
    {
        $this->migrator->delete('ad-template.theme');
        $this->migrator->delete('ad-template.max_line');
    }
};
