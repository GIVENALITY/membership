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
        // Temporarily disable locale setting to debug blank page issue
        // Set the application locale from session on every request
        // $this->app->booted(function () {
        //     $locale = Session::get('locale', config('app.locale'));
        //     $availableLocales = ['en', 'sw'];
        //     
        //     if (in_array($locale, $availableLocales)) {
        //         App::setLocale($locale);
        //         $this->app->setLocale($locale);
        //     }
        // });
    }
}
