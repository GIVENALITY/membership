<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\PhysicalCardController;
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
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\MemberImportController;
use App\Http\Controllers\RestaurantSettingsController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\QuickViewController;
use App\Http\Controllers\MemberAlertController;
use App\Http\Controllers\PointsConfigurationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\MemberEmailController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\RateLimitedEmailController;
use App\Http\Controllers\MemberApprovalController;
use App\Http\Controllers\WaiterCheckoutController;

// Landing page route
Route::get('/landing', function () {
    try {
        return view('landing');
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'View error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
})->name('landing');

// Root route redirects to landing
Route::get('/', function () {
    return redirect('/landing');
});

// Fallback route for root
Route::get('/public', function () {
    return redirect('/');
});

// Test route to verify routing is working
Route::get('/test-landing', function () {
    return 'Landing page route is working!';
});

// Simple test route for debugging
Route::get('/test-root', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Root route is working',
        'url' => request()->url(),
        'path' => request()->path()
    ]);
});

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

// Public Event Routes (must come after admin routes to avoid conflicts)
Route::get('/public/events', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/public/events/{hotelSlug}', [PublicEventController::class, 'index'])->name('public.events.hotel');
Route::get('/public/events/{hotelSlug}/{event}', [PublicEventController::class, 'show'])->name('public.events.show')->where('event', '[0-9]+');
Route::get('/public/events/{hotelSlug}/{event}/register', [PublicEventController::class, 'register'])->name('public.events.register')->where('event', '[0-9]+');
Route::post('/public/events/{hotelSlug}/{event}/register', [PublicEventController::class, 'processRegistration'])->name('public.events.process-registration')->where('event', '[0-9]+');
Route::get('/public/events/{hotelSlug}/search', [PublicEventController::class, 'searchForm'])->name('public.events.search');
Route::post('/public/events/{hotelSlug}/search', [PublicEventController::class, 'searchRegistration'])->name('public.events.search-registration');
Route::get('/public/events/{hotelSlug}/{event}/confirmation/{registrationId}', [PublicEventController::class, 'confirmation'])->name('public.events.confirmation')->where(['event' => '[0-9]+', 'registrationId' => '[0-9]+']);

// Temporary test route to see if the pattern is being matched
Route::get('/public/events/{hotelSlug}/{event}/confirmation-test/{registrationId}', function($hotelSlug, $event, $registrationId) {
    return response()->json([
        'message' => 'Route pattern matched!',
        'hotelSlug' => $hotelSlug,
        'event' => $event,
        'registrationId' => $registrationId
    ]);
})->name('public.events.confirmation.test');
Route::post('/public/events/{hotelSlug}/{event}/cancel/{registrationId}', [PublicEventController::class, 'cancelRegistration'])->name('public.events.cancel-registration')->where(['event' => '[0-9]+', 'registrationId' => '[0-9]+']);

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
    Route::get('/member-search', function () {
        return view('members.search');
    })->name('members.search-page');
    
    // Test route to verify routing is working
    Route::get('/test-members-create', function () {
        return 'Members create route is working!';
    })->name('test.members.create');
    
    // Member Import Routes (MUST come before resource route)
Route::get('/members/import', [MemberImportController::class, 'index'])->name('members.import');
Route::post('/members/import', [MemberImportController::class, 'import'])->name('members.import.process');
Route::post('/members/import/storage', [MemberImportController::class, 'importFromStorage'])->name('members.import.storage');
Route::get('/members/import/membership-types', [MemberImportController::class, 'getMembershipTypes'])->name('members.import.membership-types');
Route::get('/members/import/template', [MemberImportController::class, 'downloadTemplate'])->name('members.import.template');

// Member Email Routes (MUST come before resource route)
Route::get('/member-emails/test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Email route is working',
        'user' => auth()->user() ? auth()->user()->name : 'no user',
        'hotel' => auth()->user() && auth()->user()->hotel ? auth()->user()->hotel->name : 'no hotel',
        'mail_config' => [
            'default' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]
    ]);
})->name('members.emails.test');

