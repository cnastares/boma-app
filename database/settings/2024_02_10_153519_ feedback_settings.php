<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('feedback.enable_feedback', true);
        $this->migrator->add('feedback.enable_replies', true);
        $this->migrator->add('feedback.enable_likes', true);
    }

    public function down(): void
    {
        $this->migrator->delete('feedback.enable_feedback');
        $this->migrator->delete('feedback.enable_replies');
        $this->migrator->delete('feedback.enable_likes');
    }
};
