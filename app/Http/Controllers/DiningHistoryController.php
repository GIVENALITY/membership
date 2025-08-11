<?php

namespace App\Http\Controllers;

use App\Models\DiningVisit;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class DiningHistoryController extends Controller
{
    /**
     * Show dining history with filters and analytics
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        // Get filter parameters
        $search = $request->get('search');
        $member_id = $request->get('member_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $min_amount = $request->get('min_amount');
        $max_amount = $request->get('max_amount');
        $status = $request->get('status', 'all'); // all, active, completed

        // Build query
        $query = DiningVisit::with(['member', 'recordedBy', 'checkedOutBy'])
            ->where('hotel_id', $user->hotel_id);

        // Apply filters
        if ($search) {
            $query->whereHas('member', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('membership_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($member_id) {
            $query->where('member_id', $member_id);
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        if ($min_amount) {
            $query->where('amount_spent', '>=', $min_amount);
        }

        if ($max_amount) {
            $query->where('amount_spent', '<=', $max_amount);
        }

        if ($status === 'active') {
            $query->where('is_checked_out', false);
        } elseif ($status === 'completed') {
            $query->where('is_checked_out', true);
        }

        // Get paginated results
        $visits = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get analytics data
        $analytics = $this->getAnalytics($user->hotel_id, $date_from, $date_to);

        // Get members for filter dropdown
        $members = Member::where('hotel_id', $user->hotel_id)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'membership_id']);

        return view('dining.history', compact(
            'visits', 
            'analytics', 
            'members', 
            'search', 
            'member_id', 
            'date_from', 
            'date_to', 
            'min_amount', 
            'max_amount', 
            'status'
        ));
    }

    /**
     * Get analytics data
     */
    private function getAnalytics($hotel_id, $date_from = null, $date_to = null)
    {
        $query = DiningVisit::where('hotel_id', $hotel_id);

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        // Total visits
        $totalVisits = $query->count();
        
        // Completed visits
        $completedVisits = (clone $query)->where('is_checked_out', true)->count();
        
        // Active visits
        $activeVisits = (clone $query)->where('is_checked_out', false)->count();
        
        // Total revenue
        $totalRevenue = (clone $query)->where('is_checked_out', true)->sum('amount_spent');
        
        // Total discounts given
        $totalDiscounts = (clone $query)->where('is_checked_out', true)->sum('discount_amount');
        
        // Average bill amount
        $avgBillAmount = (clone $query)->where('is_checked_out', true)->avg('amount_spent');
        
        // Average discount rate
        $avgDiscountRate = (clone $query)->where('is_checked_out', true)
            ->where('amount_spent', '>', 0)
            ->avg(DB::raw('(discount_amount / amount_spent) * 100'));

        // Monthly trends (last 6 months)
        $monthlyTrends = (clone $query)->where('is_checked_out', true)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as visits,
                SUM(amount_spent) as revenue,
                SUM(discount_amount) as discounts
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top members by visits
        $topMembers = (clone $query)->where('is_checked_out', true)
            ->with('member:id,first_name,last_name,membership_id')
            ->selectRaw('member_id, COUNT(*) as visit_count, SUM(amount_spent) as total_spent')
            ->groupBy('member_id')
            ->orderByDesc('visit_count')
            ->limit(10)
            ->get();

        return [
            'total_visits' => $totalVisits,
            'completed_visits' => $completedVisits,
            'active_visits' => $activeVisits,
            'total_revenue' => $totalRevenue,
            'total_discounts' => $totalDiscounts,
            'avg_bill_amount' => $avgBillAmount,
            'avg_discount_rate' => $avgDiscountRate,
            'monthly_trends' => $monthlyTrends,
            'top_members' => $topMembers,
        ];
    }

    /**
     * Export dining history to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }

        // Get filter parameters (same as index)
        $search = $request->get('search');
        $member_id = $request->get('member_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $min_amount = $request->get('min_amount');
        $max_amount = $request->get('max_amount');
        $status = $request->get('status', 'all');

        // Build query
        $query = DiningVisit::with(['member', 'recordedBy', 'checkedOutBy'])
            ->where('hotel_id', $user->hotel_id);

        // Apply filters (same as index)
        if ($search) {
            $query->whereHas('member', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('membership_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($member_id) {
            $query->where('member_id', $member_id);
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        if ($min_amount) {
            $query->where('amount_spent', '>=', $min_amount);
        }

        if ($max_amount) {
            $query->where('amount_spent', '<=', $max_amount);
        }

        if ($status === 'active') {
            $query->where('is_checked_out', false);
        } elseif ($status === 'completed') {
            $query->where('is_checked_out', true);
        }

        $visits = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'dining_history_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Visit ID',
                'Member Name',
                'Membership ID',
                'Visit Date',
                'Checkout Date',
                'Number of People',
                'Amount Spent',
                'Discount Amount',
                'Final Amount',
                'Discount Rate (%)',
                'Status',
                'Recorded By',
                'Checked Out By',
                'Notes',
                'Receipt'
            ]);

            // CSV data
            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->id,
                    $visit->member->full_name,
                    $visit->member->membership_id,
                    $visit->created_at->format('Y-m-d H:i:s'),
                    $visit->checked_out_at ? $visit->checked_out_at->format('Y-m-d H:i:s') : 'N/A',
                    $visit->number_of_people,
                    $visit->amount_spent ?? 'N/A',
                    $visit->discount_amount ?? 'N/A',
                    $visit->final_amount ?? 'N/A',
                    $visit->amount_spent && $visit->amount_spent > 0 ? 
                        round(($visit->discount_amount / $visit->amount_spent) * 100, 2) : 'N/A',
                    $visit->is_checked_out ? 'Completed' : 'Active',
                    $visit->recordedBy ? $visit->recordedBy->name : 'N/A',
                    $visit->checkedOutBy ? $visit->checkedOutBy->name : 'N/A',
                    $visit->notes ?? 'N/A',
                    $visit->receipt_path ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get dining history for a specific member
     */
    public function memberHistory(Member $member)
    {
        $user = auth()->user();
        if (!$user || !$user->hotel_id || $member->hotel_id !== $user->hotel_id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $visits = $member->diningVisits()
            ->with(['recordedBy', 'checkedOutBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get member analytics
        $analytics = [
            'total_visits' => $member->diningVisits()->count(),
            'completed_visits' => $member->diningVisits()->where('is_checked_out', true)->count(),
            'total_spent' => $member->diningVisits()->where('is_checked_out', true)->sum('amount_spent'),
            'total_discounts' => $member->diningVisits()->where('is_checked_out', true)->sum('discount_amount'),
            'avg_bill_amount' => $member->diningVisits()->where('is_checked_out', true)->avg('amount_spent'),
            'last_visit' => $member->diningVisits()->where('is_checked_out', true)->latest()->first(),
        ];

        return view('dining.member-history', compact('member', 'visits', 'analytics'));
    }
} 