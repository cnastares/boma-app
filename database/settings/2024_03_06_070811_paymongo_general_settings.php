
<?php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('paymongo.name', 'PayMongo');
        $this->migrator->add('paymongo.status', false);
        $this->migrator->add('paymongo.logo', null);
        $this->migrator->add('paymongo.public_key', null);
        $this->migrator->add('paymongo.secret_key', null);
        $this->migrator->add('paymongo.authorization_token', null);
        $this->migrator->add('paymongo.currency', 'USD');
        $this->migrator->add('paymongo.exchange_rate', 1);

    }

    public function down(): void
    {
        $this->migrator->delete('paymongo.name');
        $this->migrator->delete('paymongo.logo');
        $this->migrator->delete('paymongo.status');
        $this->migrator->delete('paymongo.public_key');
        $this->migrator->delete('paymongo.secret_key');
        $this->migrator->delete('paymongo.currency');
        $this->migrator->delete('paymongo.exchange_rate');

    }
};
