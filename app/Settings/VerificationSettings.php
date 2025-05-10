<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class VerificationSettings extends Settings
{
    public array $document_types;
    public bool $hide_attachment;
    public static function group(): string
    {
        return 'verification';
    }
}
