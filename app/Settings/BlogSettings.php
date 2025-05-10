<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BlogSettings extends Settings
{

    public bool $enable_comments;
    public bool $enable_blog;

    public static function group(): string
    {
        return 'blog';
    }
}
