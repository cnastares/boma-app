<?php

namespace App\Observers\Reservation;

use App\Models\Reservation\OrderStatusHistory;
use App\Jobs\Order\OrderPlacedEmail;
use App\Models\Ad;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderStatusObserver
{
    /**
     * Handle the OrderStatusHistory "created" event.
     */
    public function created(OrderStatusHistory $history): void
    {
        try {
            $seller = $history->vendor;
            $user = $history->user;
            $order = $history->order;
            if ($history->action == 'order_requested') {
                // Prepare subjects for both buyer and seller notifications
                $buyerSubject = str_replace('[order_id]', $order->order_number, __('messages.t_order_confirmation_status'));
                $sellerSubject = str_replace('[order_id]', $order->order_number, __('messages.t_new_order_received'));

                // Dispatch emails for buyer and seller
                OrderPlacedEmail::dispatch($user, $order, $buyerSubject);
                OrderPlacedEmail::dispatch($seller, $order, $sellerSubject);
            }
        } catch (Exception $exception) {
            // Log detailed error information
            Log::error('Error processing order history action', [
                'message' => $exception->getMessage(),
                'history_id' => $history->id ?? null,
                'order_id' => $order->id ?? null,
                'user_id' => $user->id ?? null,
                'seller_id' => $seller->id ?? null,
            ]);
        }
    }

    /**
     * Handle the OrderStatusHistory "updated" event.
     */
    public function updated(OrderStatusHistory $history): void
    {
        try {
            $seller = $history->vendor;
            $user = $history->user;
            $order = $history->order;

            // Prepare the email subject for the buyer
            $buyerSubject = str_replace(
                ['[order_id]', '[status]'],
                [$order->order_number, Str::title(str_replace('_', ' ', $history->action))],
                __('messages.t_order_status')
            );
            // Dispatch the email to the buyer
            OrderPlacedEmail::dispatch($user, $order, $buyerSubject);

            if ($history->action == "order_cancelled") {

                $sellerSubject = str_replace(
                    ['[order_id]', '[status]'],
                    [$order->order_number, Str::title(str_replace('_', ' ', $history->action))],
                    __('messages.t_order_status')
                );

                OrderPlacedEmail::dispatch($seller, $order, $sellerSubject);
            }
        } catch (Exception $exception) {
            // Log the error with more details
            Log::error('Error processing order history action: ' . $exception->getMessage(), [
                'history_id' => $history->id ?? null,
                'order_id' => $order->id ?? null,
                'user_id' => $user->id ?? null,
            ]);
        }


        //Buyer Notification


        //Seller Notification
        // $sellerSubject = str_replace('[order_id]', $order->order_number, __('messages.t_order_status'));
        // OrderPlacedEmail::dispatch($seller, $order, $sellerSubject);
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
