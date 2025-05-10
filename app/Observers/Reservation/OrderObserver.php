<?php

namespace App\Observers\Reservation;

use App\Models\Reservation\Order;
use App\Models\Wallets\Wallet;;
use App\Models\Ad;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void {}


    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // try {
        //     // Early return if commission is not enabled
        //     if (!isCommissionEnabled()) {
        //         return;
        //     }

        //     if (!$order->subtotal_amount) {
        //         return;
        //     }

        //     // Calculate commission and vendor wallet amount
        //     $commissionAmount = calculateCommissionAmount($order->subtotal_amount);
        //     $amountForVendorWallet = $order->subtotal_amount - $commissionAmount;

        //     // Create a new commission record for the order
        //     $order->payable()->create([
        //         'user_id' => $order->user_id,
        //         'amount' => $order->subtotal_amount,
        //         'commission_rate' => getCommissionValue(),  // Assuming this returns a percentage
        //         'commission_type' => getCommissionType(),  // Assuming this returns 'fixed' or 'percentage'
        //         'commission_amount' => $commissionAmount,
        //     ]);

        //     // Fetch or create the user's wallet
        //     $wallet = Wallet::firstOrCreate(
        //         ['user_id' => auth()->id()],  // Query condition
        //         ['balance' => 0]              // Default balance for new wallet
        //     );

        //     // Increment wallet balance
        //     $wallet->increment('balance', $amountForVendorWallet);

        //     // Create a transaction record for the wallet
        //     $wallet->transactions()->create([
        //         'user_id' => auth()->id(),
        //         'amount' => $amountForVendorWallet,
        //         'status' => 'completed',
        //     ]);
        // } catch (Exception $exception) {
        //     // Log the error with exception details
        //     Log::error('Error processing order commission', [
        //         'message' => $exception->getMessage(),
        //         'order_id' => $order->id ?? null,
        //         'user_id' => $order->user_id ?? null,
        //     ]);
        // }
    }
    /**
     * Handle the Ad "deleted" event.
     */
    public function deleted(Ad $ad): void
    {
        //
    }

    /**
     * Handle the Ad "restored" event.
     */
    public function restored(Ad $ad): void
    {
        //
    }

    /**
     * Handle the Ad "force deleted" event.
     */
    public function forceDeleted(Ad $ad): void
    {
        //
    }

    public function deleting($record)
    {
        foreach ($record->media as $media) {
            $media->modifications()->delete();
        }

        $record->modifications()->delete();
        $record->media()->delete();
    }
}
