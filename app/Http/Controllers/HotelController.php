<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class HotelController extends Controller
{
    /**
     * Show hotel profile management page
     */
    public function profile()
    {
        $user = Auth::user();
        $hotel = $user->hotel;
        
        return view('hotel.profile', compact('hotel', 'user'));
    }

    /**
     * Update hotel profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $hotel = $user->hotel;

        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'hotel_email' => 'required|string|email|max:255|unique:hotels,email,' . $hotel->id,
            'hotel_phone' => 'nullable|string|max:20',
            'hotel_address' => 'nullable|string|max:500',
            'hotel_city' => 'nullable|string|max:100',
            'hotel_country' => 'nullable|string|max:100',
            'currency' => 'required|string|size:3|in:' . implode(',', array_keys(\App\Models\Hotel::getAvailableCurrencies())),
            'currency_symbol' => 'required|string|max:5',
            'hotel_website' => 'nullable|url|max:255',
            'hotel_description' => 'nullable|string|max:1000',
            'hotel_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hotel_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'tertiary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'reply_to_email' => 'nullable|email|max:255',
            'email_logo_url' => 'nullable|url|max:500',
            'email_primary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'email_secondary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'email_accent_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
        ]);

        try {
            $hotel->update([
                'name' => $request->hotel_name,
                'email' => $request->hotel_email,
                'phone' => $request->hotel_phone,
                'address' => $request->hotel_address,
                'city' => $request->hotel_city,
                'country' => $request->hotel_country,
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'website' => $request->hotel_website,
                'description' => $request->hotel_description,
                'primary_color' => $request->primary_color,
                'secondary_color' => $request->secondary_color,
                'tertiary_color' => $request->tertiary_color,
                'reply_to_email' => $request->reply_to_email,
                'email_logo_url' => $request->email_logo_url,
                'email_primary_color' => $request->email_primary_color,
                'email_secondary_color' => $request->email_secondary_color,
                'email_accent_color' => $request->email_accent_color,
            ]);

            // Handle logo upload
            if ($request->hasFile('hotel_logo')) {
                // Delete old logo if exists
                if ($hotel->logo_path && Storage::disk('public')->exists($hotel->logo_path)) {
                    Storage::disk('public')->delete($hotel->logo_path);
                }
                
                $logoPath = $request->file('hotel_logo')->store('hotels/logos', 'public');
                $hotel->update(['logo_path' => $logoPath]);
            }

            // Handle banner upload
            if ($request->hasFile('hotel_banner')) {
                // Delete old banner if exists
                if ($hotel->banner_path && Storage::disk('public')->exists($hotel->banner_path)) {
                    Storage::disk('public')->delete($hotel->banner_path);
                }
                
                $bannerPath = $request->file('hotel_banner')->store('hotels/banners', 'public');
                $hotel->update(['banner_path' => $bannerPath]);
            }

            return back()->with('success', 'Hotel profile updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update hotel profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Show account settings page
     */
    public function account()
    {
        $user = Auth::user();
        return view('hotel.account', compact('user'));
    }

    /**
     * Update admin account information
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|confirmed|min:8',
        ]);

        try {
            // Check current password if provided
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect.']);
                }
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->filled('new_password')) {
                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);
            }

            return back()->with('success', 'Account updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update account: ' . $e->getMessage()]);
        }
    }

    /**
     * Show hotel statistics and overview
     */
    public function dashboard()
    {
        $user = Auth::user();
        $hotel = $user->hotel;

        // Get hotel statistics
        $stats = [
            'total_members' => $hotel->members()->count(),
            'active_members' => $hotel->members()->where('status', 'active')->count(),
            'total_membership_types' => $hotel->membershipTypes()->count(),
            'total_visits_this_month' => $hotel->diningVisits()
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_revenue_this_month' => $hotel->diningVisits()
                ->whereMonth('created_at', now()->month)
                ->sum('amount_spent'),
        ];

        // Get recent members
        $recentMembers = $hotel->members()
            ->with('membershipType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent visits
        $recentVisits = $hotel->diningVisits()
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('hotel.dashboard', compact('hotel', 'stats', 'recentMembers', 'recentVisits'));
    }
} 