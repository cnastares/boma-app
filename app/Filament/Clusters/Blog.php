<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;


class Blog extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_content_design');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_blog_title');
    }
}
