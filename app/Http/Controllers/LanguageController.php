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
            'user_agent' => request()->userAgent()
        ]);

        // Validate the locale
        $availableLocales = ['en', 'sw'];
        
        if (!in_array($locale, $availableLocales)) {
            Log::warning('Invalid locale requested', ['locale' => $locale]);
            $locale = 'en'; // Default to English if invalid
        }

        // Set the locale in the session
        Session::put('locale', $locale);
        
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
            'app_locale' => App::getLocale(),
            'request_locale' => request()->getLocale(),
            'app_instance_locale' => app()->getLocale(),
            'config_locale' => config('app.locale')
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