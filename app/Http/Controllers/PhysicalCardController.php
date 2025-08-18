<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhysicalCardController extends Controller
{
    /**
     * Show the physical cards management page
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);
        $statusFilter = $request->input('status', '');
        
        // Get hotels for filter (super admin can see all, others see only their hotel)
        $hotels = $user->role === 'super_admin' 
            ? Hotel::orderBy('name')->get()
            : collect([$user->hotel]);

        // Build query for members
        $query = Member::with(['hotel', 'membershipType'])
            ->when($hotelId, function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })
            ->when($user->role !== 'super_admin', function($q) use ($user) {
                $q->where('hotel_id', $user->hotel_id);
            })
            ->when($statusFilter, function($q) use ($statusFilter) {
                $q->where('physical_card_status', $statusFilter);
            });

        // Get statistics
        $totalMembers = $query->count();
        $notIssued = $query->where('physical_card_status', 'not_issued')->count();
        $issued = $query->where('physical_card_status', 'issued')->count();
        $delivered = $query->where('physical_card_status', 'delivered')->count();
        $lost = $query->where('physical_card_status', 'lost')->count();
        $replaced = $query->where('physical_card_status', 'replaced')->count();

        // Get paginated members for display
        $members = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('members.physical-cards.index', compact(
            'members', 
            'hotels', 
            'hotelId', 
            'statusFilter',
            'totalMembers', 
            'notIssued', 
            'issued', 
            'delivered', 
            'lost', 
            'replaced'
        ));
    }

    /**
     * Show the form to issue a physical card
     */
    public function issueForm(Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to issue cards for this member.');
        }

        return view('members.physical-cards.issue', compact('member'));
    }

    /**
     * Issue a physical card
     */
    public function issue(Request $request, Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to issue cards for this member.');
        }

        $request->validate([
            'status' => 'required|in:issued,delivered',
            'notes' => 'nullable|string|max:500',
            'delivered_by' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $member->update([
                'physical_card_status' => $request->status,
                'physical_card_issued_date' => now(),
                'physical_card_issued_by' => $user->name,
                'physical_card_notes' => $request->notes,
                'physical_card_delivered_date' => $request->status === 'delivered' ? now() : null,
                'physical_card_delivered_by' => $request->status === 'delivered' ? ($request->delivered_by ?: $user->name) : null,
            ]);

            Log::info('Physical card issued', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'status' => $request->status,
                'issued_by' => $user->id,
                'notes' => $request->notes
            ]);

            DB::commit();

            $statusText = $request->status === 'delivered' ? 'issued and delivered' : 'issued';
            return redirect()->route('members.physical-cards.index')->with('success', "Physical card {$statusText} for {$member->full_name}");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to issue physical card', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to issue physical card: ' . $e->getMessage());
        }
    }

    /**
     * Update physical card status
     */
    public function updateStatus(Request $request, Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to update this member\'s card status.');
        }

        $request->validate([
            'status' => 'required|in:not_issued,issued,delivered,lost,replaced',
            'notes' => 'nullable|string|max:500',
            'delivered_by' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'physical_card_status' => $request->status,
                'physical_card_notes' => $request->notes,
            ];

            // Handle specific status updates
            if ($request->status === 'issued' && $member->physical_card_status === 'not_issued') {
                $updateData['physical_card_issued_date'] = now();
                $updateData['physical_card_issued_by'] = $user->name;
            }

            if ($request->status === 'delivered' && $member->physical_card_status !== 'delivered') {
                $updateData['physical_card_delivered_date'] = now();
                $updateData['physical_card_delivered_by'] = $request->delivered_by ?: $user->name;
            }

            if ($request->status === 'replaced') {
                $updateData['physical_card_issued_date'] = now();
                $updateData['physical_card_issued_by'] = $user->name;
                $updateData['physical_card_delivered_date'] = null;
                $updateData['physical_card_delivered_by'] = null;
            }

            $member->update($updateData);

            Log::info('Physical card status updated', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'old_status' => $member->getOriginal('physical_card_status'),
                'new_status' => $request->status,
                'updated_by' => $user->id,
                'notes' => $request->notes
            ]);

            DB::commit();

            return back()->with('success', "Physical card status updated to '{$request->status}' for {$member->full_name}");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update physical card status', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to update physical card status: ' . $e->getMessage());
        }
    }

    /**
     * Mass issue physical cards
     */
    public function massIssue(Request $request)
    {
        $request->validate([
            'hotel_id' => 'nullable|exists:hotels,id',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'status' => 'required|in:issued,delivered',
            'notes' => 'nullable|string|max:500',
            'delivered_by' => 'nullable|string|max:255'
        ]);

        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);

        try {
            DB::beginTransaction();

            $membersToProcess = Member::whereIn('id', $request->member_ids)
                ->when($hotelId, function($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                })
                ->when($user->role !== 'super_admin', function($q) use ($user) {
                    $q->where('hotel_id', $user->hotel_id);
                })
                ->get();

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($membersToProcess as $member) {
                try {
                    $updateData = [
                        'physical_card_status' => $request->status,
                        'physical_card_issued_date' => now(),
                        'physical_card_issued_by' => $user->name,
                        'physical_card_notes' => $request->notes,
                    ];

                    if ($request->status === 'delivered') {
                        $updateData['physical_card_delivered_date'] = now();
                        $updateData['physical_card_delivered_by'] = $request->delivered_by ?: $user->name;
                    }

                    $member->update($updateData);
                    $successCount++;

                    Log::info('Physical card issued in mass operation', [
                        'member_id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'status' => $request->status,
                        'issued_by' => $user->id
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Member {$member->full_name} ({$member->membership_id}): " . $e->getMessage();
                    
                    Log::error('Failed to issue physical card in mass operation', [
                        'member_id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = "Successfully issued {$successCount} physical cards.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} cards failed to issue.";
            }

            return back()->with([
                'success' => $message,
                'physical_card_errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Mass physical card issuance failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Mass physical card issuance failed: ' . $e->getMessage());
        }
    }

    /**
     * Get physical card statistics for AJAX
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);

        $query = Member::when($hotelId, function($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        })
        ->when($user->role !== 'super_admin', function($q) use ($user) {
            $q->where('hotel_id', $user->hotel_id);
        });

        $totalMembers = $query->count();
        $notIssued = $query->where('physical_card_status', 'not_issued')->count();
        $issued = $query->where('physical_card_status', 'issued')->count();
        $delivered = $query->where('physical_card_status', 'delivered')->count();
        $lost = $query->where('physical_card_status', 'lost')->count();
        $replaced = $query->where('physical_card_status', 'replaced')->count();

        return response()->json([
            'total' => $totalMembers,
            'not_issued' => $notIssued,
            'issued' => $issued,
            'delivered' => $delivered,
            'lost' => $lost,
            'replaced' => $replaced,
            'issued_percentage' => $totalMembers > 0 ? round((($issued + $delivered + $replaced) / $totalMembers) * 100, 1) : 0,
            'delivered_percentage' => $totalMembers > 0 ? round((($delivered + $replaced) / $totalMembers) * 100, 1) : 0
        ]);
    }
}
