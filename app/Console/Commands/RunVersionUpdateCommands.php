<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunVersionUpdateCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:update {targetVersion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs specific Artisan commands based on the target version for application updates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Get the version
        $version = $this->argument('targetVersion');
        //Get the version commands from config file
        $versionCommands = config('version-commands');

        $commands = $versionCommands[$version] ?? [];
        //If version doesn't exist
        if (!isset($versionCommands[$version])) {
            $this->warn("The given version-{$version} is invalid. Available versions: " . implode(', ', array_keys($versionCommands)));
            return;
        }
        //iterating through commands
        foreach ($commands as $command) {
            Artisan::call($command);
        }
        $this->info("Version-{$version} update commands run successfully");
    }
}
