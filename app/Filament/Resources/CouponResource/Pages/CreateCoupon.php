<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use App\Settings\StripeSettings;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;
    public function getStripePaymentSettingsProperty()
    {
        return app(StripeSettings::class);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $response = $this->createCoupon($data);
        if ($response->isOk()) {
            $productData = $response->getData(true);
            $data = array_merge($data, $productData);

            $plan = static::getModel()::create($data);


            return $plan;
        } else {
            $this->halt();
        }
    }
    public function createCoupon($coupon)
    {
        try {
            $stripe = $this->initializeStripeClient();
            $amount_off=null;
            $percent_off=null;
            $couponData=[
                'duration' => 'once',
                'currency' => $this->stripePaymentSettings?->currency,
            ];
            // Create a new product
            if($coupon['type']=='fixed'){
                $couponData['amount_off']=$coupon['discount_value'];
            }else{
                $couponData['percent_off']=$coupon['discount_value'];
            }
            $coupon=$stripe->coupons->create($couponData);

            return response()->json([
                'stripe_coupon_id' => $coupon->id,
            ]);

        } catch (\Stripe\Exception\CardException $e) {
            Notification::make()
                ->title($e->getError()->message)
                ->send();
            return response()->json(['error' => $e->getError()->message], 500);
        } catch (\Exception $e) {
            // Log the unexpected error
            Log::error(`Unexpected error: {$e->getMessage()}`);
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->title('Failed to Create Plan')
                ->send();
            // Handle the error gracefully
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    public function initializeStripeClient()
    {
        // Set Stripe API key
        return new StripeClient($this->stripePaymentSettings?->secret_key);
    }
}
