<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all log files from storage/logs directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs');

        if (!File::exists($logPath)) {
            $this->error('Logs directory does not exist!');
            return 1;
        }

        $logFiles = File::glob($logPath . '/*.log');

        if (empty($logFiles)) {
            $this->info('No log files found to clear.');
            return 0;
        }

        $deletedCount = 0;
        foreach ($logFiles as $file) {
            if (File::delete($file)) {
                $deletedCount++;
                $this->line('Deleted: ' . basename($file));
            }
        }

        $this->info("Successfully cleared {$deletedCount} log file(s).");
        return 0;
    }
}
