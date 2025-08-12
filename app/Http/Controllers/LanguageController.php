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
        try {
            // Log the request for debugging
            Log::info('Language switch requested', [
                'locale' => $locale,
                'current_locale' => App::getLocale(),
                'user_agent' => request()->userAgent()
            ]);

            // Validate the locale
            $availableLocales = ['en', 'sw'];
            
            if (!in_array($locale, $availableLocales)) {
                Log::warning('Invalid locale requested', ['locale' => $locale]);
                $locale = 'en'; // Default to English if invalid
            }

            // Try to set the locale in the session (with error handling)
            try {
                Session::put('locale', $locale);
                Log::info('Session locale set successfully', ['locale' => $locale]);
            } catch (\Exception $e) {
                Log::warning('Failed to set session locale', ['error' => $e->getMessage()]);
            }
            
            // Set it in a cookie as primary method (more reliable)
            cookie()->queue('locale', $locale, 60 * 24 * 365); // 1 year
            
            // Set the application locale
            App::setLocale($locale);
            app()->setLocale($locale);
            request()->setLocale($locale);

            Log::info('Language switched successfully', [
                'new_locale' => $locale,
                'app_locale' => App::getLocale(),
                'cookie_locale' => request()->cookie('locale')
            ]);

            // Redirect back to the previous page
            return redirect()->back()->with('success', 'Language switched to ' . ($locale === 'en' ? 'English' : 'Swahili'));
            
        } catch (\Exception $e) {
            Log::error('Language switch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback redirect
            return redirect()->back()->with('error', 'Language switch failed. Please try again.');
        }
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