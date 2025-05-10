<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $steps = [
            ['step_name' => 'Title & Information'],
            ['step_name' => 'Images'],
            ['step_name' => 'Location'],
        ];

        $this->migrator->add('ad.publish_order',$steps);
    }
    public function down(): void
    {
        $this->migrator->delete('ad.publish_order');
    }
};
