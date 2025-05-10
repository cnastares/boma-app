<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social.facebook_link', 'https://www.facebook.com/Adfox');
        $this->migrator->add('social.twitter_link', 'https://www.twitter.com/Adfox');
        $this->migrator->add('social.instagram_link', 'https://www.instagram.com/Adfox'); 
        $this->migrator->add('social.linkedin_link', 'https://www.linkedin.com/Adfox');
    }

    public function down(): void
    {
        $this->migrator->delete('social.facebook_link');
        $this->migrator->delete('social.twitter_link');
        $this->migrator->delete('social.instagram_link');
        $this->migrator->delete('social.linkedin_link');
    }
};
