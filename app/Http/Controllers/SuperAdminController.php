<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Hotel;

class SuperAdminController extends Controller
{
    /**
     * Check if user is superadmin
     */
    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(401, 'Authentication required.');
        }
        
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Access denied. Superadmin privileges required.');
        }
    }

    /**
     * Show superadmin dashboard
     */
    public function dashboard()
    {
        $this->checkSuperAdmin();
        
        $user = auth()->user();
        $stats = [
            'total_hotels' => Hotel::count(),
            'total_users' => User::count(),
            'active_hotels' => Hotel::where('is_active', true)->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        return view('superadmin.dashboard', compact('stats', 'user'));
    }

    /**
     * Show translation management page
     */
    public function translations()
    {
        $this->checkSuperAdmin();
        
        $languages = ['en', 'sw'];
        $translationFiles = [];
        
        foreach ($languages as $lang) {
            $langPath = lang_path($lang);
            if (File::exists($langPath)) {
                $files = File::files($langPath);
                foreach ($files as $file) {
                    $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $translationFiles[$lang][$filename] = require $file->getPathname();
                }
            }
        }

        return view('superadmin.translations', compact('translationFiles', 'languages'));
    }

    /**
     * Update translation file
     */
    public function updateTranslations(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'language' => 'required|in:en,sw',
            'file' => 'required|string',
            'translations' => 'required|array'
        ]);

        try {
            $lang = $request->language;
            $file = $request->file;
            $translations = $request->translations;

            $filePath = lang_path($lang . '/' . $file . '.php');
            
            if (!File::exists($filePath)) {
                return back()->with('error', 'Translation file not found');
            }

            // Create the PHP array content
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            
            // Write to file
            File::put($filePath, $content);

            // Clear cache
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');

            Log::info('Superadmin updated translations', [
                'language' => $lang,
                'file' => $file,
                'user' => auth()->user()->email
            ]);

            return back()->with('success', 'Translations updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update translations', [
                'error' => $e->getMessage(),
                'user' => auth()->user()->email
            ]);
            
            return back()->with('error', 'Failed to update translations: ' . $e->getMessage());
        }
    }

    /**
     * Show system settings
     */
    public function systemSettings()
    {
        $this->checkSuperAdmin();
        return view('superadmin.system-settings');
    }

    /**
     * Show all hotels
     */
    public function hotels()
    {
        $this->checkSuperAdmin();
        $hotels = Hotel::with('users')->paginate(20);
        return view('superadmin.hotels', compact('hotels'));
    }

    /**
     * Show all users
     */
    public function users()
    {
        $this->checkSuperAdmin();
        $users = User::with('hotel')->paginate(20);
        return view('superadmin.users', compact('users'));
    }

    /**
     * Show system logs
     */
    public function logs()
    {
        $this->checkSuperAdmin();
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (File::exists($logFile)) {
            $logs = collect(file($logFile))
                ->reverse()
                ->take(100)
                ->toArray();
        }

        return view('superadmin.logs', compact('logs'));
    }
} 