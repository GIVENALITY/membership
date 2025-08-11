<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding screen
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || !$user->hotel) {
            return redirect()->route('login');
        }

        return view('onboarding.index');
    }

    /**
     * Mark onboarding as completed
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // You can add a field to the users table to track onboarding completion
        // For now, we'll use session or localStorage
        
        return response()->json(['success' => true]);
    }

    /**
     * Skip onboarding
     */
    public function skip()
    {
        return redirect()->route('dashboard')->with('info', 'You can always access the onboarding guide from your profile settings.');
    }
} 