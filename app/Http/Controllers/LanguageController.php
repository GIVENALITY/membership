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

        try {
            // Set the locale in the session
            Session::put('locale', $locale);
            
            // Set the application locale (simple approach)
            App::setLocale($locale);
            
            // Set cookie for persistence
            $response = redirect()->back()->with('success', 'Language switched to ' . ($locale === 'en' ? 'English' : 'Swahili'));
            $response->withCookie('locale', $locale, 60 * 24 * 365); // 1 year

            Log::info('Language switched successfully', [
                'new_locale' => $locale,
                'session_locale' => Session::get('locale'),
                'app_locale' => App::getLocale()
            ]);

            return $response;
            
        } catch (\Exception $e) {
            Log::error('Language switch failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Fallback to simple redirect
            return redirect()->back()->with('error', 'Language switch failed');
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