Route::get('/member-emails/test-welcome/{memberId}', function($memberId) {
    try {
        $member = \App\Models\Member::findOrFail($memberId);
        
        // Build welcome email content
        $subject = 'Welcome to ' . $member->hotel->name . ' - Your Membership is Ready!';
        $bodyText = "Dear " . $member->first_name . ",\n\n" .
                   'Welcome to ' . $member->hotel->name . "! We're excited to have you as a member of our exclusive program.\n\n" .
                   "Your membership has been successfully activated and you now have access to all the benefits and privileges that come with being part of our community.\n" .
                   "We look forward to providing you with exceptional service and memorable experiences.\n\n" .
                   "Best regards,\n\n" . $member->hotel->name . ' Team';
        
        // Convert \n to HTML line breaks for display
        $bodyTextForDisplay = nl2br($bodyText);
        
        // Send the welcome email
        \Mail::to($member->email)->send(new \App\Mail\WelcomeMemberMail($member, $subject, $bodyText));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Welcome email sent successfully!',
            'member' => [
                'id' => $member->id,
                'name' => $member->full_name,
                'email' => $member->email,
                'membership_id' => $member->membership_id,
                'hotel' => $member->hotel->name
            ],
            'email_details' => [
                'subject' => $subject,
                'to' => $member->email,
                'from' => config('mail.from.address'),
                'from_name' => $member->hotel->name
            ],
            'email_content' => [
                'raw_text' => $bodyText,
                'formatted_html' => $bodyTextForDisplay
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send welcome email: ' . $e->getMessage(),
            'error_details' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ], 500);
    }
})->name('members.emails.test-welcome');

Route::get('/member-emails/list-members', function() {
    try {
        $members = \App\Models\Member::with('hotel')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'membership_id', 'hotel_id']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Available members for testing',
            'members' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'membership_id' => $member->membership_id,
                    'hotel' => $member->hotel ? $member->hotel->name : 'No Hotel'
                ];
            })
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to list members: ' . $e->getMessage()
        ], 500);
    }
})->name('members.emails.list-members');

Route::get('/member-emails', [MemberEmailController::class, 'index'])->name('members.emails.index');
Route::get('/member-emails-simple', function() {
    return 'Email route simple test - working!';
})->name('members.emails.simple');
Route::get('/member-emails/compose', [MemberEmailController::class, 'compose'])->name('members.emails.compose');
Route::post('/member-emails/send', [MemberEmailController::class, 'send'])->name('members.emails.send');
Route::get('/member-emails/suggestions', [MemberEmailController::class, 'getMemberSuggestions'])->name('members.emails.suggestions');
Route::get('/member-emails/count', [MemberEmailController::class, 'getRecipientCount'])->name('members.emails.count');
Route::get('/member-emails/bounced', [MemberEmailController::class, 'getBouncedMembers'])->name('members.emails.bounced');
Route::get('/member-emails/statistics', [MemberEmailController::class, 'statistics'])->name('members.emails.statistics');

// Email Logs Routes
Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
Route::get('/email-logs/{emailLog}', [EmailLogController::class, 'show'])->name('email-logs.show');
Route::post('/email-logs/{emailLog}/retry', [EmailLogController::class, 'retry'])->name('email-logs.retry');
Route::get('/email-logs/statistics', [EmailLogController::class, 'statistics'])->name('email-logs.statistics');
Route::get('/email-logs/export', [EmailLogController::class, 'export'])->name('email-logs.export');

// Debug route for testing registration
Route::get('/debug/registration/{id}', function($id) {
    $registration = \App\Models\EventRegistration::find($id);
    if (!$registration) {
        return response()->json(['error' => 'Registration not found']);
    }
    
    return response()->json([
        'id' => $registration->id,
        'event_id' => $registration->event_id,
        'name' => $registration->name,
        'email' => $registration->email,
        'registration_code' => $registration->registration_code,
        'status' => $registration->status,
        'confirmed_at' => $registration->confirmed_at,
        'event' => [
            'id' => $registration->event->id,
            'title' => $registration->event->title,
            'hotel_slug' => $registration->event->hotel->slug
        ]
    ]);
})->name('debug.registration');

