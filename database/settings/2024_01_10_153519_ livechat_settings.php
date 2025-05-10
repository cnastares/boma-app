<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('livechat.enable_livechat', false);
        $this->migrator->add('livechat.pusher_key', '');
        $this->migrator->add('livechat.pusher_secret', '');
        $this->migrator->add('livechat.pusher_app_id', '');
        $this->migrator->add('livechat.pusher_cluster', '');
        $this->migrator->add('livechat.encryption', true);
        $this->migrator->add('livechat.enable_uploading_attachments', false);
        $this->migrator->add('livechat.enable_emojis', false);
        $this->migrator->add('livechat.play_new_message_sound', false);
        $this->migrator->add('livechat.allowed_image_extensions', 'jpg,jpeg,png');
        $this->migrator->add('livechat.allowed_file_extensions', 'zip,pdf,txt,psd');
        $this->migrator->add('livechat.max_file_size', 1024);
    }

    public function down(): void
    {
        $this->migrator->delete('livechat.pusher_key');
    }
};
