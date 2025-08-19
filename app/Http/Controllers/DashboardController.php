<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\DiningVisit;
use App\Models\MembershipType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with real statistics
     */
    public function index()
    {
        $user = Auth::user();
        $hotelId = $user->hotel_id;
        
        // Get current month and previous month for comparisons
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Total members statistics
        $totalMembers = Member::where('hotel_id', $hotelId)->where('status', 'active')->count();
        $newMembersThisMonth = Member::where('hotel_id', $hotelId)
            ->where('status', 'active')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $newMembersLastMonth = Member::where('hotel_id', $hotelId)
            ->where('status', 'active')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->count();
        
        // Calculate member growth percentage
        $memberGrowthPercentage = $newMembersLastMonth > 0 
            ? round((($newMembersThisMonth - $newMembersLastMonth) / $newMembersLastMonth) * 100, 1)
            : ($newMembersThisMonth > 0 ? 100 : 0);
        
        // Today's visits
        $todaysVisits = DiningVisit::where('hotel_id', $hotelId)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $yesterdaysVisits = DiningVisit::where('hotel_id', $hotelId)
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
        
        // Calculate visit growth percentage
        $visitGrowthPercentage = $yesterdaysVisits > 0 
            ? round((($todaysVisits - $yesterdaysVisits) / $yesterdaysVisits) * 100, 1)
            : ($todaysVisits > 0 ? 100 : 0);
        
        // Discounts given this month
        $discountsThisMonth = DiningVisit::where('hotel_id', $hotelId)
            ->where('is_checked_out', true)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('discount_amount');
        
        // Average visits per member
        $totalVisits = DiningVisit::where('hotel_id', $hotelId)->count();
        $avgVisitsPerMember = $totalMembers > 0 ? round($totalVisits / $totalMembers, 1) : 0;
        
        // Recent member activity (last 5 members with recent visits)
        $recentActivity = DiningVisit::where('hotel_id', $hotelId)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->unique('member_id')
            ->take(5);
        
        // Birthday statistics
        $todayBirthdays = Member::where('hotel_id', $hotelId)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") = ?', [Carbon::today()->format('m-d')])
            ->where('status', 'active')
            ->count();
        
        $thisWeekBirthdays = Member::where('hotel_id', $hotelId)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") BETWEEN ? AND ?', [
                Carbon::now()->startOfWeek()->format('m-d'),
                Carbon::now()->endOfWeek()->format('m-d')
            ])
            ->where('status', 'active')
            ->count();
        
        $thisMonthBirthdays = Member::where('hotel_id', $hotelId)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', Carbon::now()->month)
            ->where('status', 'active')
            ->count();
        
        // Visit frequency analysis
        $visitFrequency = $this->getVisitFrequencyStats($hotelId);
        
        // Monthly statistics for the chart
        $monthlyStats = $this->getMonthlyStats($hotelId);
        
        return view('dashboard', compact(
            'totalMembers',
            'newMembersThisMonth',
            'memberGrowthPercentage',
            'todaysVisits',
            'visitGrowthPercentage',
            'discountsThisMonth',
            'avgVisitsPerMember',
            'recentActivity',
            'todayBirthdays',
            'thisWeekBirthdays',
            'thisMonthBirthdays',
            'visitFrequency',
            'monthlyStats'
        ));
    }
    
    /**
     * Get visit frequency statistics
     */
    private function getVisitFrequencyStats($hotelId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        // Get all members with their visit counts this month
        $memberVisits = DiningVisit::where('hotel_id', $hotelId)
            ->where('is_checked_out', true)
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->select('member_id', DB::raw('count(*) as visit_count'))
            ->groupBy('member_id')
            ->get();
        
        $visitFrequency = [
            'once' => 0,
            'twice' => 0,
            'three_times' => 0,
            'four_plus' => 0
        ];
        
        foreach ($memberVisits as $visit) {
            if ($visit->visit_count == 1) {
                $visitFrequency['once']++;
            } elseif ($visit->visit_count == 2) {
                $visitFrequency['twice']++;
            } elseif ($visit->visit_count == 3) {
                $visitFrequency['three_times']++;
            } else {
                $visitFrequency['four_plus']++;
            }
        }
        
        return $visitFrequency;
    }
    
    /**
     * Get monthly statistics for the last 6 months
     */
    private function getMonthlyStats($hotelId)
    {
        $stats = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $newMembers = Member::where('hotel_id', $hotelId)
                ->where('status', 'active')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $visits = DiningVisit::where('hotel_id', $hotelId)
                ->where('is_checked_out', true)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $revenue = DiningVisit::where('hotel_id', $hotelId)
                ->where('is_checked_out', true)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('final_amount');
            
            $stats[] = [
                'month' => $date->format('M Y'),
                'new_members' => $newMembers,
                'visits' => $visits,
                'revenue' => $revenue
            ];
        }
        
        return $stats;
    }
} 