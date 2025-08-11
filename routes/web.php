<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HotelController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    Route::get('/settings/profile', function () {
        return view('settings.profile');
    })->name('settings.profile');

    Route::get('/settings/account', function () {
        return view('settings.account');
    })->name('settings.account');



    // Members Routes
    Route::resource('members', \App\Http\Controllers\MemberController::class);
    Route::get('/members/search', [\App\Http\Controllers\MemberController::class, 'search'])->name('members.search');
    Route::get('/members/search-page', function () {
        return view('members.search');
    })->name('members.search-page');

    // Membership Types Routes
    Route::resource('membership-types', \App\Http\Controllers\MembershipTypeController::class);

    // Cashier Routes
    Route::get('/cashier', function () {
        return view('cashier.index');
    })->name('cashier.index');

    // Dining Routes
    Route::get('/dining', function () {
        return view('dining.index');
    })->name('dining.index');
    Route::post('/dining/visits', [\App\Http\Controllers\DiningVisitController::class, 'store'])->name('dining.visits.store');

    // Discounts Routes
    Route::get('/discounts', function () {
        return view('discounts.index');
    })->name('discounts.index');

    // Reports Routes
    Route::get('/reports/members', function () {
        return view('reports.dining');
    })->name('reports.members');

    Route::get('/reports/dining', function () {
        return view('reports.dining');
    })->name('reports.dining');

    Route::get('/reports/discounts', function () {
        return view('reports.discounts');
    })->name('reports.discounts');

    // Notifications Routes
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');

    // Hotel Management Routes
    Route::get('/hotel/profile', [HotelController::class, 'profile'])->name('hotel.profile');
    Route::put('/hotel/profile', [HotelController::class, 'updateProfile'])->name('hotel.profile.update');
    Route::get('/hotel/account', [HotelController::class, 'account'])->name('hotel.account');
    Route::put('/hotel/account', [HotelController::class, 'updateAccount'])->name('hotel.account.update');
});
