<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale');

        if (! $locale) {
            $user = auth()->user();
            $locale = $user?->preferred_locale ?? config('app.locale', 'en');
            // Ensure default is English
            if ($locale === 'bn') {
                $locale = 'en';
            }
            Session::put('locale', $locale);
        }

        if (in_array($locale, ['bn', 'en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
