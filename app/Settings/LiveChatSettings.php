<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LiveChatSettings extends Settings
{
    public bool $enable_livechat;
    public ?string $pusher_key;
    public ?string $pusher_secret;
    public ?string $pusher_app_id;
    public ?string $pusher_cluster;
    public bool $encryption;
    public bool $enable_uploading_attachments;
    public bool $enable_emojis;
    public bool $play_new_message_sound;
    public string $allowed_image_extensions;
    public string $allowed_file_extensions;
    public int $max_file_size;
    public bool $enable_audio_recording;

    public static function group(): string
    {
        return 'livechat';
    }
}
