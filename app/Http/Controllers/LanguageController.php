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
        
        // Set the application locale
        App::setLocale($locale);

        Log::info('Language switched successfully', [
            'new_locale' => $locale,
            'session_locale' => Session::get('locale'),
            'app_locale' => App::getLocale()
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