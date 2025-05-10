<?php

namespace App\Jobs;

use App\Notifications\WalletTransactionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WalletTransactionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $walletTransaction;
    public $subject;
    public $line;
    /**
     * Create a new job instance.
     */
    public function __construct($user, $walletTransaction, $subject)
    {
        $this->user = $user;
        $this->walletTransaction = $walletTransaction;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->user->notify(new WalletTransactionNotification($this->walletTransaction, $this->subject));
        } catch (\Exception $e) {
            // Log the error instead of failing the job
            \Log::error('Wallet Transaction Email Failed', [
                'user_id' => $this->user->id,
                'wallet_transaction_id' => $this->walletTransaction->id,
                'error' => $e->getMessage()
            ]);

            // Optionally, you can requeue the job for temporary failures
            if ($this->attempts() < 3) {
                $this->release(60); // Retry after 60 seconds
            }
        }
    }
}
