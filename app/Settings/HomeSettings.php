<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public array $section_data;
    public int $header_top_spacing;
    public int $header_between_line_spacing;
    public int $header_bottom_spacing;
    public int $search_box_size;
    public int $lg_search_box_size;
    public bool $show_all_category;
    public int $all_category_font_size;
    public bool $show_all_category_animation;
    public bool $enable_hover_animation;
    public array $displayed_popular_categories;
    public bool $ad_type_dropdown_enable;

    public static function group(): string
    {
        return 'home';
    }
}
