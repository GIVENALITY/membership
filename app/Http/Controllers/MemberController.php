<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\MemberCardGenerator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMemberMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $members = Member::with('membershipType')
            ->where('hotel_id', $user->hotel_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $membershipId = Member::generateMembershipId();
        $membershipTypes = \App\Models\MembershipType::where('hotel_id', $user->hotel_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        return view('members.create', compact('membershipId', 'membershipTypes'));
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
            $user = auth()->user();
            if (!$user || !$user->hotel_id) {
                return back()->withErrors(['error' => 'User not associated with a hotel.']);
            }

            $membershipType = \App\Models\MembershipType::where('id', $request->membership_type_id)
                ->where('hotel_id', $user->hotel_id)
                ->first();
            
            if (!$membershipType) {
                return back()->withErrors(['membership_type_id' => 'Invalid membership type selected.']);
            }

            $member = Member::create([
                'hotel_id' => $user->hotel_id,
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

            // Build welcome email content from settings or fallback
            $subject = DB::table('system_settings')->where('key', 'welcome_email_subject')->value('value')
                ?? 'Welcome to Membership MS';
            $templateJson = DB::table('system_settings')->where('key', 'welcome_email_template')->value('value');
            $body = 'Dear ' . $member->full_name . ",\n\nWelcome to Membership MS!\n\nMembership ID: " . $member->membership_id;
            if ($templateJson) {
                $tpl = json_decode($templateJson, true);
                if (isset($tpl['body'])) {
                    $body = str_replace([
                        '[Member Name]', '[MS001]', '[Date]', '[5%]'
                    ], [
                        $member->full_name,
                        $member->membership_id,
                        now()->toDateString(),
                        rtrim(rtrim(number_format($member->current_discount_rate,2,'.',''), '0'),'.') . '%'
                    ], $tpl['body']);
                }
            }

            // Send email (sync). In production consider queueing.
            Mail::to($member->email)->send(new WelcomeMemberMail($member, $subject, $body));

            // Log to email_notifications if table exists
            if (Schema::hasTable('email_notifications')) {
                DB::table('email_notifications')->insert([
                    'member_id' => $member->id,
                    'email' => $member->email,
                    'subject' => $subject,
                    'message' => $body,
                    'type' => 'welcome',
                    'status' => 'sent',
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('members.index')
                ->with('success', 'Member created successfully and welcome email sent! Membership ID: ' . $member->membership_id);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating member: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        $member->load(['diningVisits', 'presenceRecords']);
        return view('members.show', compact('member'));
    }

    /**
     * Display the specified member as JSON.
     */
    public function showJson(Member $member)
    {
        $user = auth()->user();
        
        // Ensure the member belongs to the user's hotel
        if ($member->hotel_id !== $user->hotel_id) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $member->load('membershipType');
        
        return response()->json([
            'id' => $member->id,
            'name' => $member->first_name . ' ' . $member->last_name,
            'membership_id' => $member->membership_id,
            'phone' => $member->phone,
            'email' => $member->email,
            'allergies' => $member->allergies,
            'dietary_preferences' => $member->dietary_preferences,
            'special_requests' => $member->special_requests,
            'additional_notes' => $member->additional_notes,
            'emergency_contact' => $member->emergency_contact_name ? 
                $member->emergency_contact_name . ' (' . $member->emergency_contact_relationship . ') - ' . $member->emergency_contact_phone : null,
            'membership_type' => $member->membershipType ? [
                'name' => $member->membershipType->name,
                'discount_rate' => $member->membershipType->discount_rate
            ] : null,
            'current_points_balance' => $member->current_points_balance,
            'total_visits' => $member->total_visits,
            'is_birthday_visit' => $member->isBirthdayVisit(),
            'qualifies_for_discount' => $member->qualifies_for_discount
        ]);
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit(Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $membershipTypes = \App\Models\MembershipType::where('hotel_id', $user->hotel_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('members.edit', compact('member', 'membershipTypes'));
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
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
            // Validate that the membership type belongs to the same hotel
            $membershipType = \App\Models\MembershipType::where('id', $request->membership_type_id)
                ->where('hotel_id', $user->hotel_id)
                ->first();
            
            if (!$membershipType) {
                return back()->withErrors(['membership_type_id' => 'Invalid membership type selected.']);
            }

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
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

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
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $query = $request->get('query');
        
        $members = Member::where('hotel_id', $user->hotel_id)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('membership_id', 'LIKE', "%{$query}%");
            })
            ->with(['membershipType'])
            ->get();

        return response()->json($members);
    }
} 