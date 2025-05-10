<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification-registration.banner_image', "");
        $this->migrator->add('notification-registration.auto_focus_enabled', false);
        //Todo:enable if customize only the logo in the page
        // $this->migrator->add('notification-registration.logo_height_mobile', '1');
        // $this->migrator->add('notification-registration.logo_width_mobile', '3');
        // $this->migrator->add('notification-registration.logo_width_desktop', '1.5');
        // $this->migrator->add('notification-registration.logo_height_desktop', '4.5');
    }
    public function down(): void
    {
        $this->migrator->delete('notification-registration.banner_image');
        $this->migrator->delete('notification-registration.auto_focus_enabled');
        //Todo:enable if customize only the logo in the page
        // $this->migrator->delete('notification-registration.logo_height_mobile');
        // $this->migrator->delete('notification-registration.logo_width_mobile');
        // $this->migrator->delete('notification-registration.logo_height_desktop');
        // $this->migrator->delete('notification-registration.logo_width_desktop');
    }
};
