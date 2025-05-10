<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Artisan;
use File;

class CacheManager extends Component
{
    /**
     * Clear the application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            notifySuccess(__('messages.t_ap_system_cache_cleared'));
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Clear all compiled view files.
     */
    public function clearViews()
    {
        try {
            Artisan::call('view:clear');
            notifySuccess(__('messages.t_ap_compiled_views_cleared'));
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Clear all application log files.
     */
    public function clearLogs()
    {
        try {
            $files = File::allFiles(storage_path('logs'));
            foreach ($files as $file) {
                File::delete($file);
            }
            notifySuccess(__('messages.t_ap_log_files_cleared'));
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    public function updateLanguage(){
        try {
            Artisan::call('merge:translation-messages');
            notifySuccess(__('messages.t_ap_language_files_updated'));
        } catch (\Exception $e) {
            notifyError($e->getMessage());
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.system.cache-manager');
    }
}
