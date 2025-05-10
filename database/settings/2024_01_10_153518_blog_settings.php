<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('blog.enable_comments', true);
        $this->migrator->add('blog.enable_blog', true);
    }

    public function down(): void
    {
        $this->migrator->delete('blog.enable_comments');
        $this->migrator->delete('blog.enable_blog');
    }
};
