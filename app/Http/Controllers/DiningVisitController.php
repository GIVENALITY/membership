<?php

namespace App\Http\Controllers;

use App\Models\DiningVisit;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DiningVisitController extends Controller
{
    /**
     * Show the dining visits page with active visits
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        // Get active visits (not checked out yet)
        $activeVisits = DiningVisit::with(['member'])
            ->where('hotel_id', $user->hotel_id)
            ->where('is_checked_out', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed visits (checked out)
        $completedVisits = DiningVisit::with(['member'])
            ->where('hotel_id', $user->hotel_id)
            ->where('is_checked_out', true)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('dining.index', compact('activeVisits', 'completedVisits'));
    }

    /**
     * Search members for AJAX selection
     */
    public function searchMembers(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return response()->json(['error' => 'User not associated with a hotel.'], 400);
        }

        $query = $request->get('q');
        
        $members = Member::where('hotel_id', $user->hotel_id)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('membership_id', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get([
                'id', 'first_name', 'last_name', 'membership_id', 'phone', 'email', 
                'current_discount_rate', 'allergies', 'dietary_preferences', 'special_requests', 
                'additional_notes', 'emergency_contact_name', 'emergency_contact_phone', 
                'emergency_contact_relationship'
            ]);

        return response()->json($members);
    }

    /**
     * Record a new visit (step 1)
     */
    public function recordVisit(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'number_of_people' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify member belongs to this hotel
        $member = Member::where('id', $request->member_id)
            ->where('hotel_id', $user->hotel_id)
            ->first();

        if (!$member) {
            return back()->withErrors(['member_id' => 'Invalid member selected.']);
        }

        try {
            $visit = DiningVisit::create([
                'hotel_id' => $user->hotel_id,
                'member_id' => $request->member_id,
                'number_of_people' => $request->number_of_people,
                'notes' => $request->notes,
                'is_checked_out' => false,
                'recorded_by' => $user->id,
            ]);

            return back()->with('success', "Visit recorded for {$member->full_name} ({$request->number_of_people} people). Ready for checkout when they finish dining.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to record visit: ' . $e->getMessage()]);
        }
    }

    /**
     * Checkout a visit (step 2)
     */
    public function checkout(Request $request, DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        // Verify visit belongs to this hotel and is not already checked out
        if ($visit->hotel_id !== $user->hotel_id || $visit->is_checked_out) {
            return back()->withErrors(['error' => 'Invalid visit or already checked out.']);
        }

        $request->validate([
            'amount_spent' => 'required|numeric|min:0',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'checkout_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Handle receipt upload
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
            }

            // Calculate discount
            $discountAmount = ($request->amount_spent * $visit->member->current_discount_rate) / 100;
            $finalAmount = $request->amount_spent - $discountAmount;

            // Update visit with checkout information
            $visit->update([
                'amount_spent' => $request->amount_spent,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'receipt_path' => $receiptPath,
                'checkout_notes' => $request->checkout_notes,
                'is_checked_out' => true,
                'checked_out_at' => now(),
                'checked_out_by' => $user->id,
            ]);

            // Update member statistics
            $member = $visit->member;
            $member->increment('total_visits');
            $member->increment('total_spent', $request->amount_spent);
            $member->update([
                'last_visit_at' => now(),
                'current_discount_rate' => $member->calculateDiscountRate(),
            ]);

            DB::commit();

            return back()->with('success', "Checkout completed for {$member->full_name}. Amount: TZS " . number_format($request->amount_spent) . ", Discount: TZS " . number_format($discountAmount) . ", Final: TZS " . number_format($finalAmount));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to checkout: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel an active visit
     */
    public function cancelVisit(DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        if ($visit->hotel_id !== $user->hotel_id || $visit->is_checked_out) {
            return back()->withErrors(['error' => 'Invalid visit or already checked out.']);
        }

        try {
            $memberName = $visit->member->full_name;
            $visit->delete();
            return back()->with('success', "Visit cancelled for {$memberName}.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel visit: ' . $e->getMessage()]);
        }
    }

    /**
     * View visit details
     */
    public function show(DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $visit->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        return view('dining.show', compact('visit'));
    }
} 