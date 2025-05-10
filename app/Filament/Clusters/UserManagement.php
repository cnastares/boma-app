<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UserManagement extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_user_access');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_user_management');
    }
}
