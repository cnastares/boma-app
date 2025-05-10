<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAdPosting;
use App\Settings\PackageSettings;
use Carbon\Carbon;

class ResetAdCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adcounts:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset ad counts for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;
        $now = Carbon::now();

        switch ($renewalPeriod) {
            case 'month':
                $resetDate = $now->subMonth();
                break;
            case 'year':
                $resetDate = $now->subYear();
                break;
            default:
                $resetDate = $now->subMonth();
        }

        UserAdPosting::where('period_start', '<', $resetDate)
                    ->update(['ad_count' => 0, 'free_ad_count' => 0, 'period_start' => $now]);

    }
}
