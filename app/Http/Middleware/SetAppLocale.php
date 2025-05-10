<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isSetupComplete()) {

            $defaultLanguage = config('app.locale');

            // Get the language either from session or a default setting
            $locale = getLocale();

            // Cache the language data
            $language = Language::where('is_visible', true)->where('lang_code', $locale)->first();

            if ($language) {
                App::setLocale($language->lang_code);
                config([
                    'direction' => $language->rtl ? 'rtl' : 'ltr',
                ]);
            } else {
                App::setLocale($defaultLanguage);
                config([
                    'direction' => 'ltr',
                ]);
            }
        }

        return $next($request);
    }
}
