<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle hotel registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'hotel_email' => 'required|string|email|max:255|unique:hotels,email',
            'hotel_phone' => 'nullable|string|max:20',
            'hotel_address' => 'nullable|string|max:500',
            'hotel_city' => 'nullable|string|max:100',
            'hotel_country' => 'nullable|string|max:100',
            'hotel_website' => 'nullable|url|max:255',
            'hotel_description' => 'nullable|string|max:1000',
            'hotel_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hotel_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Admin user details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::defaults()],
            
            'terms' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            // Create hotel
            $hotel = Hotel::create([
                'name' => $request->hotel_name,
                'email' => $request->hotel_email,
                'phone' => $request->hotel_phone,
                'address' => $request->hotel_address,
                'city' => $request->hotel_city,
                'country' => $request->hotel_country ?? 'Tanzania',
                'website' => $request->hotel_website,
                'description' => $request->hotel_description,
                'is_active' => true,
            ]);

            // Handle logo upload
            if ($request->hasFile('hotel_logo')) {
                $logoPath = $request->file('hotel_logo')->store('hotels/logos', 'public');
                $hotel->update(['logo_path' => $logoPath]);
            }

            // Handle banner upload
            if ($request->hasFile('hotel_banner')) {
                $bannerPath = $request->file('hotel_banner')->store('hotels/banners', 'public');
                $hotel->update(['banner_path' => $bannerPath]);
            }

            // Create admin user
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'hotel_id' => $hotel->id,
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Create default membership types for the hotel
            $this->createDefaultMembershipTypes($hotel);

            DB::commit();

            // Log in the user
            auth()->login($user);

            return redirect()->route('dashboard')->with('success', 'Hotel registered successfully! Welcome to Membership MS.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Create default membership types for new hotels
     */
    private function createDefaultMembershipTypes(Hotel $hotel)
    {
        $defaultTypes = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for occasional diners who want to enjoy some benefits',
                'price' => 50000,
                'billing_cycle' => 'yearly',
                'perks' => ['5% discount on all meals', 'Free dessert on birthday', 'Monthly newsletter'],
                'max_visits_per_month' => 10,
                'discount_rate' => 5.0,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Premium',
                'description' => 'Great value for regular diners with enhanced benefits',
                'price' => 100000,
                'billing_cycle' => 'yearly',
                'perks' => ['10% discount on all meals', 'Free appetizer with main course', 'Priority reservations', 'Quarterly special events'],
                'max_visits_per_month' => 20,
                'discount_rate' => 10.0,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'VIP',
                'description' => 'Ultimate dining experience with exclusive benefits',
                'price' => 200000,
                'billing_cycle' => 'yearly',
                'perks' => ['15% discount on all meals', 'Free bottle of wine monthly', 'Exclusive VIP events', 'Personal concierge service', 'Complimentary valet parking'],
                'max_visits_per_month' => null, // Unlimited
                'discount_rate' => 15.0,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($defaultTypes as $type) {
            $hotel->membershipTypes()->create($type);
        }
    }
} 