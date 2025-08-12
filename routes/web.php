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
use App\Http\Controllers\SuperAdminController;

// Test route for debugging 500 error
Route::get('/test-simple', function () {
    return response()->json([
        'status' => 'working',
        'message' => 'Simple route works',
        'timestamp' => now()
    ]);
});

// Test route for debugging dashboard
Route::get('/test-dashboard', function () {
    try {
        $user = auth()->user();
        return response()->json([
            'status' => 'auth_working',
            'user' => $user ? $user->name : 'no_user',
            'hotel' => $user && $user->hotel ? $user->hotel->name : 'no_hotel',
            'role' => $user ? $user->role : 'no_role'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Simple dashboard route for debugging
Route::get('/dashboard-simple', function () {
    try {
        $user = auth()->user();
        if (!$user) {
            return redirect('/login');
        }
        
        return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <title>Simple Dashboard</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
                    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                    .header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
                    .card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; background: #fafafa; }
                    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
                    .btn:hover { background: #0056b3; }
                    .success { color: green; }
                    .error { color: red; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Simple Dashboard (Debug Mode)</h1>
                        <p class="success">✅ Basic authentication working</p>
                    </div>
                    
                    <div class="card">
                        <h2>User Information</h2>
                        <p><strong>Name:</strong> ' . ($user->name ?? 'Unknown') . '</p>
                        <p><strong>Email:</strong> ' . ($user->email ?? 'Unknown') . '</p>
                        <p><strong>Role:</strong> ' . ($user->role ?? 'Unknown') . '</p>
                        <p><strong>Hotel:</strong> ' . ($user->hotel->name ?? 'No Hotel') . '</p>
                    </div>
                    
                    <div class="card">
                        <h2>Quick Navigation</h2>
                        <a href="/members" class="btn">Members</a>
                        <a href="/dining" class="btn">Dining</a>
                        <a href="/cashier" class="btn">Cashier</a>
                        <a href="/dashboard" class="btn">Full Dashboard</a>
                        <a href="/test-simple" class="btn">Test Simple</a>
                        <a href="/test-dashboard" class="btn">Test Auth</a>
                    </div>
                    
                    <div class="card">
                        <h2>Debug Information</h2>
                        <p><strong>PHP Version:</strong> ' . PHP_VERSION . '</p>
                        <p><strong>Laravel Version:</strong> ' . app()->version() . '</p>
                        <p><strong>Current Time:</strong> ' . now() . '</p>
                        <p><strong>Session ID:</strong> ' . session()->getId() . '</p>
                    </div>
                </div>
            </body>
            </html>
        ');
    } catch (\Exception $e) {
        return response()->make('
            <!DOCTYPE html>
            <html>
            <head>
                <title>Error - Simple Dashboard</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
                    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                    .error { color: red; background: #ffe6e6; padding: 15px; border-radius: 5px; border: 1px solid #ff9999; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Error in Simple Dashboard</h1>
                    <div class="error">
                        <h3>Error Details:</h3>
                        <p><strong>Message:</strong> ' . $e->getMessage() . '</p>
                        <p><strong>File:</strong> ' . $e->getFile() . '</p>
                        <p><strong>Line:</strong> ' . $e->getLine() . '</p>
                        <p><strong>Trace:</strong></p>
                        <pre>' . $e->getTraceAsString() . '</pre>
                    </div>
                </div>
            </body>
            </html>
        ');
    }
});

// Language switching routes - must be first to avoid wildcard conflicts
Route::get('/switch-language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Test route for debugging language switching
Route::get('/test-language', function () {
    return response()->json([
        'current_locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'config_locale' => config('app.locale'),
        'available_locales' => ['en', 'sw'],
        'test_translation' => __('app.welcome'),
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
})->name('test.language');

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

    // Superadmin Routes (only for superadmin users)
    Route::middleware('auth')->group(function () {
        Route::prefix('superadmin')->name('superadmin.')->group(function () {
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/translations', [SuperAdminController::class, 'translations'])->name('translations');
            Route::post('/translations/update', [SuperAdminController::class, 'updateTranslations'])->name('translations.update');
            Route::get('/system-settings', [SuperAdminController::class, 'systemSettings'])->name('system-settings');
            Route::get('/hotels', [SuperAdminController::class, 'hotels'])->name('hotels');
            Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
            Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
        });
    });
});
