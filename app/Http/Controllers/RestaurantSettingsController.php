<?php

namespace App\Http\Controllers;

use App\Models\RestaurantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantSettingsController extends Controller
{
    /**
     * Show the restaurant settings page
     */
    public function index()
    {
        $user = Auth::user();
        $hotel = $user->hotel;
        
        if (!$hotel) {
            return back()->with('error', 'No restaurant associated with your account.');
        }

        // Get current settings
        $settings = RestaurantSetting::getAllForHotel($hotel->id);
        
        // Define available roles and modules
        $availableRoles = [
            'admin' => 'Administrator - Full access to all features',
            'manager' => 'Manager - Can manage staff, view reports, and handle operations',
            'frontdesk' => 'Front Desk - Can record visits, manage members, and handle basic operations',
            'cashier' => 'Cashier - Can process payments and handle transactions',
        ];

        $availableModules = [
            'cashier_module' => 'Cashier Module - Payment processing and transaction management',
            'receipt_required' => 'Receipt Required - Require receipt upload during checkout',
            'physical_cards' => 'Physical Cards - Track physical card issuance and delivery',
            'virtual_cards' => 'Virtual Cards - Generate and manage virtual membership cards',
            'points_system' => 'Points System - Member points and rewards system',
            'dining_management' => 'Dining Management - Record and track dining visits',
            'reports_module' => 'Reports Module - Generate and view reports',
            'user_management' => 'User Management - Manage staff accounts',
        ];

        return view('restaurant.settings', compact('settings', 'availableRoles', 'availableModules', 'hotel'));
    }

    /**
     * Update restaurant settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $hotel = $user->hotel;
        
        if (!$hotel) {
            return back()->with('error', 'No restaurant associated with your account.');
        }

        // Validate request
        $request->validate([
            'enabled_roles' => 'array',
            'enabled_roles.*' => 'string|in:admin,manager,frontdesk,cashier',
            'enabled_modules' => 'array',
            'enabled_modules.*' => 'string',
        ]);

        try {
            // Update enabled roles
            $enabledRoles = $request->input('enabled_roles', []);
            $hotel->setSetting('enabled_roles', $enabledRoles, 'json', 'Enabled user roles for this restaurant');

            // Update enabled modules
            $enabledModules = $request->input('enabled_modules', []);
            $hotel->setSetting('enabled_modules', $enabledModules, 'json', 'Enabled modules for this restaurant');

            // Update individual module settings
            $hotel->setSetting('receipt_required', $request->has('receipt_required'), 'boolean', 'Require receipt upload during checkout');
            $hotel->setSetting('physical_cards_enabled', $request->has('physical_cards_enabled'), 'boolean', 'Enable physical card tracking');
            $hotel->setSetting('virtual_cards_enabled', $request->has('virtual_cards_enabled'), 'boolean', 'Enable virtual card generation');
            $hotel->setSetting('points_system_enabled', $request->has('points_system_enabled'), 'boolean', 'Enable points system');
            $hotel->setSetting('dining_management_enabled', $request->has('dining_management_enabled'), 'boolean', 'Enable dining management');
            $hotel->setSetting('reports_enabled', $request->has('reports_enabled'), 'boolean', 'Enable reports module');
            $hotel->setSetting('user_management_enabled', $request->has('user_management_enabled'), 'boolean', 'Enable user management');

            return back()->with('success', 'Restaurant settings updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Get settings for API/JavaScript
     */
    public function getSettings()
    {
        $user = Auth::user();
        $hotel = $user->hotel;
        
        if (!$hotel) {
            return response()->json(['error' => 'No restaurant associated with your account.'], 400);
        }

        $settings = RestaurantSetting::getAllForHotel($hotel->id);
        
        return response()->json($settings);
    }
}
