
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.show_all_category',true);
        $this->migrator->add('home.all_category_font_size','14');
        $this->migrator->add('home.show_all_category_animation',true);
    }

    public function down(): void
    {
        $this->migrator->delete('home.show_all_category');
        $this->migrator->delete('home.all_category_font_size');
        $this->migrator->delete('home.show_all_category_animation');
    }
};
