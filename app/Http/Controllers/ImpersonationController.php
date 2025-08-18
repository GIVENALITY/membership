<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Hotel;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user
     */
    public function start(Request $request, $userId)
    {
        // Check if current user is superadmin
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Access denied. Superadmin privileges required.');
        }

        $targetUser = User::findOrFail($userId);
        
        // Prevent impersonating another superadmin
        if ($targetUser->role === 'superadmin') {
            return back()->with('error', 'Cannot impersonate another superadmin.');
        }

        // Only allow impersonating admins and managers
        if (!in_array($targetUser->role, ['admin', 'manager'])) {
            return back()->with('error', 'Can only impersonate admins and managers.');
        }

        // Store the original user's ID in session
        session([
            'impersonator_id' => auth()->id(),
            'impersonator_name' => auth()->user()->name,
            'impersonator_email' => auth()->user()->email,
        ]);

        // Log the impersonation
        Log::info('Superadmin started impersonation', [
            'impersonator_id' => auth()->id(),
            'impersonator_email' => auth()->user()->email,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_hotel_id' => $targetUser->hotel_id,
        ]);

        // Login as the target user
        Auth::login($targetUser);

        return redirect()->route('dashboard')->with('success', 'Now impersonating ' . $targetUser->name . ' at ' . ($targetUser->hotel->name ?? 'Unknown Hotel'));
    }

    /**
     * Stop impersonating and return to original user
     */
    public function stop()
    {
        // Check if we're currently impersonating
        if (!session('impersonator_id')) {
            return redirect()->route('dashboard')->with('error', 'Not currently impersonating any user.');
        }

        $impersonatorId = session('impersonator_id');
        $impersonator = User::find($impersonatorId);

        if (!$impersonator) {
            // Clear session and redirect to login if impersonator no longer exists
            session()->forget(['impersonator_id', 'impersonator_name', 'impersonator_email']);
            Auth::logout();
            return redirect()->route('login')->with('error', 'Impersonation session ended. Please login again.');
        }

        // Log the end of impersonation
        Log::info('Superadmin stopped impersonation', [
            'impersonator_id' => $impersonatorId,
            'impersonator_email' => $impersonator->email,
            'target_user_id' => auth()->id(),
            'target_user_email' => auth()->user()->email,
        ]);

        // Clear impersonation session data
        session()->forget(['impersonator_id', 'impersonator_name', 'impersonator_email']);

        // Login as the original superadmin
        Auth::login($impersonator);

        return redirect()->route('superadmin.dashboard')->with('success', 'Returned to superadmin account.');
    }

    /**
     * Get current impersonation status
     */
    public function status()
    {
        $isImpersonating = session('impersonator_id') !== null;
        
        return response()->json([
            'is_impersonating' => $isImpersonating,
            'impersonator_name' => session('impersonator_name'),
            'impersonator_email' => session('impersonator_email'),
            'current_user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
                'hotel' => auth()->user()->hotel ? auth()->user()->hotel->name : null,
            ] : null,
        ]);
    }
}
