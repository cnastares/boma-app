<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class ResetSpecificTables extends Command
{
    protected $signature = 'migrate:reset-specific';
    protected $description = 'Reset specific tables';

    public function handle()
    {
        // Drop specific tables
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');

        // Run specific migrations for conversations and messages
        $this->call('migrate:refresh', [
            '--path' => 'database/migrations/2023_08_04_024122_create_conversations_table.php'
        ]);
        $this->call('migrate:refresh', [
            '--path' => 'database/migrations/2023_08_04_024122_create_messages_table.php'
        ]);
        $this->call('migrate:refresh', [
            '--path' => 'database/migrations/2024_01_29_051056_add_new_fields_to_messages_table.php'
        ]);

        $this->info('Conversations and Messages tables have been reset and re-migrated.');
    }
}
