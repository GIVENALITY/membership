<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\DiningVisit;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $hotel = $user->hotel;

            // Get basic statistics for all roles
            $stats = $this->getBasicStats($user->hotel_id);

            // Return role-specific dashboard
            switch ($user->role) {
                case 'admin':
                case 'manager':
                    return $this->managerDashboard($user, $hotel, $stats);
                case 'cashier':
                    return $this->cashierDashboard($user, $hotel, $stats);
                case 'frontdesk':
                    return $this->frontdeskDashboard($user, $hotel, $stats);
                default:
                    return $this->managerDashboard($user, $hotel, $stats);
            }
        } catch (\Exception $e) {
            // Fallback to a simple dashboard if there are any errors
            return $this->fallbackDashboard();
        }
    }

    /**
     * Fallback dashboard for when there are database errors
     */
    private function fallbackDashboard()
    {
        $user = auth()->user();
        $hotel = $user->hotel ?? null;
        
        $stats = [
            'total_members' => 0,
            'active_members' => 0,
            'total_visits' => 0,
            'today_visits' => 0,
            'active_visits' => 0,
        ];

        return view('dashboard.manager', compact('user', 'hotel', 'stats'));
    }

    /**
     * Manager/Admin Dashboard - Full access
     */
    private function managerDashboard($user, $hotel, $stats)
    {
        try {
            // Additional manager-specific stats
            $recentVisits = DiningVisit::where('hotel_id', $user->hotel_id)
                ->with('member')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $membershipTypes = MembershipType::where('hotel_id', $user->hotel_id)
                ->where('is_active', true)
                ->get();

            $monthlyStats = $this->getMonthlyStats($user->hotel_id);

            return view('dashboard.manager', compact('user', 'hotel', 'stats', 'recentVisits', 'membershipTypes', 'monthlyStats'));
        } catch (\Exception $e) {
            // Fallback with empty data
            return view('dashboard.manager', compact('user', 'hotel', 'stats', 'recentVisits', 'membershipTypes', 'monthlyStats'));
        }
    }

    /**
     * Cashier Dashboard - Payment focused
     */
    private function cashierDashboard($user, $hotel, $stats)
    {
        try {
            // Cashier-specific data
            $activeVisits = DiningVisit::where('hotel_id', $user->hotel_id)
                ->where('is_checked_out', false)
                ->with('member')
                ->orderBy('created_at', 'desc')
                ->get();

            $todayVisits = DiningVisit::where('hotel_id', $user->hotel_id)
                ->whereDate('created_at', Carbon::today())
                ->where('is_checked_out', true)
                ->count();

            $todayRevenue = DiningVisit::where('hotel_id', $user->hotel_id)
                ->whereDate('checked_out_at', Carbon::today())
                ->where('is_checked_out', true)
                ->sum('final_amount');

            return view('dashboard.cashier', compact('user', 'hotel', 'stats', 'activeVisits', 'todayVisits', 'todayRevenue'));
        } catch (\Exception $e) {
            // Fallback with empty data
            $activeVisits = collect();
            $todayVisits = 0;
            $todayRevenue = 0;
            return view('dashboard.cashier', compact('user', 'hotel', 'stats', 'activeVisits', 'todayVisits', 'todayRevenue'));
        }
    }

    /**
     * Front Desk Dashboard - Check-in focused
     */
    private function frontdeskDashboard($user, $hotel, $stats)
    {
        try {
            // Front desk specific data
            $todayCheckins = DiningVisit::where('hotel_id', $user->hotel_id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            $recentMembers = Member::where('hotel_id', $user->hotel_id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $birthdayMembers = Member::where('hotel_id', $user->hotel_id)
                ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") = ?', [Carbon::today()->format('m-d')])
                ->get();

            return view('dashboard.frontdesk', compact('user', 'hotel', 'stats', 'todayCheckins', 'recentMembers', 'birthdayMembers'));
        } catch (\Exception $e) {
            // Fallback with empty data
            $todayCheckins = 0;
            $recentMembers = collect();
            $birthdayMembers = collect();
            return view('dashboard.frontdesk', compact('user', 'hotel', 'stats', 'todayCheckins', 'recentMembers', 'birthdayMembers'));
        }
    }

    /**
     * Get basic statistics for the hotel
     */
    private function getBasicStats($hotelId)
    {
        try {
            return [
                'total_members' => Member::where('hotel_id', $hotelId)->count(),
                'active_members' => Member::where('hotel_id', $hotelId)->where('status', 'active')->count(),
                'total_visits' => DiningVisit::where('hotel_id', $hotelId)->count(),
                'today_visits' => DiningVisit::where('hotel_id', $hotelId)->whereDate('created_at', Carbon::today())->count(),
                'active_visits' => DiningVisit::where('hotel_id', $hotelId)->where('is_checked_out', false)->count(),
            ];
        } catch (\Exception $e) {
            // Fallback if there are database issues
            return [
                'total_members' => 0,
                'active_members' => 0,
                'total_visits' => 0,
                'today_visits' => 0,
                'active_visits' => 0,
            ];
        }
    }

    /**
     * Get monthly statistics for managers
     */
    private function getMonthlyStats($hotelId)
    {
        try {
            $currentMonth = Carbon::now()->startOfMonth();
            
            return [
                'monthly_visits' => DiningVisit::where('hotel_id', $hotelId)
                    ->whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)
                    ->count(),
                'monthly_revenue' => DiningVisit::where('hotel_id', $hotelId)
                    ->whereMonth('checked_out_at', $currentMonth->month)
                    ->whereYear('checked_out_at', $currentMonth->year)
                    ->where('is_checked_out', true)
                    ->sum('final_amount'),
                'monthly_members' => Member::where('hotel_id', $hotelId)
                    ->whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)
                    ->count(),
            ];
        } catch (\Exception $e) {
            // Fallback if there are database issues
            return [
                'monthly_visits' => 0,
                'monthly_revenue' => 0,
                'monthly_members' => 0,
            ];
        }
    }
} 