// Debug route for testing admin confirmation
Route::get('/debug/admin-confirm/{eventId}/{registrationId}', function($eventId, $registrationId) {
    $event = \App\Models\Event::find($eventId);
    $registration = \App\Models\EventRegistration::find($registrationId);
    
    if (!$event || !$registration) {
        return response()->json(['error' => 'Event or registration not found']);
    }
    
    return response()->json([
        'event' => [
            'id' => $event->id,
            'title' => $event->title,
            'hotel_id' => $event->hotel_id
        ],
        'registration' => [
            'id' => $registration->id,
            'event_id' => $registration->event_id,
            'name' => $registration->name,
            'status' => $registration->status
        ],
        'match' => $registration->event_id == $event->id
    ]);
})->name('debug.admin.confirm');

// Debug route for testing public confirmation
Route::get('/debug/public-confirm/{hotelSlug}/{eventId}/{registrationId}', function($hotelSlug, $eventId, $registrationId) {
    $event = \App\Models\Event::find($eventId);
    $registration = \App\Models\EventRegistration::find($registrationId);
    
    if (!$event) {
        return response()->json(['error' => 'Event not found', 'eventId' => $eventId]);
    }
    
    if (!$registration) {
        return response()->json(['error' => 'Registration not found', 'registrationId' => $registrationId]);
    }
    
    return response()->json([
        'hotelSlug' => $hotelSlug,
        'event' => [
            'id' => $event->id,
            'title' => $event->title,
            'hotel_id' => $event->hotel_id,
            'hotel_slug' => $event->hotel->slug ?? 'null'
        ],
        'registration' => [
            'id' => $registration->id,
            'event_id' => $registration->event_id,
            'name' => $registration->name,
            'status' => $registration->status
        ],
        'matches' => [
            'event_hotel_match' => $event->hotel->slug === $hotelSlug,
            'registration_event_match' => $registration->event_id == $event->id
        ]
    ]);
})->name('debug.public.confirm');

// Member Card Management Routes
Route::get('/members/cards', [MemberCardController::class, 'index'])->name('members.cards.index');
Route::post('/members/cards/mass-generate', [MemberCardController::class, 'massGenerate'])->name('members.cards.mass-generate');
Route::post('/members/{member}/cards/generate', [MemberCardController::class, 'generateCard'])->name('members.cards.generate');
Route::get('/members/{member}/cards/view', [MemberCardController::class, 'viewCard'])->name('members.cards.view');
Route::get('/members/{member}/cards/download', [MemberCardController::class, 'downloadCard'])->name('members.cards.download');
Route::delete('/members/{member}/cards/delete', [MemberCardController::class, 'deleteCard'])->name('members.cards.delete');
Route::get('/members/cards/stats', [MemberCardController::class, 'getStats'])->name('members.cards.stats');
Route::get('/members/{member}/cards/debug', [MemberCardController::class, 'debugCard'])->name('members.cards.debug');

