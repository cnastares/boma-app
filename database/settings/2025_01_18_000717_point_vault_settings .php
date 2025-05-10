<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('point_vault.enable_point_system', false);
        $this->migrator->add('point_vault.name', 'KitapPuan');
        $this->migrator->add('point_vault.short_name', 'KP');
        $this->migrator->add('point_vault.enable_points_for_new_users', true);
        $this->migrator->add('point_vault.default_points_on_signup', 100);
        $this->migrator->add('point_vault.per_point_value', 2);
        $this->migrator->add('point_vault.set_max_points_ad', 100);
        $this->migrator->add('point_vault.policy_page', '');
        $this->migrator->add('point_vault.default_country', 'US');
    }

    public function down(): void
    {
        $this->migrator->delete('point_vault.enable_point_system');
        $this->migrator->delete('point_vault.name');
        $this->migrator->delete('point_vault.short_name');
        $this->migrator->delete('point_vault.enable_points_for_new_users');
        $this->migrator->delete('point_vault.default_points_on_signup');
        $this->migrator->delete('point_vault.per_point_value');
        $this->migrator->delete('point_vault.set_max_points_ad');
        $this->migrator->delete('point_vault.policy_page');
        $this->migrator->delete('point_vault.default_country');
    }
};
