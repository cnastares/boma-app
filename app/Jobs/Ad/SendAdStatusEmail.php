<?php

namespace App\Jobs\Ad;

use App\Notifications\Ad\StatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAdStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $ad;
    public $subject;
    public $line;
    /**
     * Create a new job instance.
     */
    public function __construct($user,$ad, $subject, $line)
    {
        $this->user=$user;
        $this->ad=$ad;
        $this->subject=$subject;
        $this->line=$line;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new StatusNotification($this->ad, $this->subject, $this->line));
    }
}
