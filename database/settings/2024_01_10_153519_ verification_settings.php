<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('verification.document_types',[
            ['type' => 'id', 'selfie_required' => false, 'enable' => true],
            ['type' => 'driver_license', 'selfie_required' => false, 'enable' => true],
            ['type' => 'passport', 'selfie_required' => false, 'enable' => true],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('verification.document_types');
    }
};
