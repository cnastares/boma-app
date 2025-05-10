<?php

namespace App\Http\Middleware;

use Closure;

class TrackTrafficSource
{
    public function handle($request, Closure $next)
    {
        // Capture UTM parameters from the URL if present
        $utmSource = $request->input('utm_source');
        $utmMedium = $request->input('utm_medium');
        $utmCampaign = $request->input('utm_campaign');

        // Set UTM source, medium, and campaign in session if available
        // if ($utmSource) {
            session([
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign
            ]);
        // }

        $this->setTrafficSource($utmMedium, $utmSource, $request);

        return $next($request);
    }
    /**
     * Check for UTM Medium and set traffic source accordingly
     * @param mixed $utmMedium
     * @return void
     */
    public function setTrafficSource($utmMedium, $utmSource, $request)
    {

        // Check if the user was referred by an internal page
        $refererUrl = $request->headers->get('referer');

        if ($refererUrl && strpos($refererUrl, $request->getHost()) !== false) {
            session(['traffic_source' => 'internal_redirect', 'internal_referrer' => $refererUrl]);
        } else {
            //
            if ($utmMedium === 'email') {
                session(['traffic_source' => 'email_campaign']);
            } elseif ($utmMedium === 'social') {
                session(['traffic_source' => 'social_media']);
            } elseif ($utmMedium === 'organic') {
                session(['traffic_source' => 'organic_search']);
            } elseif ($utmMedium === 'cpc' || $utmMedium === 'ppc') {
                session(['traffic_source' => 'paid_search']);
            } elseif ($utmMedium === 'referral') {
                session(['traffic_source' => 'referral']);
            } elseif ($utmMedium === 'banner') {
                session(['traffic_source' => 'banner_ad']);
            } elseif ($utmMedium === 'affiliate') {
                session(['traffic_source' => 'affiliate_marketing']);
            } elseif ($utmMedium === 'video') {
                session(['traffic_source' => 'video_ad']);
            } elseif ($utmMedium === 'print') {
                session(['traffic_source' => 'print_media']);
            } elseif (!$utmSource && !$request->headers->get('referer')) {
                // No UTM parameters or referrer means direct traffic
                session(['traffic_source' => 'direct']);
            } elseif ($request->headers->get('referer') && strpos($request->headers->get('referer'), 'google.com') !== false) {
                // Google Search referrer detection
                session(['traffic_source' => 'google_search']);
            } elseif ($request->headers->get('referer') && strpos($request->headers->get('referer'), 'facebook.com') !== false) {
                // Facebook referrer detection
                session(['traffic_source' => 'facebook']);
            } elseif ($request->headers->get('referer') && strpos($request->headers->get('referer'), 'twitter.com') !== false) {
                // Twitter referrer detection
                session(['traffic_source' => 'twitter']);
            } elseif ($request->headers->get('referer') && strpos($request->headers->get('referer'), 'linkedin.com') !== false) {
                // LinkedIn referrer detection
                session(['traffic_source' => 'linkedin']);
            } elseif ($utmMedium) {
                // Default: use UTM medium as the traffic source if none of the above conditions apply
                session(['traffic_source' => $utmMedium]);
            }
        }
    }
}
