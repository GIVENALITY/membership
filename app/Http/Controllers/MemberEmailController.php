<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Hotel;
use App\Mail\MemberEmail;
use Carbon\Carbon;

class MemberEmailController extends Controller
{
    /**
     * Show the email composition page
     */
    public function index()
    {
        // Debug logging
        \Log::info('MemberEmailController@index called');
        
        $user = auth()->user();
        if (!$user) {
            \Log::error('No authenticated user found');
            return redirect('/login');
        }
        
        \Log::info('User authenticated', ['user_id' => $user->id, 'name' => $user->name]);
        
        $hotel = $user->hotel;
        if (!$hotel) {
            \Log::error('User has no hotel', ['user_id' => $user->id]);
            return back()->withErrors(['error' => 'User not associated with a hotel.']);
        }
        
        \Log::info('Hotel found', ['hotel_id' => $hotel->id, 'hotel_name' => $hotel->name]);
        
        // Get member statistics
        $totalMembers = Member::where('hotel_id', $hotel->id)->count();
        $activeMembers = Member::where('hotel_id', $hotel->id)->where('status', 'active')->count();
        $inactiveMembers = Member::where('hotel_id', $hotel->id)->where('status', 'inactive')->count();
        
        // Get recent email templates
        $recentTemplates = $this->getRecentTemplates();
        
        \Log::info('Rendering email index view', [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'inactiveMembers' => $inactiveMembers
        ]);
        
        return view('members.emails.index', compact('hotel', 'totalMembers', 'activeMembers', 'inactiveMembers', 'recentTemplates'));
    }

    /**
     * Show the email composition form
     */
    public function compose()
    {
        $user = auth()->user();
        $hotel = $user->hotel;
        
        // Get member filters
        $membershipTypes = $hotel->membershipTypes()->where('is_active', true)->get();
        
        return view('members.emails.compose', compact('hotel', 'membershipTypes'));
    }

    /**
     * Send emails to selected members
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,active,inactive,selected,filtered',
            'selected_members' => 'required_if:recipient_type,selected|array',
            'selected_members.*' => 'exists:members,id',
            'membership_type_ids' => 'required_if:recipient_type,filtered|array',
            'membership_type_ids.*' => 'exists:membership_types,id',
            'status_filter' => 'required_if:recipient_type,filtered|in:all,active,inactive',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        $user = auth()->user();
        $hotel = $user->hotel;

        // Get recipients based on selection
        $recipients = $this->getRecipients($request, $hotel);
        
        if ($recipients->isEmpty()) {
            return back()->with('error', 'No recipients found for the selected criteria.');
        }

        // Create email data
        $emailData = [
            'subject' => $request->subject,
            'content' => $request->content,
            'hotel_name' => $hotel->name,
            'sent_by' => $user->name,
            'sent_at' => now()
        ];

        // Send emails
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $member) {
            try {
                if ($request->send_immediately) {
                    // Send immediately
                    Mail::to($member->email)->send(new MemberEmail($member, $emailData));
                    $sentCount++;
                } else {
                    // Queue for later
                    Mail::to($member->email)->queue(new MemberEmail($member, $emailData));
                    $sentCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send email to member ' . $member->id . ': ' . $e->getMessage());
            }
        }

        // Save email template
        $this->saveEmailTemplate($request, $hotel);

        $message = "Email sent to {$sentCount} members successfully.";
        if ($failedCount > 0) {
            $message .= " Failed to send to {$failedCount} members.";
        }

        return redirect()->route('members.emails.index')->with('success', $message);
    }

    /**
     * Get recipients based on selection criteria
     */
    private function getRecipients(Request $request, $hotel)
    {
        $query = Member::where('hotel_id', $hotel->id)->whereNotNull('email');

        switch ($request->recipient_type) {
            case 'all':
                return $query->get();

            case 'active':
                return $query->where('status', 'active')->get();

            case 'inactive':
                return $query->where('status', 'inactive')->get();

            case 'selected':
                return $query->whereIn('id', $request->selected_members)->get();

            case 'filtered':
                if (!empty($request->membership_type_ids)) {
                    $query->whereIn('membership_type_id', $request->membership_type_ids);
                }
                
                if ($request->status_filter !== 'all') {
                    $query->where('status', $request->status_filter);
                }
                
                return $query->get();

            default:
                return collect();
        }
    }

    /**
     * Save email template for reuse
     */
    private function saveEmailTemplate(Request $request, $hotel)
    {
        DB::table('email_templates')->insert([
            'hotel_id' => $hotel->id,
            'name' => $request->subject,
            'subject' => $request->subject,
            'content' => $request->content,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Get recent email templates
     */
    private function getRecentTemplates()
    {
        $user = auth()->user();
        return DB::table('email_templates')
            ->where('hotel_id', $user->hotel->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get member suggestions for autocomplete
     */
    public function getMemberSuggestions(Request $request)
    {
        $user = auth()->user();
        $query = $request->get('query', '');
        
        $members = Member::where('hotel_id', $user->hotel->id)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('membership_id', 'like', "%{$query}%");
            })
            ->whereNotNull('email')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'membership_id']);
        
        return response()->json($members);
    }

    /**
     * Get email statistics
     */
    public function statistics()
    {
        $user = auth()->user();
        $hotel = $user->hotel;
        
        // Get email statistics (you can implement this based on your email tracking needs)
        $stats = [
            'total_sent' => 0,
            'total_opened' => 0,
            'total_clicked' => 0,
            'open_rate' => 0,
            'click_rate' => 0
        ];
        
        return view('members.emails.statistics', compact('hotel', 'stats'));
    }
}
