<?php

namespace App\Services\Wallet;

use App\Models\Reservation\Order;
use App\Models\Wallets\Wallet;

class TransactionServices
{


    /**
     * Creates a transaction record in the user's wallet.
     *
     * @param int $userId User's id
     * @param int $points Number of points to add/remove from wallet
     * @param string $orderNumber Order's number (used as transaction reference)
     * @param int $orderId Order's id (used for polymorphic relation)
     * @param string $transactionType Type of transaction (e.g. 'order', 'refund')
     * @param bool $isAdded Is transaction adding points to wallet?
     * @param bool $canDeductPointsOnHold Can deduct points from on-hold balance?
     * @return void
     */
    public static function createTransaction($userId, $points, $orderNumber, $orderId, $transactionType, $isAdded = true, $canDeductPointsOnHold = false)
    {
        // Fetch or create the user's wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],  // Query condition
            ['points' => 0] // Default balance for new wallet
        );

        // Increment wallet balance
        if ($isAdded) {
            $wallet->increment('points', $points);
        }
        //Deduct points on hold after order received
        if ($canDeductPointsOnHold) {
            $wallet->update([
                'points_on_hold' => max(0, $wallet->points_on_hold - $points)
            ]);
        }

        // Create a transaction record for the wallet
        $wallet->transactions()->create([
            'user_id' => $userId,
            'points' => $points,
            'transaction_reference' => $orderNumber,
            'transaction_type' => $transactionType,
            'is_added' => $isAdded,
            'status' => 'completed',
            'payable_type' => Order::class,  // Polymorphic model type
            'payable_id' => $orderId,        // Polymorphic model id
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

    /**
     * Refund the points if seller cancel(reject) order
     *
     * @param int $userId User's id
     * @param int $points Number of points to refund
     * @param string $orderNumber Order's number
     * @param int $orderId Order's id
     * @param string $transactionType Type of transaction (e.g. 'order', 'refund')
     * @param bool $isAdded Is transaction adding points to wallet?
     *
     * @return void
     */
    public static function refundTransaction($userId, $points, $orderNumber, $orderId, $transactionType, $isAdded = true): void
    {
        // Fetch or create the user's wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],  // Query condition
            ['points' => 0] // Default balance for new wallet
        );

        // Increment wallet balance
        if ($isAdded) {
            $wallet->update([
                'points_on_hold' => max(0, $wallet->points_on_hold - $points)
            ]);
            $wallet->increment('points', $points);
        }

        // Create a transaction record for the wallet
        $wallet->transactions()->create([
            'user_id' => $userId,
            'points' => $points,
            'transaction_reference' => $orderNumber,
            'transaction_type' => $transactionType,
            'is_added' => $isAdded,
            'status' => 'completed',
            'payable_type' => Order::class,  // Polymorphic model type
            'payable_id' => $orderId,        // Polymorphic model id
        ]);
    }

}
