<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\DiningVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WaiterCheckoutController extends Controller
{
    /**
     * Show waiter checkout dashboard
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        // Get active visits for this waiter
        $activeVisits = DiningVisit::with(['member', 'member.membershipType'])
            ->where('hotel_id', $user->hotel_id)
            ->where('waiter_id', $user->id)
            ->where('checkout_status', 'checked_in')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get completed checkouts for this waiter
        $completedCheckouts = DiningVisit::with(['member', 'member.membershipType'])
            ->where('hotel_id', $user->hotel_id)
            ->where('waiter_id', $user->id)
            ->where('checkout_status', 'checked_out')
            ->orderBy('checked_out_at', 'desc')
            ->limit(10)
            ->get();

        return view('waiter.checkout.index', compact('activeVisits', 'completedCheckouts'));
    }

    /**
     * Show checkout form for a specific visit
     */
    public function showCheckout(DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $visit->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        // Check if this waiter is assigned to this visit
        if ($visit->waiter_id !== $user->id) {
            return back()->withErrors(['error' => 'You are not assigned to this visit.']);
        }

        return view('waiter.checkout.show', compact('visit'));
    }

    /**
     * Process checkout with payment and receipt
     */
    public function processCheckout(Request $request, DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $visit->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        // Check if this waiter is assigned to this visit
        if ($visit->waiter_id !== $user->id) {
            return back()->withErrors(['error' => 'You are not assigned to this visit.']);
        }

        $request->validate([
            'amount_spent' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'waiter_notes' => 'nullable|string|max:1000',
            'checkout_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Handle receipt upload
            $receiptPath = $request->file('receipt')->store('receipts', 'public');

            // Calculate discount based on member's current discount rate
            $member = $visit->member;
            $discountPercentage = $member->current_discount_rate ?? 0;
            $discountAmount = ($request->amount_spent * $discountPercentage) / 100;
            $finalAmount = $request->amount_spent - $discountAmount;

            // Update the dining visit with checkout information
            $visit->update([
                'amount_spent' => $request->amount_spent,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'receipt_path' => $receiptPath,
                'receipt_notes' => $request->checkout_notes,
                'receipt_uploaded_by' => $user->id,
                'receipt_uploaded_at' => now(),
                'checkout_status' => 'checked_out',
                'checkout_notes' => $request->checkout_notes,
                'checked_out_by' => $user->id,
                'checked_out_at' => now(),
                'waiter_notes' => $request->waiter_notes,
                'waiter_checkout_at' => now(),
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => "Processed by waiter: {$user->name}",
            ]);

            // Update member statistics
            $member->increment('total_visits');
            $member->increment('total_spent', $request->amount_spent);
            $member->update([
                'last_visit_at' => now(),
                'last_visit_date' => now()->toDateString(),
            ]);

            // Add points for this visit if member qualifies
            if ($member->qualifies_for_discount) {
                // Calculate points based on spending
                $pointsEarned = floor($request->amount_spent / 1000); // 1 point per 1000 TZS
                if ($pointsEarned > 0) {
                    $member->increment('total_points_earned', $pointsEarned);
                    $member->increment('current_points_balance', $pointsEarned);
                }
            }

            DB::commit();

            return redirect()->route('waiter.checkout.index')
                ->with('success', "Checkout completed successfully for {$member->full_name}. Final amount: TZS " . number_format($finalAmount));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error processing checkout: ' . $e->getMessage()]);
        }
    }

    /**
     * Search for members to assign to waiter
     */
    public function searchMembers(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return response()->json(['success' => false, 'message' => 'User not associated with a hotel.']);
        }

        $search = $request->get('search');
        
        $members = Member::where('hotel_id', $user->hotel_id)
            ->where('status', 'active')
            ->where(function($query) use ($search) {
                $query->where('membership_id', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
            })
            ->with(['membershipType'])
            ->limit(10)
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'membership_id' => $member->membership_id,
                    'phone' => $member->phone,
                    'membership_type' => optional($member->membershipType)->name ?? 'N/A',
                    'current_discount_rate' => $member->current_discount_rate ?? 0,
                ];
            });

        return response()->json(['success' => true, 'members' => $members]);
    }

    /**
     * Assign a member to waiter for dining service
     */
    public function assignMember(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'number_of_people' => 'required|integer|min:1|max:50',
            'waiter_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Check if member is already assigned to another waiter today
            $existingVisit = DiningVisit::where('hotel_id', $user->hotel_id)
                ->where('member_id', $request->member_id)
                ->whereDate('created_at', today())
                ->where('checkout_status', 'checked_in')
                ->first();

            if ($existingVisit) {
                return back()->withErrors(['error' => 'Member is already assigned to another waiter.']);
            }

            // Create new dining visit
            $visit = DiningVisit::create([
                'hotel_id' => $user->hotel_id,
                'member_id' => $request->member_id,
                'waiter_id' => $user->id,
                'waiter_notes' => $request->waiter_notes,
                'number_of_people' => $request->number_of_people,
                'checkout_status' => 'checked_in',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('waiter.checkout.index')
                ->with('success', 'Member assigned successfully. You can now process their checkout.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error assigning member: ' . $e->getMessage()]);
        }
    }

    /**
     * Download receipt
     */
    public function downloadReceipt(DiningVisit $visit)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $visit->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        if (!$visit->receipt_path) {
            return back()->withErrors(['error' => 'No receipt found.']);
        }

        $path = storage_path('app/public/' . $visit->receipt_path);
        
        if (!file_exists($path)) {
            return back()->withErrors(['error' => 'Receipt file not found.']);
        }

        return response()->download($path);
    }
}