// Physical Card Management Routes
Route::get('/members/physical-cards', [PhysicalCardController::class, 'index'])->name('members.physical-cards.index');
Route::get('/members/{member}/physical-cards/issue', [PhysicalCardController::class, 'issueForm'])->name('members.physical-cards.issue-form');
Route::post('/members/{member}/physical-cards/issue', [PhysicalCardController::class, 'issue'])->name('members.physical-cards.issue');
Route::post('/members/{member}/physical-cards/update-status', [PhysicalCardController::class, 'updateStatus'])->name('members.physical-cards.update-status');
Route::post('/members/physical-cards/mass-issue', [PhysicalCardController::class, 'massIssue'])->name('members.physical-cards.mass-issue');
Route::get('/members/physical-cards/stats', [PhysicalCardController::class, 'getStats'])->name('members.physical-cards.stats');

       // Birthday Routes
       Route::get('/birthdays/today', [BirthdayController::class, 'today'])->name('birthdays.today');
       Route::get('/birthdays/this-week', [BirthdayController::class, 'thisWeek'])->name('birthdays.this-week');
       Route::get('/birthdays/notifications', [BirthdayController::class, 'getNotifications'])->name('birthdays.notifications');


           
       Route::get('/members/{member}/points-history', function ($member) {
        return view('members.points-history', compact('member'));
    })->name('members.points-history');
    
    // Resource route comes BEFORE specific member routes to avoid conflicts
    Route::resource('members', MemberController::class);
    
    // These routes come AFTER the resource route to avoid conflicts
    Route::get('/members/{member}/json', [MemberController::class, 'showJson'])->name('members.show.json');

    // Membership Types Routes
    Route::resource('membership-types', MembershipTypeController::class);
    Route::delete('/membership-types/delete-all', [MembershipTypeController::class, 'deleteAll'])->name('membership-types.delete-all');

    // QuickView Routes
    Route::get('/quickview', [QuickViewController::class, 'index'])->name('quickview.index');
    Route::post('/quickview/lookup', [QuickViewController::class, 'lookupMember'])->name('quickview.lookup');
    Route::post('/quickview/process-payment', [QuickViewController::class, 'processPayment'])->name('quickview.process-payment');

    // Member Alerts Routes
    Route::resource('alerts', MemberAlertController::class);
    Route::get('/alerts/{alert}/triggers', [MemberAlertController::class, 'triggers'])->name('alerts.triggers');
    Route::post('/alerts/{alert}/test', [MemberAlertController::class, 'test'])->name('alerts.test');
    Route::post('/alerts/triggers/{trigger}/acknowledge', [MemberAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/triggers/{trigger}/resolve', [MemberAlertController::class, 'resolve'])->name('alerts.resolve');
    Route::get('/alerts/api/active', [MemberAlertController::class, 'getActiveAlerts'])->name('alerts.api.active');

    // Points Configuration Routes
    Route::resource('points-configuration', PointsConfigurationController::class);
    Route::post('/points-configuration/test', [PointsConfigurationController::class, 'test'])->name('points-configuration.test');
    Route::get('/points-configuration/multipliers', [PointsConfigurationController::class, 'multipliers'])->name('points-configuration.multipliers');
    Route::get('/points-configuration/tiers', [PointsConfigurationController::class, 'tiers'])->name('points-configuration.tiers');

    // Events Routes (specific routes first to avoid conflicts with resource route)
    Route::post('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
    Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::post('/events/{event}/close-registration', [EventController::class, 'closeRegistration'])->name('events.close-registration');
    Route::post('/events/{event}/open-registration', [EventController::class, 'openRegistration'])->name('events.open-registration');
    Route::get('/events/{event}/registrations', [EventController::class, 'registrations'])->name('events.registrations');
    Route::post('/events/{event}/registrations/{registration}/confirm', [EventController::class, 'confirmRegistration'])->name('events.confirm-registration');
    Route::post('/events/{event}/registrations/{registration}/cancel', [EventController::class, 'cancelRegistration'])->name('events.cancel-registration');
    Route::post('/events/{event}/registrations/{registration}/attend', [EventController::class, 'markAttended'])->name('events.mark-attended');
Route::delete('/events/{event}/registrations/{registration}', [EventController::class, 'deleteRegistration'])->name('events.delete-registration');
Route::post('/events/{event}/registrations/bulk-delete', [EventController::class, 'bulkDeleteTestRegistrations'])->name('events.bulk-delete-registrations');
    Route::get('/events/{event}/search-members', [EventController::class, 'searchMembers'])->name('events.search-members');
    Route::post('/events/{event}/register-member', [EventController::class, 'registerMember'])->name('events.register-member');
    Route::get('/events/{event}/export-registrations', [EventController::class, 'exportRegistrations'])->name('events.export-registrations');
    
    // Events resource route (must come after specific routes)
    Route::resource('events', EventController::class);



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

    // Member Approval Workflow Routes
    Route::get('/members/approval', [MemberApprovalController::class, 'index'])->name('members.approval.index');
    Route::get('/members/approval/{member}', [MemberApprovalController::class, 'show'])->name('members.approval.show');
    Route::post('/members/approval/{member}/approve', [MemberApprovalController::class, 'approve'])->name('members.approval.approve');
    Route::post('/members/approval/{member}/reject', [MemberApprovalController::class, 'reject'])->name('members.approval.reject');
    Route::post('/members/approval/{member}/verify-payment', [MemberApprovalController::class, 'verifyPayment'])->name('members.approval.verify-payment');
    Route::post('/members/approval/{member}/approve-card-issuance', [MemberApprovalController::class, 'approveCardIssuance'])->name('members.approval.approve-card-issuance');
    Route::post('/members/approval/{member}/upload-payment-proof', [MemberApprovalController::class, 'uploadPaymentProof'])->name('members.approval.upload-payment-proof');
    Route::get('/members/approval/{member}/download-payment-proof', [MemberApprovalController::class, 'downloadPaymentProof'])->name('members.approval.download-payment-proof');

    // Waiter Checkout Routes
    Route::get('/waiter/checkout', [WaiterCheckoutController::class, 'index'])->name('waiter.checkout.index');
    Route::get('/waiter/checkout/{visit}', [WaiterCheckoutController::class, 'showCheckout'])->name('waiter.checkout.show');
    Route::post('/waiter/checkout/{visit}/process', [WaiterCheckoutController::class, 'processCheckout'])->name('waiter.checkout.process');
    Route::get('/waiter/checkout/search-members', [WaiterCheckoutController::class, 'searchMembers'])->name('waiter.checkout.search-members');
    Route::post('/waiter/checkout/assign-member', [WaiterCheckoutController::class, 'assignMember'])->name('waiter.checkout.assign-member');
    Route::get('/waiter/checkout/{visit}/download-receipt', [WaiterCheckoutController::class, 'downloadReceipt'])->name('waiter.checkout.download-receipt');

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
    
    // Restaurant Settings Routes
    Route::get('/restaurant/settings', [RestaurantSettingsController::class, 'index'])->name('restaurant.settings');
    Route::post('/restaurant/settings', [RestaurantSettingsController::class, 'update'])->name('restaurant.settings.update');
    Route::get('/restaurant/settings/api', [RestaurantSettingsController::class, 'getSettings'])->name('restaurant.settings.api');

    // User Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');
    Route::get('/profile/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::put('/profile/change-password', [UserController::class, 'updatePassword'])->name('users.password.update');

    // User Management Routes
    Route::resource('user-management', UserManagementController::class);
    Route::patch('/user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');

    // Superadmin Routes (only for superadmin users)
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/translations', [SuperAdminController::class, 'translations'])->name('translations');
        Route::post('/translations/update', [SuperAdminController::class, 'updateTranslations'])->name('translations.update');
        Route::get('/system-settings', [SuperAdminController::class, 'systemSettings'])->name('system-settings');
        Route::get('/hotels', [SuperAdminController::class, 'hotels'])->name('hotels');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
    });

    // Impersonation Routes
    Route::prefix('impersonate')->name('impersonate.')->group(function () {
        Route::get('/start/{userId}', [ImpersonationController::class, 'start'])->name('start');
        Route::get('/stop', [ImpersonationController::class, 'stop'])->name('stop');
        Route::get('/status', [ImpersonationController::class, 'status'])->name('status');
    });

    // Rate-Limited Email Sending
    Route::get('/rate-limited-emails', [RateLimitedEmailController::class, 'index'])->name('rate-limited-emails.index');
    Route::post('/rate-limited-emails/start', [RateLimitedEmailController::class, 'startBatch'])->name('rate-limited-emails.start');
    Route::get('/rate-limited-emails/progress', [RateLimitedEmailController::class, 'getProgress'])->name('rate-limited-emails.progress');
    Route::post('/rate-limited-emails/stop', [RateLimitedEmailController::class, 'stopBatch'])->name('rate-limited-emails.stop');
    Route::post('/rate-limited-emails/retry', [RateLimitedEmailController::class, 'retryFailed'])->name('rate-limited-emails.retry');
});

