<?php

namespace App\Console;

use App\Models\Ad;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('sitemap:generate')->daily();

        $schedule->call(function () {
            // Set status to 'expired' for ads that have passed their expiry date.
            Ad::where('expires_at', '<', now())
                ->where('status', '!=', 'expired')
                ->update(['status' => 'expired']);
        })->daily();

        $isDemo = Config::get('app.demo');

        if ($isDemo) {
            $schedule->command('migrate:fresh')
                ->everyTwoHours()
                ->then(function () {
                    Artisan::call('db:seed');
                    Artisan::call('db:seed', ['--class' => 'DemoDataSeeder']);
                    Artisan::call('db:seed', ['--class' => 'AdImagesSeeder']);
                    Artisan::call('db:seed', ['--class' => 'OtherDataSeeder']);
                })
                ->withoutOverlapping();
        }
        if(app('filament')->hasPlugin('packages')) {
            $schedule->command('adcounts:reset')->daily();
        }
        if(app('filament')->hasPlugin('subscription') && isSubscriptionEnabled()) {
            $schedule->command('subscription:notify-upcoming-renewals')->daily();
            $schedule->command('subscription:disabled-expired-ads')->daily();
            $schedule->command('subscription:disabled-expired-promotions')->daily();
            $schedule->command('subscription:delete-expired-free-ads')->everyMinute();
        }

        //Run backup
        $backupTime=config('backup.backup.backup_time');
        $backupFrequency=config('backup.backup.backup_frequency');
        switch($backupFrequency){
            case 'daily':
                $schedule->command('backup:run')->dailyAt($backupTime);
                break;
            case 'weekly':
                $schedule->command('backup:run')->weeklyOn(1,$backupTime);
                break;
            case 'monthly':
                $schedule->command('backup:run')->monthlyOn(1,$backupTime);;
            break;
        }
        $schedule->command('backup:clean')->daily();
        $schedule->command('ads:check-abandoned-drafts')->hourly();
        $schedule->call(function () {
            Ad::active()->get()->each->monitorTrafficAndConversionTrends();
        })->hourly();

        if(isPointSystemPluginEnabled()){
            $schedule->command('orders:auto-transfer')->daily();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
