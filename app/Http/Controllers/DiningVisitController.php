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
            return response()->json([
                'success' => false,
                'message' => 'User not associated with a hotel.'
            ]);
        }

        try {
            $request->validate([
                'member_id' => 'required|exists:members,id',
                'number_of_people' => 'required|integer|min:1|max:50',
                'amount_spent' => 'required|numeric|min:0',
                'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'checkout_notes' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ]);
        }

        // Verify member belongs to this hotel
        $member = Member::where('id', $request->member_id)
            ->where('hotel_id', $user->hotel_id)
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid member selected.'
            ]);
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
                'amount_spent' => 0,
                'discount_amount' => 0,
                'final_amount' => 0,
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

        // Check if receipt is required based on restaurant settings
        $receiptRequired = $user->hotel->getSetting('receipt_required', false);
        
        $validationRules = [
            'amount_spent' => 'required|numeric|min:0',
            'checkout_notes' => 'nullable|string|max:500',
        ];
        
        if ($receiptRequired) {
            $validationRules['receipt'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $validationRules['receipt'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        
        $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Handle receipt upload
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
            }

            // Get member and validate spending requirements
            $member = $visit->member;
            $perPersonSpending = $request->amount_spent / $visit->number_of_people;
            $currentVisitCount = $member->total_visits + 1; // Include this visit

            // Validate minimum spending per person (50k minimum)
            $minSpendingPerPerson = 50000;
            $spendingQualified = $perPersonSpending >= $minSpendingPerPerson;

            // Calculate discount based on current visit count (including this visit)
            $discountPercentage = 0;
            $discountReason = '';

            if ($spendingQualified) {
                // Get base discount for current visit count
                $baseDiscount = $member->membershipType ? 
                    $member->membershipType->calculateDiscountForVisits($currentVisitCount) : 
                    5.0;

                // Check if member qualifies for points-based discount
                $currentPoints = $member->current_points_balance;
                $pointsQualified = $currentPoints >= 5;

                if ($pointsQualified) {
                    $discountPercentage = max($baseDiscount, 10.0); // Minimum 10% for qualified members
                    $discountReason = "Points qualification ({$currentPoints} points) + Visit progression ({$currentVisitCount} visits)";
                } else {
                    $discountPercentage = $baseDiscount;
                    $discountReason = "Visit progression ({$currentVisitCount} visits)";
                }

                // Check for special bonuses (birthday, consecutive visits)
                $specialDiscount = $member->getSpecialDiscountPercentage($currentVisitCount);
                if ($specialDiscount > $discountPercentage) {
                    $discountPercentage = $specialDiscount;
                    $discountReason = "Special bonus applied";
                }
            } else {
                $discountReason = "Minimum spending not met (TZS " . number_format($minSpendingPerPerson) . " per person required)";
            }

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

            // Add points for this visit (only if spending qualified)
            $pointRecord = null;
            if ($spendingQualified) {
                $pointRecord = $member->addPoints($request->amount_spent, $visit->number_of_people, $visit->id);
            }

            // Update member statistics
            $member->increment('total_visits');
            $member->increment('total_spent', $request->amount_spent);
            $member->update([
                'last_visit_at' => now(),
                'last_visit_date' => now()->toDateString(),
                'current_discount_rate' => $member->calculateDiscountRate(),
            ]);

            // Prepare success message
            $message = "Checkout completed for {$member->full_name}. ";
            $message .= "Amount: TZS " . number_format($request->amount_spent) . " ";
            
            if ($discountPercentage > 0) {
                $message .= "Discount: TZS " . number_format($discountAmount) . " ({$discountPercentage}%) ";
                $message .= "Final: TZS " . number_format($finalAmount) . ". ";
                $message .= "Reason: {$discountReason}. ";
            } else {
                $message .= "No discount applied. {$discountReason}. ";
            }
            
            if ($pointRecord && $pointRecord->points_earned > 0) {
                $message .= "Earned {$pointRecord->points_earned} points. ";
            }
            
            if ($pointRecord && $pointRecord->is_birthday_visit) {
                $message .= "🎂 Birthday visit - special treatment applied! ";
            }

            if (!$spendingQualified) {
                $message .= "Note: Minimum TZS " . number_format($minSpendingPerPerson) . " per person required for points and discounts.";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log detailed error information
            \Log::error('Checkout error details', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'visit_id' => $visit->id,
                'member_id' => $visit->member_id,
                'amount_spent' => $request->amount_spent ?? 'not provided'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to checkout: ' . $e->getMessage(),
                'debug_info' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'error' => $e->getMessage()
                ]
            ]);
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