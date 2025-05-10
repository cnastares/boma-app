<?php

namespace App\Services;

use App\Settings\SubscriptionSettings;
use Illuminate\Support\Facades\Http;

class IpService{
    function getLocationFromIP($ipAddress = null)
    {
        // Use the client's IP if none is provided
        $ipAddress = $ipAddress ?? request()->ip();

        // Replace 'YOUR_API_KEY' with your actual ipinfo.io API key
        $apiKey = app(SubscriptionSettings::class)->ipinfo_key;
        $url = "https://ipinfo.io/{$ipAddress}?token={$apiKey}";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return $response->json();
            }else{
            }
        } catch (\Exception $e) {
            // Handle exception or log error
            return null;
        }

        return null;
    }
}
