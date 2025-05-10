<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserSpendTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $userSpendTimeData;
    /**
     * Create a new job instance.
     */
    public function __construct($userSpendTimeData,$record)
    {
        $this->userSpendTimeData=$userSpendTimeData;
        $this->record=$record;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->record->pageVisits()->create($this->userSpendTimeData);
    }
}
