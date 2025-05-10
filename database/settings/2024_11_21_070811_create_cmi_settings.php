
<?php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('cmi.name', 'Cmi');
        $this->migrator->add('cmi.url', null);
        $this->migrator->add('cmi.status', false);
        $this->migrator->add('cmi.client_id', null);
        $this->migrator->add('cmi.store_key', null);
        $this->migrator->add('cmi.currency', 'USD');
        $this->migrator->add('cmi.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('cmi.name');
        $this->migrator->delete('cmi.url');
        $this->migrator->delete('cmi.status');
        $this->migrator->delete('cmi.client_id');
        $this->migrator->delete('cmi.store_key');
        $this->migrator->delete('cmi.currency');
        $this->migrator->delete('cmi.exchange_rate');

    }
};
