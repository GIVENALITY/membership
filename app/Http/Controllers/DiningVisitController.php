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
     * Search members for dining check-in
     */
    public function searchMembers(Request $request)
    {
        $search = $request->get('search');
        $user = auth()->user();
        
        if (!$search || strlen($search) < 2) {
            return response()->json(['members' => []]);
        }

        $members = Member::where('hotel_id', $user->hotel_id)
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('membership_id', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('membershipType')
            ->limit(10)
            ->get()
            ->map(function($member) {
                // Add preference indicators
                $indicators = [];
                if ($member->allergies) $indicators[] = '<span class="badge bg-danger badge-sm">⚠️ Allergies</span>';
                if ($member->dietary_preferences) $indicators[] = '<span class="badge bg-info badge-sm">🍽️ Dietary</span>';
                if ($member->special_requests) $indicators[] = '<span class="badge bg-warning badge-sm">🎯 Special</span>';
                if ($member->isBirthdayVisit()) $indicators[] = '<span class="badge bg-warning badge-sm">🎂 Birthday</span>';
                
                return [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'membership_id' => $member->membership_id,
                    'phone' => $member->phone,
                    'email' => $member->email,
                    'preferenceIndicators' => implode(' ', $indicators)
                ];
            });

        return response()->json(['members' => $members]);
    }

    /**
     * Get current active visits
     */
    public function currentVisits()
    {
        $user = auth()->user();
        
        $visits = DiningVisit::where('hotel_id', $user->hotel_id)
            ->where('is_checked_out', false)
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($visit) {
                return [
                    'id' => $visit->id,
                    'member' => [
                        'id' => $visit->member->id,
                        'name' => $visit->member->first_name . ' ' . $visit->member->last_name,
                        'membership_id' => $visit->member->membership_id
                    ],
                    'number_of_people' => $visit->number_of_people,
                    'notes' => $visit->notes,
                    'created_at' => $visit->created_at
                ];
            });

        return response()->json(['visits' => $visits]);
    }

    /**
     * Process payment via cashier (record visit and checkout in one step)
     */
    public function processPayment(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'number_of_people' => 'required|integer|min:1|max:50',
            'amount_spent' => 'required|numeric|min:0',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'checkout_notes' => 'nullable|string|max:500',
        ]);

        // Verify member belongs to this hotel
        $member = Member::where('id', $request->member_id)
            ->where('hotel_id', $user->hotel_id)
            ->first();

        if (!$member) {
            return back()->withErrors(['member_id' => 'Invalid member selected.']);
        }

        try {
            DB::beginTransaction();

            // Handle receipt upload
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
            }

            // Calculate discount based on points system
            $discountPercentage = $member->getSpecialDiscountPercentage();
            $discountAmount = ($request->amount_spent * $discountPercentage) / 100;
            $finalAmount = $request->amount_spent - $discountAmount;

            // Create visit with checkout information
            $visit = DiningVisit::create([
                'hotel_id' => $user->hotel_id,
                'member_id' => $request->member_id,
                'number_of_people' => $request->number_of_people,
                'amount_spent' => $request->amount_spent,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'receipt_path' => $receiptPath,
                'checkout_notes' => $request->checkout_notes,
                'is_checked_out' => true,
                'checked_out_at' => now(),
                'recorded_by' => $user->id,
                'checked_out_by' => $user->id,
            ]);

            // Add points for this visit
            $pointRecord = $member->addPoints($request->amount_spent, $request->number_of_people, $visit->id);

            // Update member statistics
            $member->increment('total_visits');
            $member->increment('total_spent', $request->amount_spent);
            $member->update([
                'last_visit_at' => now(),
                'last_visit_date' => now()->toDateString(),
                'current_discount_rate' => $member->calculateDiscountRate(),
            ]);

            // Prepare success message with points information
            $pointsMessage = '';
            if ($pointRecord->points_earned > 0) {
                $pointsMessage = " Earned {$pointRecord->points_earned} points.";
            }
            
            if ($pointRecord->is_birthday_visit) {
                $pointsMessage .= " 🎂 Birthday visit - special treatment applied!";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Payment processed for {$member->full_name}. Amount: TZS " . number_format($request->amount_spent) . ", Discount: TZS " . number_format($discountAmount) . " ({$discountPercentage}%), Final: TZS " . number_format($finalAmount) . "." . $pointsMessage
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record a visit (step 1)
     */
    public function recordVisit(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not associated with a hotel.'
                ], 400);
            }

            $validator = \Validator::make($request->all(), [
                'member_id' => 'required|exists:members,id',
                'number_of_people' => 'required|integer|min:1|max:50',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify member belongs to this hotel
            $member = Member::where('id', $request->member_id)
                ->where('hotel_id', $user->hotel_id)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid member selected.'
                ], 400);
            }

            // Create the dining visit
            $visitData = [
                'hotel_id' => $user->hotel_id,
                'member_id' => $request->member_id,
                'number_of_people' => $request->number_of_people,
                'notes' => $request->notes,
                'is_checked_out' => false,
                'recorded_by' => $user->id,
            ];

            $visit = DiningVisit::create($visitData);

            return response()->json([
                'success' => true,
                'message' => "Visit recorded for {$member->full_name} ({$request->number_of_people} people). Ready for checkout when they finish dining.",
                'visit_id' => $visit->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error recording visit: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record visit: ' . $e->getMessage()
            ], 500);
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

            // Calculate discount based on points system
            $member = $visit->member;
            $discountPercentage = $member->getSpecialDiscountPercentage();
            $discountAmount = ($request->amount_spent * $discountPercentage) / 100;
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

            // Add points for this visit
            $pointRecord = $member->addPoints($request->amount_spent, $visit->number_of_people, $visit->id);

            // Update member statistics
            $member->increment('total_visits');
            $member->increment('total_spent', $request->amount_spent);
            $member->update([
                'last_visit_at' => now(),
                'last_visit_date' => now()->toDateString(),
                'current_discount_rate' => $member->calculateDiscountRate(),
            ]);

            // Prepare success message with points information
            $pointsMessage = '';
            if ($pointRecord->points_earned > 0) {
                $pointsMessage = " Earned {$pointRecord->points_earned} points.";
            }
            
            if ($pointRecord->is_birthday_visit) {
                $pointsMessage .= " 🎂 Birthday visit - special treatment applied!";
            }

            DB::commit();

            return back()->with('success', "Checkout completed for {$member->full_name}. Amount: TZS " . number_format($request->amount_spent) . ", Discount: TZS " . number_format($discountAmount) . " ({$discountPercentage}%), Final: TZS " . number_format($finalAmount) . "." . $pointsMessage);

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