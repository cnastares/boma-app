<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationRegistrationSettings extends Settings
{
    public ?string $notification_email;
    public bool $enable;
    public ?string $instagram_username;
    public bool $auto_focus_enabled;
    public ?string $banner_image;
    //Todo:enable if customize only the logo in the page
    // public ?string $logo_width_mobile;
    // public ?string $logo_height_mobile;
    // public ?string $logo_width_desktop;
    // public ?string $logo_height_desktop;

    public static function group(): string
    {
        return 'notification-registration';
    }
}
