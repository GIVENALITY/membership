<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiningHistoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\DiningVisitController;
use App\Http\Controllers\OnboardingController;

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



    // Members Routes - Specific routes must come BEFORE the resource route
    Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
    Route::get('/members/search-page', function () {
        return view('members.search');
    })->name('members.search-page');
    Route::get('/members/{member}/points-history', function ($member) {
        return view('members.points-history', compact('member'));
    })->name('members.points-history');
    
    // Resource route comes LAST to avoid catching specific routes
    Route::resource('members', MemberController::class);

    // Membership Types Routes
    Route::resource('membership-types', MembershipTypeController::class);

    // Cashier Routes
    Route::get('/cashier', function () {
        return view('cashier.index');
    })->name('cashier.index');

    // Onboarding Routes
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::get('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');

    // Dining Routes
    Route::get('/dining', [DiningVisitController::class, 'index'])->name('dining.index');
    Route::get('/dining/search-members', [DiningVisitController::class, 'searchMembers'])->name('dining.search-members');
    Route::post('/dining/record-visit', [DiningVisitController::class, 'recordVisit'])->name('dining.record-visit');
    Route::put('/dining/{visit}/checkout', [DiningVisitController::class, 'checkout'])->name('dining.checkout');
    Route::delete('/dining/{visit}/cancel', [DiningVisitController::class, 'cancelVisit'])->name('dining.cancel');

    // Dining History Routes (MUST come before the wildcard route)
    Route::get('/dining/history', [DiningHistoryController::class, 'index'])->name('dining.history');
    Route::get('/dining/history/export', [DiningHistoryController::class, 'export'])->name('dining.history.export');
    Route::get('/dining/history/member/{member}', [DiningHistoryController::class, 'memberHistory'])->name('dining.history.member');

    // This wildcard route must come LAST to avoid catching specific routes
    Route::get('/dining/{visit}', [DiningVisitController::class, 'show'])->name('dining.show');

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

    // User Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');
    Route::get('/profile/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::put('/profile/change-password', [UserController::class, 'updatePassword'])->name('users.password.update');
});
