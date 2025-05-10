<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PointVaultSettings extends Settings
{
    public bool $enable_point_system;
    public string $name;
    public string $short_name;
    public bool $enable_points_for_new_users;
    public int $default_points_on_signup;
    public float $per_point_value;
    public int $set_max_points_ad;
    public string $policy_page;
    public string $default_country;
    public string $buy_point_sidebar_text;
    public int $auto_transfer_days;

    public static function group(): string
    {
        return 'point_vault';
    }
}
