<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale from session on every request
        $this->app->booted(function () {
            try {
                // Get locale from session, fallback to cookie, then config
                $locale = Session::get('locale');
                
                // If session is null, try cookie
                if ($locale === null) {
                    $locale = request()->cookie('locale');
                }
                
                // Final fallback to config
                if ($locale === null) {
                    $locale = config('app.locale');
                }
                
                $availableLocales = ['en', 'sw'];
                
                if (in_array($locale, $availableLocales)) {
                    App::setLocale($locale);
                    app()->setLocale($locale);
                    
                    \Log::info('AppServiceProvider set locale', [
                        'session_locale' => Session::get('locale'),
                        'cookie_locale' => request()->cookie('locale'),
                        'final_locale' => $locale,
                        'app_locale' => App::getLocale()
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail - don't break the application
                \Log::warning('Locale setting failed in AppServiceProvider', [
                    'error' => $e->getMessage()
                ]);
            }
        });
    }
}
