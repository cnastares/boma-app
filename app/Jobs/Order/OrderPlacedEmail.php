<?php

namespace App\Jobs\Order;

use App\Notifications\Ad\StatusNotification;
use App\Notifications\Order\OrderPlacedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderPlacedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $ad;
    public $subject;
    public $line;
    /**
     * Create a new job instance.
     */
    public function __construct($user,$ad, $subject)
    {
        $this->user=$user;
        $this->ad=$ad;
        $this->subject=$subject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new OrderPlacedNotification($this->ad, $this->subject));
    }
}
