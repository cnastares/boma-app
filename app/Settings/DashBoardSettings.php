<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DashBoardSettings extends Settings
{
    public bool $enable_move_chart_to_bottom;

    public static function group(): string
    {
        return 'dashboard-settings';
    }
}
