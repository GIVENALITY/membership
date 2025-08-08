<?php

namespace App\Http\Controllers;

use App\Models\DiningVisit;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DiningVisitController extends Controller
{
    /**
     * Store a newly created dining visit.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Allow either member_id or membership_id
            'member_id' => 'nullable|exists:members,id',
            'membership_id' => 'nullable|string',

            'bill_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'discount_rate' => 'required|numeric|min:0|max:100',
            'visited_at' => 'nullable|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // up to 5MB
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Resolve member
        $member = null;
        if ($request->filled('member_id')) {
            $member = Member::find($request->member_id);
        } elseif ($request->filled('membership_id')) {
            $member = Member::where('membership_id', $request->membership_id)->first();
        }

        if (!$member) {
            return redirect()->back()->with('error', 'Member not found.')->withInput();
        }

        // Handle file upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        // Create dining visit
        $visit = DiningVisit::create([
            'member_id' => $member->id,
            'bill_amount' => $request->bill_amount,
            'discount_amount' => $request->input('discount_amount', 0),
            'final_amount' => $request->final_amount,
            'discount_rate' => $request->discount_rate,
            'receipt_path' => $receiptPath,
            'notes' => $request->input('notes'),
            'visited_at' => $request->input('visited_at', now()),
        ]);

        // Update member aggregates
        $member->increment('total_visits');
        $member->total_spent = ($member->total_spent ?? 0) + (float) $request->final_amount;
        $member->last_visit_at = now();
        $member->current_discount_rate = $member->calculateDiscountRate();
        $member->save();

        return redirect()->back()->with('success', 'Visit recorded successfully' . ($receiptPath ? ' and receipt uploaded.' : '.'));
    }
} 