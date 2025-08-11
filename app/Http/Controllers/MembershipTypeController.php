<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MembershipTypeController extends Controller
{
    /**
     * Display a listing of membership types
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $membershipTypes = MembershipType::where('hotel_id', $user->hotel_id)
            ->ordered()
            ->get();
        return view('membership-types.index', compact('membershipTypes'));
    }

    /**
     * Show the form for creating a new membership type
     */
    public function create()
    {
        return view('membership-types.create');
    }

    /**
     * Store a newly created membership type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:membership_types,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'perks' => 'required|array|min:1',
            'perks.*' => 'required|string|max:255',
            'max_visits_per_month' => 'nullable|integer|min:1',
            'discount_rate' => 'required|numeric|min:0|max:100',
            'points_required_for_discount' => 'required|integer|min:1',
            'has_special_birthday_discount' => 'nullable|boolean',
            'birthday_discount_rate' => 'required|numeric|min:0|max:100',
            'has_consecutive_visit_bonus' => 'nullable|boolean',
            'consecutive_visits_for_bonus' => 'required|integer|min:1',
            'consecutive_visit_bonus_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = auth()->user();
            if (!$user || !$user->hotel_id) {
                return back()->withErrors(['error' => 'User not associated with a hotel.']);
            }

            $perks = array_filter($request->perks); // Remove empty perks

            // Build discount progression array
            $discountProgression = [];
            if ($request->has('progression_visits') && $request->has('progression_discounts')) {
                $visits = $request->progression_visits;
                $discounts = $request->progression_discounts;
                
                for ($i = 0; $i < count($visits); $i++) {
                    if (!empty($visits[$i]) && !empty($discounts[$i])) {
                        $discountProgression[] = [
                            'visits' => (int)$visits[$i],
                            'discount' => (float)$discounts[$i]
                        ];
                    }
                }
            }

            MembershipType::create([
                'hotel_id' => $user->hotel_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'perks' => $perks,
                'max_visits_per_month' => $request->max_visits_per_month,
                'discount_rate' => $request->discount_rate,
                'discount_progression' => $discountProgression,
                'points_required_for_discount' => $request->points_required_for_discount,
                'has_special_birthday_discount' => $request->boolean('has_special_birthday_discount'),
                'birthday_discount_rate' => $request->birthday_discount_rate,
                'has_consecutive_visit_bonus' => $request->boolean('has_consecutive_visit_bonus'),
                'consecutive_visits_for_bonus' => $request->consecutive_visits_for_bonus,
                'consecutive_visit_bonus_rate' => $request->consecutive_visit_bonus_rate,
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return redirect()->route('membership-types.index')
                ->with('success', 'Membership type created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating membership type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified membership type
     */
    public function show(MembershipType $membershipType)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $membershipType->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $membershipType->load('members');
        return view('membership-types.show', compact('membershipType'));
    }

    /**
     * Show the form for editing the specified membership type
     */
    public function edit(MembershipType $membershipType)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $membershipType->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        return view('membership-types.edit', compact('membershipType'));
    }

    /**
     * Update the specified membership type
     */
    public function update(Request $request, MembershipType $membershipType)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $membershipType->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:membership_types,name,' . $membershipType->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'perks' => 'required|array|min:1',
            'perks.*' => 'required|string|max:255',
            'max_visits_per_month' => 'nullable|integer|min:1',
            'discount_rate' => 'required|numeric|min:0|max:100',
            'points_required_for_discount' => 'required|integer|min:1',
            'has_special_birthday_discount' => 'nullable|boolean',
            'birthday_discount_rate' => 'required|numeric|min:0|max:100',
            'has_consecutive_visit_bonus' => 'nullable|boolean',
            'consecutive_visits_for_bonus' => 'required|integer|min:1',
            'consecutive_visit_bonus_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $perks = array_filter($request->perks); // Remove empty perks

            // Build discount progression array
            $discountProgression = [];
            if ($request->has('progression_visits') && $request->has('progression_discounts')) {
                $visits = $request->progression_visits;
                $discounts = $request->progression_discounts;
                
                for ($i = 0; $i < count($visits); $i++) {
                    if (!empty($visits[$i]) && !empty($discounts[$i])) {
                        $discountProgression[] = [
                            'visits' => (int)$visits[$i],
                            'discount' => (float)$discounts[$i]
                        ];
                    }
                }
            }

            $membershipType->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'perks' => $perks,
                'max_visits_per_month' => $request->max_visits_per_month,
                'discount_rate' => $request->discount_rate,
                'discount_progression' => $discountProgression,
                'points_required_for_discount' => $request->points_required_for_discount,
                'has_special_birthday_discount' => $request->boolean('has_special_birthday_discount'),
                'birthday_discount_rate' => $request->birthday_discount_rate,
                'has_consecutive_visit_bonus' => $request->boolean('has_consecutive_visit_bonus'),
                'consecutive_visits_for_bonus' => $request->consecutive_visits_for_bonus,
                'consecutive_visit_bonus_rate' => $request->consecutive_visit_bonus_rate,
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return redirect()->route('membership-types.index')
                ->with('success', 'Membership type updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating membership type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified membership type
     */
    public function destroy(MembershipType $membershipType)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $membershipType->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        try {
            // Check if any members are using this type
            if ($membershipType->members()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete membership type. It is being used by ' . $membershipType->members()->count() . ' member(s).');
            }

            $membershipType->delete();
            return redirect()->route('membership-types.index')
                ->with('success', 'Membership type deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting membership type: ' . $e->getMessage());
        }
    }
} 