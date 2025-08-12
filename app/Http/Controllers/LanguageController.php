<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switchLanguage($locale)
    {
        // Validate the locale
        $availableLocales = ['en', 'sw'];
        
        if (!in_array($locale, $availableLocales)) {
            $locale = 'en'; // Default to English if invalid
        }

        // Set the locale in the session
        Session::put('locale', $locale);
        
        // Set the application locale
        App::setLocale($locale);

        // Redirect back to the previous page
        return redirect()->back();
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