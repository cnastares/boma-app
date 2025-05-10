
<?php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mollie.name', 'Mollie');
        $this->migrator->add('mollie.status', false);
        $this->migrator->add('mollie.api_key', null);
        $this->migrator->add('mollie.currency', 'USD');
        $this->migrator->add('mollie.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('mollie.name');
        $this->migrator->delete('mollie.status');
        $this->migrator->delete('mollie.api_key');
        $this->migrator->delete('mollie.currency');
        $this->migrator->delete('mollie.exchange_rate');

    }
};
