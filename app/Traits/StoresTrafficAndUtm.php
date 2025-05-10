<?php
namespace App\Traits;

use App\Models\UserTrafficSource;
use App\Services\IpService;
use Jenssegers\Agent\Agent;

trait StoresTrafficAndUtm
{
    public function storeUtmData($trackableRecord, $request)
    {

        $utmSource = session('utm_source');
        $utmMedium = session('utm_medium');
        $utmCampaign = session('utm_campaign');
        $trafficSource = session('traffic_source');
        $ipService = new IpService();
        // Capture referrer and other user details
        $referrerUrl = $request->headers->get('referer');
        $userIp = getClientIp();
        $userAgent = $request->header('User-Agent');
        $locationDetails = $ipService->getLocationFromIP($userIp);

        $agent = new Agent();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $deviceName = $agent->device();
        $deviceType=null;
        if($agent->isDesktop()){
            $deviceType='desktop';
        }elseif($agent->isMobile() || $agent->isTablet()){
            $deviceType='mobile';
        }
        // Store in database
        if ($trackableRecord) {
            $data = [
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'traffic_source' => $trafficSource,
                'referrer_url' => $referrerUrl, // Include referrer URL
                'full_url' => $request->fullUrl(), // Include referrer URL
                'visitor_ip' => $userIp,           // Include user IP
                'user_agent' => $userAgent,     // Include user agent
                'location_data' => $locationDetails ?? null,
                'browser'=>$browser ?? null,
                'os'=>$platform ?? null,
                'device_name'=>$deviceName ?? null,
                'device_type'=>$deviceType ?? null,

            ];
            if (auth()->check()) {
                $data['user_id'] = auth()->id();
            }
            $trackableRecord->userTrafficSources()->create($data);
        }
    }
}
