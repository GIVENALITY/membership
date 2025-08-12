<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session, fallback to config default
        $locale = Session::get('locale', config('app.locale'));
        
        // Validate locale
        $availableLocales = ['en', 'sw'];
        if (!in_array($locale, $availableLocales)) {
            $locale = 'en';
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Log for debugging (only on first few requests to avoid spam)
        if ($request->is('dashboard') || $request->is('members*')) {
            Log::info('SetLocale middleware executed', [
                'request_path' => $request->path(),
                'session_locale' => Session::get('locale'),
                'config_locale' => config('app.locale'),
                'final_locale' => $locale,
                'app_locale' => App::getLocale()
            ]);
        }
        
        return $next($request);
    }
} 