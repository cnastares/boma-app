<?php

namespace App\Console\Commands;

use App\Models\Reservation\Order;
use Illuminate\Console\Command;
use App\Models\Setting;
use App\Settings\PointVaultSettings;
use Carbon\Carbon;

class AutoTransferOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically transfers on-hold points to the seller if the buyer does not confirm receipt after shipping.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Get auto transfer days
        $daysThreshold = app(PointVaultSettings::class)->auto_transfer_days; // Get from admin settings

        $orders = Order::where('payment_status', 'completed')
                ->whereHas('histories', function ($query) use ($daysThreshold) {
                    $query->where('action', 'order_shipped')
                        ->whereNotNull('action_date')
                        ->where('action_date', '<=', Carbon::now()->subDays($daysThreshold));
                })
                ->whereHas('histories', function ($query) {
                    $query->where('action', 'order_received')
                        ->whereNull('action_date');
                })
                ->get();
                
        if ($orders->isEmpty()) {
            $this->info('No orders eligible for auto-transfer.');
            return;
        }

        foreach ($orders as $order) {

            $sellerWallet = $order?->vendor?->wallet;
            $buyerWallet = $order?->user?->wallet;

            // Transfer points to seller
            if($sellerWallet){
                $sellerWallet->increment('balance', $order->points);
            }

            // Deduct points from buyer seller
            if($buyerWallet){
                $buyerWallet->update([
                    'points_on_hold' => max(0, $buyerWallet->points_on_hold - $order->points)
                ]);
            }

            //Set order to received
            $order->histories()->where('action', 'order_received')->first()?->update([
                'action_date' => now()
            ]);

            // Create a transaction record for the wallet
            $sellerWallet->transactions()->create([
                'user_id' => $order->vendor_id,
                'points' => $order->points,
                'transaction_reference' => $order->order_number,
                'transaction_type' => 'Order received',
                'is_added' => true,
                'status' => 'completed',
                'payable_type' => Order::class,  // Polymorphic model type
                'payable_id' => $order->id,        // Polymorphic model id
            ]);

            $this->info("Auto-transferred {$order->points} points for Order #{$order->id}");
        }

        $this->info('Auto transfer process completed.');
    }
}
