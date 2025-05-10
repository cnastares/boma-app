<?php

namespace App\Traits;

use App\Models\Wallets\Commission;
use App\Models\Wallets\Wallet;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation\Order;


trait HasCommission
{
    public function payable()
    {
        return $this->morphMany(Commission::class, 'payable');
    }

    public function processingOrderCommission($amount, $orderNumber, $userId)
    {
         try {
            if (isWalletSystemPluginEnabled()) {
                if (isCommissionEnabled()) {
                    // Calculate commission and vendor wallet amount
                    $commissionAmount = calculateCommissionAmount($amount);
                    $amountForVendorWallet = $amount - $commissionAmount;

                    // Create a new commission record for the order
                    $this->payable()->create([
                        'user_id' => $userId,
                        'amount' => $amount,
                        'commission_rate' => getCommissionValue(),  // Assuming this returns a percentage
                        'commission_type' => getCommissionType(),  // Assuming this returns 'fixed' or 'percentage'
                        'commission_amount' => $commissionAmount,
                        'status' => 'received'
                    ]);
                } else {
                    $amountForVendorWallet = $amount;
                }

                // Fetch or create the user's wallet
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $userId],  // Query condition
                    ['balance' => 0]              // Default balance for new wallet
                );

                // Increment wallet balance
                 $wallet->increment('balance', $amountForVendorWallet);

                // Create a transaction record for the wallet
                $wallet->transactions()->create([
                    'user_id' => $userId,
                    'amount' => $amountForVendorWallet,
                    'transaction_reference' => $orderNumber,
                    'status' => 'completed',
                    'payable_type' => $this::class,  // Polymorphic model type
                    'payable_id' => $this->id,    // Polymorphic model id
                ]);

                if (!empty($orderNumber)) {
                    // Find the order by order number
                    $order = Order::where('order_number', $orderNumber)->first();
                    if ($order) {
                        // Update the order status
                        $order->update([
                            'order_status' => 'completed', // Change 'completed' to the desired status
                        ]);
                    }
                }
            }
        } catch (Exception $exception) {
            // Log the error with exception details
            Log::error('Error processing order commission', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => $order->user_id ?? null,
            ]);
        }
    }
}
