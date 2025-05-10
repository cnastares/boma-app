<?php

namespace App\Observers;

use App\Jobs\Ad\SendAdStatusEmail;
use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use App\Notifications\Ad\StatusNotification;

class AdObserver
{
    /**
     * Handle the Ad "created" event.
     */
    public function created(Ad $ad): void
    {
        $user = $ad->user;
        $admin = User::where('is_admin', true)->first();
        if ($ad && $ad->slug) {
            try {
                if ($ad->status && $ad->status->value == 'pending') {
                    //Seller Notification
                    $sellerSubject = __('messages.t_et_pending_ad_seller_subject');
                    $sellerLine = __('messages.t_et_pending_ad_seller_line');
                    SendAdStatusEmail::dispatch($user, $ad, $sellerSubject, $sellerLine);

                    //Admin Notification
                    $adminSubject = __('messages.t_et_pending_ad_admin_subject');
                    $adminLine = __('messages.t_et_pending_ad_admin_line');
                    SendAdStatusEmail::dispatch($admin, $ad, $adminSubject, $adminLine);
                }

                if ($ad->status && $ad->status->value == 'active') {
                    //Seller Notification
                    $sellerSubject = __('messages.t_et_active_ad_seller_subject');
                    $sellerLine = __('messages.t_et_active_ad_seller_line');
                    SendAdStatusEmail::dispatch($user, $ad, $sellerSubject, $sellerLine);
                    //Admin Notification
                    $adminSubject = __('messages.t_et_active_ad_admin_subject');
                    $adminLine = __('messages.t_et_active_ad_admin_line');
                    SendAdStatusEmail::dispatch($admin, $ad, $adminSubject, $adminLine);
                }
            } catch (\Exception $e) {

            }
        }

    }

    /**
     * Handle the Ad "updated" event.
     */
    public function updated(Ad $ad): void
    {

        if ($ad->isDirty('status')) {
            $user = $ad->user;
            $admin = User::where('is_admin', true)->first();
            if ($ad && $ad->slug && $ad->status) {

                switch ($ad->status->value) {
                    case 'pending':
                        //Seller Notification Message
                        $sellerSubject = __('messages.t_et_pending_ad_seller_subject');
                        $sellerLine = __('messages.t_et_pending_ad_seller_line');

                        //Admin Notification Message
                        $adminSubject = __('messages.t_et_pending_ad_admin_subject');
                        $adminLine = __('messages.t_et_pending_ad_admin_line');
                        break;

                    case 'active':
                        //Seller Notification Message
                        $sellerSubject = __('messages.t_et_active_ad_seller_subject');
                        $sellerLine = __('messages.t_et_active_ad_seller_line');

                        //Admin Notification Message
                        $adminSubject = __('messages.t_et_active_ad_admin_subject');
                        $adminLine = __('messages.t_et_active_ad_admin_line');
                        break;

                    case 'inactive':
                        //Seller Notification Message
                        $sellerSubject = __('messages.t_et_inactive_ad_seller_subject');
                        $sellerLine = __('messages.t_et_inactive_ad_seller_line');

                        //Admin Notification Message
                        $adminSubject = __('messages.t_et_inactive_ad_admin_subject');
                        $adminLine = __('messages.t_et_inactive_ad_admin_line');
                        break;
                }
                try {
                    if (in_array($ad->status->value, ['pending', 'active', 'inactive'])) {
                        //Seller Notification
                        SendAdStatusEmail::dispatch($user, $ad, $sellerSubject, $sellerLine);
                        //Admin Notification
                        SendAdStatusEmail::dispatch($admin, $ad, $adminSubject, $adminLine);
                    }
                } catch (\Exception $e) {

                }
            }
        }

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

    // public function updating(Ad $ad)
    // {
    //     // Get changed attributes
    //     $dirtyAttributes = $ad->getDirty();

    //     // Log changed attributes (optional for debugging)
    //     \Log::info('Updated fields:', $dirtyAttributes);

    //     // Define all category-related fields and map them to their corresponding models
    //     $categoryFieldMap = [
    //         'main_category_id',
    //         'category_id',
    //         'child_category_id',
    //     ];
    //     // Determine which category field was updated
    //     foreach ($categoryFieldMap as $field ) {
    //         if (array_key_exists($field, $dirtyAttributes)) {
    //             // Fetch the new category based on the updated field
    //             $updatedCategory = Category::find($ad->$field);

    //             // If manual approval is enabled for the updated category, set status to pending
    //             if ($updatedCategory && $updatedCategory->enable_manual_approval && $ad->status->value == 'active') {
    //                 $ad->status = 'pending';
    //                 break; // Stop checking further if one condition is met
    //             }
    //         }
    //     }
    // }


}
