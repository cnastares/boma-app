<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

trait ConfigBackupManager
{
    private $configBackupPath;

    /**
     * Backup configurations before making any changes
     */
    private function backupConfigs()
    {
        try {
            $timestamp = time();
            $this->configBackupPath = storage_path("app/config_backup_{$timestamp}");

            if (!File::exists($this->configBackupPath)) {
                File::makeDirectory($this->configBackupPath, 0755, true);
            }

            // Backup essential config files
            $configFiles = [
                'app.php',
                'maintenance.php',
                'chatify.php',
                'envato.php'
            ];

            foreach ($configFiles as $file) {
                if (File::exists(config_path($file))) {
                    File::copy(
                        config_path($file),
                        $this->configBackupPath . '/' . $file
                    );
                }
            }

            $this->logMessage('Config Backup', 'Configuration files backed up successfully');
            return true;

        } catch (\Exception $e) {
            $this->logMessage('Config Backup Failed', $e->getMessage());
            return false;
        }
    }

    /**
     * Restore configurations if update fails
     */
    private function restoreConfigs()
    {
        if (!$this->configBackupPath || !File::exists($this->configBackupPath)) {
            return;
        }

        try {
            // Restore config files
            $configFiles = File::files($this->configBackupPath);
            foreach ($configFiles as $file) {
                File::copy(
                    $file->getPathname(),
                    config_path($file->getFilename())
                );
            }

            // Clear config cache
            Artisan::call('config:clear');

            $this->logMessage('Config Restore', 'Configurations restored successfully');

            // Clean up backup
            File::deleteDirectory($this->configBackupPath);

        } catch (\Exception $e) {
            $this->logMessage('Config Restore Failed', $e->getMessage());
        }
    }
}
