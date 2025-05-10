<?php


namespace App\Observers;


use App\Models\User;
use App\Models\Wallets\Wallet;
use Exception;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try{
            if (getPointSystemSetting('enable_points_for_new_users')) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0]              // Default balance for new wallet
                );
    
                // Increment wallet balance
                $wallet->increment('points',getPointSystemSetting('default_points_on_signup'));
    
                // Create a transaction record for the wallet
                $wallet->transactions()->create([
                    'user_id' =>  $user->id,
                    'points' => getPointSystemSetting('default_points_on_signup'),
                    'transaction_type' => 'Welcome KP Points',
                    'transaction_reference' => null,
                    'status' => 'completed',
                    'payable_type' => User::class,  // Polymorphic model type
                    'payable_id' => $user->id,    // Polymorphic model id
                ]);
            }
        } catch(Exception $ex) {
            
        }
    }


    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }


    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }


    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }


    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }


    public function deleting(User $user)
    {
        // Soft delete related ads when the user is being deleted
        $user->ads()->delete(); // This soft deletes the related ads
    }


    public function restoring(User $user)
    {
        // Optionally, you can restore related ads when the user is restored
        $user->ads()->restore();
    }
}
