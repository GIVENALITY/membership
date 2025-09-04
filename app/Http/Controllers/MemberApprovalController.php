<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MemberApprovalController extends Controller
{
    /**
     * Show pending members for approval
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        $search = $request->get('search');
        $status = $request->get('status', 'all');

        // Base query for all members
        $baseQuery = Member::with(['membershipType', 'approvedBy', 'paymentVerifiedBy', 'cardApprovedBy'])
            ->where('hotel_id', $user->hotel_id);

        // Apply search if provided
        if ($search) {
            $baseQuery->where(function($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('membership_id', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        $pendingMembers = clone $baseQuery;
        $approvedMembers = clone $baseQuery;
        $paymentVerifiedMembers = clone $baseQuery;

        if ($status === 'all' || $status === 'pending') {
            $pendingMembers = $pendingMembers->where('approval_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->paginate(20);
        } else {
            $pendingMembers = collect([]);
        }

        if ($status === 'all' || $status === 'approved') {
            $approvedMembers = $approvedMembers->where('approval_status', 'approved')
                ->where('payment_status', 'pending')
                ->orderBy('approved_at', 'desc')
                ->paginate(20);
        } else {
            $approvedMembers = collect([]);
        }

        if ($status === 'all' || $status === 'payment_verified') {
            $paymentVerifiedMembers = $paymentVerifiedMembers->where('payment_status', 'verified')
                ->where('card_issuance_status', 'pending')
                ->orderBy('payment_verified_at', 'desc')
                ->paginate(20);
        } else {
            $paymentVerifiedMembers = collect([]);
        }

        return view('members.approval.index', compact(
            'pendingMembers', 
            'approvedMembers', 
            'paymentVerifiedMembers',
            'search',
            'status'
        ));
    }

    /**
     * Show member details for approval
     */
    public function show(Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        // Load the relationships to avoid N+1 queries
        $member->load(['membershipType', 'approvedBy', 'paymentVerifiedBy', 'cardApprovedBy']);

        return view('members.approval.show', compact('member'));
    }

    /**
     * Approve a member
     */
    public function approve(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $member->update([
                'approval_status' => 'approved',
                'approval_notes' => $request->approval_notes,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            return redirect()->route('members.approval.index')
                ->with('success', 'Member approved successfully. Payment verification required.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error approving member: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a member
     */
    public function reject(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        try {
            $member->update([
                'approval_status' => 'rejected',
                'approval_notes' => $request->approval_notes,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            return redirect()->route('members.approval.index')
                ->with('success', 'Member rejected successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error rejecting member: ' . $e->getMessage()]);
        }
    }

    /**
     * Verify payment proof
     */
    public function verifyPayment(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'payment_notes' => 'nullable|string|max:1000',
            'payment_status' => 'required|in:verified,failed',
        ]);

        try {
            $member->update([
                'payment_status' => $request->payment_status,
                'payment_notes' => $request->payment_notes,
                'payment_verified_by' => $user->id,
                'payment_verified_at' => now(),
            ]);

            $status = $request->payment_status === 'verified' ? 'verified' : 'failed';
            $message = $request->payment_status === 'verified' 
                ? 'Payment verified successfully. Card issuance approval required.'
                : 'Payment verification failed.';

            return redirect()->route('members.approval.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error verifying payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve card issuance
     */
    public function approveCardIssuance(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'card_issuance_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $member->update([
                'card_issuance_status' => 'approved',
                'card_issuance_notes' => $request->card_issuance_notes,
                'card_approved_by' => $user->id,
                'card_approved_at' => now(),
            ]);

            return redirect()->route('members.approval.index')
                ->with('success', 'Card issuance approved successfully. Virtual card can now be generated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error approving card issuance: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Store payment proof
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            $member->update([
                'payment_proof_path' => $proofPath,
                'payment_notes' => $request->payment_notes,
                'payment_status' => 'pending', // Reset to pending for verification
            ]);

            return redirect()->route('members.approval.show', $member)
                ->with('success', 'Payment proof uploaded successfully. Awaiting verification.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error uploading payment proof: ' . $e->getMessage()]);
        }
    }

    /**
     * Download payment proof
     */
    public function downloadPaymentProof(Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        if (!$member->payment_proof_path) {
            return back()->withErrors(['error' => 'No payment proof found.']);
        }

        $path = storage_path('app/public/' . $member->payment_proof_path);
        
        if (!file_exists($path)) {
            return back()->withErrors(['error' => 'Payment proof file not found.']);
        }

        return response()->download($path);
    }
}
