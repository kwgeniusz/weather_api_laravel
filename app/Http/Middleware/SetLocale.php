<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');
        
        if ($locale) {
            // Get the first locale from the Accept-Language header
            $locale = explode(',', $locale)[0];
            // Get the primary language without the region
            $locale = explode('-', $locale)[0];
            
            // Check if the locale is supported
            if (in_array($locale, config('app.supported_locales', ['en']))) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
