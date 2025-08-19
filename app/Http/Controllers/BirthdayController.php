<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BirthdayController extends Controller
{
    /**
     * Show today's birthdays
     */
    public function today()
    {
        $user = Auth::user();
        
        $todayBirthdays = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") = ?', [Carbon::today()->format('m-d')])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('birthdays.today', compact('todayBirthdays'));
    }

    /**
     * Show this week's birthdays
     */
    public function thisWeek()
    {
        $user = Auth::user();
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weekBirthdays = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") BETWEEN ? AND ?', [
                $startOfWeek->format('m-d'),
                $endOfWeek->format('m-d')
            ])
            ->where('status', 'active')
            ->orderByRaw('DATE_FORMAT(birth_date, "%m-%d")')
            ->get()
            ->groupBy(function($member) {
                return Carbon::parse($member->birth_date)->format('l, M d');
            });

        return view('birthdays.this-week', compact('weekBirthdays', 'startOfWeek', 'endOfWeek'));
    }

    /**
     * Get birthday notifications for dashboard
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        $todayCount = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") = ?', [Carbon::today()->format('m-d')])
            ->where('status', 'active')
            ->count();

        $thisWeekCount = Member::where('hotel_id', $user->hotel_id)
            ->whereNotNull('birth_date')
            ->whereRaw('DATE_FORMAT(birth_date, "%m-%d") BETWEEN ? AND ?', [
                Carbon::now()->startOfWeek()->format('m-d'),
                Carbon::now()->endOfWeek()->format('m-d')
            ])
            ->where('status', 'active')
            ->count();

        return response()->json([
            'today_count' => $todayCount,
            'this_week_count' => $thisWeekCount
        ]);
    }
}
