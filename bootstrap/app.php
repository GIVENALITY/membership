<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Session;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Temporarily disable all middleware to debug 500 error
        // $middleware->append(\App\Http\Middleware\SetLocale::class);
        
        // Temporarily disable web middleware locale setting
        // $middleware->web(function ($request, $next) {
        //     $locale = Session::get('locale', config('app.locale'));
        //     $availableLocales = ['en', 'sw'];
        //     
        //     if (in_array($locale, $availableLocales)) {
        //         app()->setLocale($locale);
        //         $request->setLocale($locale);
        //     }
        //     
        //     return $next($request);
        // });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
