<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\DiningVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    /**
     * Show the cashier dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get today's present members (checked in but not checked out)
        $presentMembers = DiningVisit::where('hotel_id', $user->hotel_id)
            ->whereDate('created_at', Carbon::today())
            ->where('is_checked_out', false)
            ->with(['member', 'member.membershipType'])
            ->get();

        // Get today's birthday members
        $todayBirthdays = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") = ?', [Carbon::today()->format('m-d')])
            ->where('status', 'active')
            ->get();

        // Get upcoming birthdays (next 7 days)
        $upcomingBirthdays = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") BETWEEN ? AND ?', [
                Carbon::today()->format('m-d'),
                Carbon::today()->addDays(7)->format('m-d')
            ])
            ->where('status', 'active')
            ->get();

        return view('cashier.index', compact('presentMembers', 'todayBirthdays', 'upcomingBirthdays'));
    }

    /**
     * Lookup member by ID or phone
     */
    public function lookupMember(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->input('search');
        
        $member = Member::where('hotel_id', $user->hotel_id)
            ->where('status', 'active')
            ->where(function($query) use ($searchTerm) {
                $query->where('membership_id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
            })
            ->with(['membershipType'])
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ]);
        }

        // Check if member is already present today
        $isPresent = DiningVisit::where('hotel_id', $user->hotel_id)
            ->where('member_id', $member->id)
            ->whereDate('created_at', Carbon::today())
            ->where('is_checked_out', false)
            ->exists();

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->full_name,
                'membership_id' => $member->membership_id,
                'email' => $member->email,
                'phone' => $member->phone,
                'total_visits' => $member->total_visits,
                'current_discount_rate' => $member->current_discount_rate,
                'current_points_balance' => $member->current_points_balance,
                'qualifies_for_discount' => $member->qualifies_for_discount,
                'membership_type' => optional($member->membershipType)->name,
                'is_present' => $isPresent
            ]
        ]);
    }

    /**
     * Process payment and checkout
     */
    public function processPayment(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount_spent' => 'required|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'receipt' => auth()->user()->hotel->getSetting('receipt_required', false) ? 'required|image|mimes:jpeg,png,jpg,gif|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $member = Member::where('hotel_id', $user->hotel_id)
            ->where('id', $request->member_id)
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ]);
        }

        // Find the dining visit to checkout
        $diningVisit = DiningVisit::where('hotel_id', $user->hotel_id)
            ->where('member_id', $member->id)
            ->whereDate('created_at', Carbon::today())
            ->where('is_checked_out', false)
            ->first();

        if (!$diningVisit) {
            return response()->json([
                'success' => false,
                'message' => 'No active dining visit found for this member'
            ]);
        }

        // Handle receipt upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        // Update dining visit with checkout information
        $diningVisit->update([
            'amount_spent' => $request->amount_spent,
            'final_amount' => $request->final_amount,
            'discount_amount' => $request->amount_spent - $request->final_amount,
            'is_checked_out' => true,
            'checkout_notes' => $request->checkout_notes ?? 'Processed via cashier',
            'receipt_path' => $receiptPath,
            'checked_out_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully! Member has been checked out.',
            'visit_id' => $diningVisit->id
        ]);
    }
}
