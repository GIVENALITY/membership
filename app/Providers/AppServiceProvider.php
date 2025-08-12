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
        // Simple locale setting without middleware dependency
        $this->app->booted(function () {
            try {
                $locale = Session::get('locale', config('app.locale'));
                $availableLocales = ['en', 'sw'];
                
                if (in_array($locale, $availableLocales)) {
                    App::setLocale($locale);
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
