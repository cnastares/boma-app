<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.setup_finished', false);
        $this->migrator->add('general.purchase_code', '');
        $this->migrator->add('general.site_name', app_name());
        $this->migrator->add('general.separator', '|');
        $this->migrator->add('general.site_tagline', 'Your Classifieds Destination');
        $this->migrator->add('general.site_description', 'AdFox is your go-to marketplace for buying and selling goods locally and nationally. Simple, fast, and efficient. Discover great deals today!');
        $this->migrator->add('general.logo_path', '');
        $this->migrator->add('general.favicon_path', '');
        $this->migrator->add('general.default_language', 'en');
        $this->migrator->add('general.contact_email', 'saasforest@gmail.com');
        $this->migrator->add('general.contact_phone', '');
        $this->migrator->add('general.contact_address', '');
        $this->migrator->add('general.cookie_consent_enabled', false);
        $this->migrator->add('general.cookie_consent_message', 'This website uses cookies to ensure you get the best experience on our website.');
        $this->migrator->add('general.cookie_consent_agree', 'I Agree');
    }

    public function down(): void
    {
        $this->migrator->delete('general.setup_finished');
        $this->migrator->delete('general.purchase_code');
        $this->migrator->delete('general.site_name');
        $this->migrator->delete('general.separator');
        $this->migrator->delete('general.site_tagline');
        $this->migrator->delete('general.site_description');
        $this->migrator->delete('general.logo_path');
        $this->migrator->delete('general.favicon_path');
        $this->migrator->delete('general.default_language');
        $this->migrator->delete('general.contact_email');
        $this->migrator->delete('general.contact_phone');
        $this->migrator->delete('general.contact_address');
        $this->migrator->delete('general.cookie_consent_enabled');
        $this->migrator->delete('general.cookie_consent_message');
        $this->migrator->delete('general.cookie_consent_agree');
    }
};

