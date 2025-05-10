<?php

namespace App\Observers;

use App\Jobs\WalletTransactionEmail;
use App\Models\Ad;
use App\Models\Wallets\WalletTransaction;

class WalletTransactionObserver
{
    /**
     * Handle the OrderStatusHistory "created" event.
     */
    public function created(WalletTransaction $walletTransaction): void
    {
        $user = $walletTransaction->user;

        //Buyer Notification
        $buyerSubject = __('messages.t_wallet_balance_update');
        WalletTransactionEmail::dispatch($user, $walletTransaction, $buyerSubject);
    }

    /**
     * Handle the OrderStatusHistory "updated" event.
     */
    // public function updated(WalletTransaction $walletTransaction): void
    // {

    //     $seller = $history->vendor;
    //     $user = $history->user;
    //     $order = $history->order;

    //     if ($history->action != "order_cancelled") {
    //         $buyerSubject = str_replace('[order_id]', $order->order_number, __('messages.t_order_status'));
    //         $buyerSubject = str_replace('[status]', Str::title(str_replace('_', ' ', $order->action)), $buyerSubject);

    //         OrderPlacedEmail::dispatch($user, $order, $buyerSubject);
    //     }

    //     //Buyer Notification


    //     //Seller Notification
    //     // $sellerSubject = str_replace('[order_id]', $order->order_number, __('messages.t_order_status'));
    //     // OrderPlacedEmail::dispatch($seller, $order, $sellerSubject);
    // }
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
