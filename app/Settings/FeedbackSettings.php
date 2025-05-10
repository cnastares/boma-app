<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FeedbackSettings extends Settings
{
    public bool $enable_feedback;
    public bool $enable_replies;
    public bool $enable_likes;

    public static function group(): string
    {
        return 'feedback';
    }
}
