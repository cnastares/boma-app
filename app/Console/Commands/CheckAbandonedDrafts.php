<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Ad;
use App\Mail\AbandonedDraftMail;
use App\Notifications\Ad\AbandonedDraftNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckAbandonedDrafts extends Command
{
    protected $signature = 'ads:check-abandoned-drafts';
    protected $description = 'Check for ads that have been abandoned';

    public function handle()
    {
        // Define the abandonment time threshold (e.g., 24 hours)
        $threshold = Carbon::now()->subHours(24);

        // Find drafts that have not been updated for 24 hours
        $abandonedDrafts = Ad::where('status', 'draft')
            ->where('updated_at', '<', $threshold)
            ->get();

        foreach ($abandonedDrafts as $draft) {
            // Send reminder email
            if (getSubscriptionSetting('status') && getUserSubscriptionPlan($draft->user_id)?->automated_email_marketing) {
                $draft->user->notify(new AbandonedDraftNotification($draft));
            }
        }

        $this->info('Abandoned drafts check complete.');
    }
}
