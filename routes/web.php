<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\DiningVisitController;
use App\Http\Controllers\DiningHistoryController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;

// Language switching routes
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Application Settings Routes
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');

    Route::get('/settings/points', function () {
        return view('settings.points');
    })->name('settings.points');

    Route::get('/settings/email', function () {
        return view('settings.email');
    })->name('settings.email');

    Route::get('/settings/discounts', function () {
        return view('settings.discounts');
    })->name('settings.discounts');


    // Members Routes - Specific routes must come BEFORE the resource route
    Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
    Route::get('/members/search-page', function () {
        return view('members.search');
    })->name('members.search-page');
    Route::get('/members/{member}/points-history', function ($member) {
        return view('members.points-history', compact('member'));
    })->name('members.points-history');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/json', [MemberController::class, 'showJson'])->name('members.show.json');
    
    // Resource route comes LAST to avoid catching specific routes
    Route::resource('members', MemberController::class);

    // Membership Types Routes
    Route::resource('membership-types', MembershipTypeController::class);
    Route::delete('/membership-types/delete-all', [MembershipTypeController::class, 'deleteAll'])->name('membership-types.delete-all');

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
    Route::get('/dining/current-visits', [DiningVisitController::class, 'currentVisits'])->name('dining.current-visits');
    Route::post('/dining/record-visit', [DiningVisitController::class, 'recordVisit'])->name('dining.record-visit');
    Route::post('/dining/process-payment', [DiningVisitController::class, 'processPayment'])->name('dining.process-payment');
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
        $user = auth()->user();
        $membershipTypes = \App\Models\MembershipType::where('hotel_id', $user->hotel_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        return view('discounts.index', compact('membershipTypes'));
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

    // User Management Routes
    Route::resource('user-management', UserManagementController::class);
    Route::patch('/user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');
});
