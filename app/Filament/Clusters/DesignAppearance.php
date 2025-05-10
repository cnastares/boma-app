<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;


// Design & Appearance Cluster
class DesignAppearance extends Cluster
{
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Design & Appearance';

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_content_design');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_design_appearance_title');
    }
}
