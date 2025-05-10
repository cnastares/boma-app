<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('livechat.enable_audio_recording', false);
    }
    public function down(): void
    {
        $this->migrator->delete('livechat.enable_audio_recording');
    }
};
