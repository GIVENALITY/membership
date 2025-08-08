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
        $membershipTypes = MembershipType::ordered()->get();
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

            MembershipType::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'perks' => json_encode($perks),
                'max_visits_per_month' => $request->max_visits_per_month,
                'discount_rate' => $request->discount_rate,
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
        $membershipType->load('members');
        return view('membership-types.show', compact('membershipType'));
    }

    /**
     * Show the form for editing the specified membership type
     */
    public function edit(MembershipType $membershipType)
    {
        return view('membership-types.edit', compact('membershipType'));
    }

    /**
     * Update the specified membership type
     */
    public function update(Request $request, MembershipType $membershipType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:membership_types,name,' . $membershipType->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'perks' => 'required|array|min:1',
            'perks.*' => 'required|string|max:255',
            'max_visits_per_month' => 'nullable|integer|min:1',
            'discount_rate' => 'required|numeric|min:0|max:100',
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

            $membershipType->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'perks' => json_encode($perks),
                'max_visits_per_month' => $request->max_visits_per_month,
                'discount_rate' => $request->discount_rate,
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