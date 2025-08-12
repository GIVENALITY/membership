<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switchLanguage($locale)
    {
        // Log the request for debugging
        Log::info('Language switch requested', [
            'locale' => $locale,
            'current_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'cookie_locale' => request()->cookie('locale'),
            'user_agent' => request()->userAgent(),
            'session_id' => Session::getId()
        ]);

        // Validate the locale
        $availableLocales = ['en', 'sw'];
        
        if (!in_array($locale, $availableLocales)) {
            Log::warning('Invalid locale requested', ['locale' => $locale]);
            $locale = 'en'; // Default to English if invalid
        }

        // Set the locale in the session
        Session::put('locale', $locale);
        
        // Also set it in a cookie as fallback (Laravel 11 session persistence issue)
        cookie()->queue('locale', $locale, 60 * 24 * 365); // 1 year
        
        // For Laravel 11, set the locale in multiple places to ensure it persists
        // Set the application locale
        App::setLocale($locale);
        
        // Set the locale in the request (Laravel 11 specific)
        request()->setLocale($locale);
        
        // Set the locale in the application instance
        app()->setLocale($locale);
        
        // Set the locale in the container
        $this->app->setLocale($locale);
        
        // Also set it in the config for this request
        config(['app.locale' => $locale]);

        Log::info('Language switched successfully', [
            'new_locale' => $locale,
            'session_locale' => Session::get('locale'),
            'cookie_locale' => request()->cookie('locale'),
            'app_locale' => App::getLocale(),
            'request_locale' => request()->getLocale(),
            'app_instance_locale' => app()->getLocale(),
            'config_locale' => config('app.locale'),
            'session_id' => Session::getId()
        ]);

        // Redirect back to the previous page
        return redirect()->back()->with('success', 'Language switched to ' . ($locale === 'en' ? 'English' : 'Swahili'));
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'current' => App::getLocale(),
            'available' => ['en', 'sw']
        ]);
    }
} 