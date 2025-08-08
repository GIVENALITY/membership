<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\MemberCardGenerator;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index()
    {
        $members = Member::with('membershipType')->orderBy('created_at', 'desc')->get();
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        $membershipId = Member::generateMembershipId();
        return view('members.create', compact('membershipId'));
    }

    /**
     * Store a newly created member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'required|date',
            'membership_type_id' => 'required|exists:membership_types,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $membershipType = \App\Models\MembershipType::find($request->membership_type_id);

            $member = Member::create([
                'membership_id' => Member::generateMembershipId(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'join_date' => now()->toDateString(),
                'membership_type_id' => $request->membership_type_id,
                'status' => $request->status,
                'total_visits' => 0,
                'total_spent' => 0,
                'current_discount_rate' => $membershipType->discount_rate,
                // Expiry: match billing cycle of type
                'expires_at' => $membershipType->billing_cycle === 'monthly'
                    ? now()->addMonth()->toDateString()
                    : now()->addYear()->toDateString(),
            ]);

            // Generate and save the membership card image
            $generator = new MemberCardGenerator();
            $cardPath = $generator->generate($member->fresh(['membershipType']));
            $member->card_image_path = $cardPath;
            $member->save();

            return redirect()->route('members.index')
                ->with('success', 'Member created successfully! Membership ID: ' . $member->membership_id);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified member
     */
    public function show(Member $member)
    {
        $member->load(['diningVisits', 'presenceRecords']);
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'required|date',
            'membership_type' => 'required|in:basic,premium,vip',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $member->update($request->all());
            return redirect()->route('members.index')
                ->with('success', 'Member updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified member
     */
    public function destroy(Member $member)
    {
        try {
            $member->delete();
            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting member: ' . $e->getMessage());
        }
    }

    /**
     * Search for members
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $members = Member::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('membership_id', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($members);
    }
} 