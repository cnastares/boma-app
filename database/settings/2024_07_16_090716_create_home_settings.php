<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $sectionData = [
            [
                'title'=>'Explore by categories',
                'type'=>'categories',
            ],
            [
                'title'=>'Spotlight Display',
                'type'=>'spotlight',
            ],
            [
                'title'=>'Fresh Recommendations',
                'type'=>'fresh_ads',
            ]

        ];

        $this->migrator->add('home.section_data',$sectionData);
        $this->migrator->add('home.header_top_spacing',24);
        $this->migrator->add('home.header_between_line_spacing',24);
        $this->migrator->add('home.header_bottom_spacing',24);
        $this->migrator->add('home.search_box_size',260);
    }
    public function down(): void
    {
        $this->migrator->delete('home.section_data');
        $this->migrator->delete('home.header_top_spacing');
        $this->migrator->delete('home.header_between_line_spacing');
        $this->migrator->delete('home.header_bottom_spacing');
        $this->migrator->delete('home.search_box_size');
    }
};
