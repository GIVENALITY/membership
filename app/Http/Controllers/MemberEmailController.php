<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Hotel;
use App\Models\EmailLog;
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
        
        try {
            return view('members.emails.index', compact('hotel', 'totalMembers', 'activeMembers', 'inactiveMembers', 'recentTemplates'));
        } catch (\Exception $e) {
            \Log::error('Error rendering email index view', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'View error',
                'message' => $e->getMessage()
            ], 500);
        }
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
        try {
                    $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,active,inactive,selected,filtered,bounced,custom',
            'selected_members' => 'required_if:recipient_type,selected|required_if:recipient_type,bounced|array',
            'selected_members.*' => 'exists:members,id',
            'membership_type_ids' => 'required_if:recipient_type,filtered|array',
            'membership_type_ids.*' => 'exists:membership_types,id',
            'status_filter' => 'required_if:recipient_type,filtered|in:all,active,inactive',
            'custom_emails' => 'required_if:recipient_type,custom|nullable|string',
            'custom_names' => 'nullable|string',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

            $user = auth()->user();
            $hotel = $user->hotel;
            
            \Log::info('Email send request started', [
                'user_id' => $user->id,
                'hotel_id' => $hotel->id,
                'recipient_type' => $request->recipient_type
            ]);

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
        
        \Log::info('Starting to send emails', ['recipient_count' => $recipients->count()]);

        foreach ($recipients as $member) {
            try {
                \Log::info('Sending email to member', [
                    'member_id' => $member->id,
                    'member_email' => $member->email,
                    'send_immediately' => $request->send_immediately
                ]);
                
                // Create email log entry
                $emailLogData = [
                    'hotel_id' => $hotel->id,
                    'email_type' => 'member_email',
                    'subject' => $emailData['subject'],
                    'content' => $emailData['content'],
                    'recipient_email' => $member->email,
                    'recipient_name' => $member->first_name . ' ' . $member->last_name,
                    'status' => 'pending',
                    'metadata' => [
                        'recipient_type' => $request->recipient_type,
                        'send_immediately' => $request->send_immediately,
                        'sent_by' => $user->id
                    ]
                ];
                
                // Only set member_id if it's a valid integer (not a custom recipient)
                if (is_numeric($member->id) && $member->id > 0) {
                    $emailLogData['member_id'] = $member->id;
                }
                
                $emailLog = EmailLog::create($emailLogData);
                
                if ($request->send_immediately) {
                    // Send immediately
                    Mail::to($member->email)->send(new MemberEmail($member, $emailData));
                    $emailLog->update(['status' => 'sent', 'sent_at' => now()]);
                    $sentCount++;
                    \Log::info('Email sent immediately', ['member_id' => $member->id]);
                } else {
                    // Queue for later
                    Mail::to($member->email)->queue(new MemberEmail($member, $emailData));
                    $emailLog->update(['status' => 'queued']);
                    $sentCount++;
                    \Log::info('Email queued', ['member_id' => $member->id]);
                }
            } catch (\Exception $e) {
                $failedCount++;
                
                // Update email log with error
                if (isset($emailLog)) {
                    $emailLog->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                }
                
                \Log::error('Failed to send email to member ' . $member->id . ': ' . $e->getMessage(), [
                    'member_id' => $member->id,
                    'member_email' => $member->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Save email template
        $this->saveEmailTemplate($request, $hotel);

        $message = "Email sent to {$sentCount} members successfully.";
        if ($failedCount > 0) {
            $message .= " Failed to send to {$failedCount} members.";
        }

        \Log::info('Email sending completed', [
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);

        // Show debug information on screen
        if (request()->get('debug') === '1') {
            return response()->json([
                'status' => 'completed',
                'message' => $message,
                'debug_info' => [
                    'recipient_count' => $recipients->count(),
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                    'recipient_type' => $request->recipient_type,
                    'send_immediately' => $request->send_immediately,
                    'recipients' => $recipients->map(function($recipient) {
                        return [
                            'id' => $recipient->id,
                            'name' => $recipient->first_name . ' ' . $recipient->last_name,
                            'email' => $recipient->email,
                            'is_member' => $recipient instanceof \App\Models\Member
                        ];
                    }),
                    'email_data' => [
                        'subject' => $emailData['subject'],
                        'hotel_name' => $emailData['hotel_name'],
                        'sent_by' => $emailData['sent_by']
                    ]
                ]
            ]);
        }

        return redirect()->route('members.emails.index')->with('success', $message);
        
        } catch (\Exception $e) {
            \Log::error('Email sending failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show debug information on screen for errors too
            if (request()->get('debug') === '1') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send emails: ' . $e->getMessage(),
                    'debug_info' => [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]
                ]);
            }
            
            return back()->with('error', 'Failed to send emails: ' . $e->getMessage());
        }
    }

    /**
     * Get recipients based on selection criteria
     */
    private function getRecipients(Request $request, $hotel)
    {
        switch ($request->recipient_type) {
            case 'all':
                return Member::where('hotel_id', $hotel->id)->whereNotNull('email')->get();

            case 'active':
                return Member::where('hotel_id', $hotel->id)->whereNotNull('email')->where('status', 'active')->get();

            case 'inactive':
                return Member::where('hotel_id', $hotel->id)->whereNotNull('email')->where('status', 'inactive')->get();

            case 'selected':
                return Member::where('hotel_id', $hotel->id)->whereNotNull('email')->whereIn('id', $request->selected_members)->get();

            case 'bounced':
                return Member::where('hotel_id', $hotel->id)->whereNotNull('email')->whereIn('id', $request->selected_members)->get();

            case 'filtered':
                $query = Member::where('hotel_id', $hotel->id)->whereNotNull('email');
                
                if (!empty($request->membership_type_ids)) {
                    $query->whereIn('membership_type_id', $request->membership_type_ids);
                }
                
                if ($request->status_filter !== 'all') {
                    $query->where('status', $request->status_filter);
                }
                
                return $query->get();

            case 'custom':
                return $this->getCustomRecipients($request);

            default:
                return collect();
        }
    }

    /**
     * Save email template for reuse
     */
    private function saveEmailTemplate(Request $request, $hotel)
    {
        try {
            DB::table('email_templates')->insert([
                'hotel_id' => $hotel->id,
                'name' => $request->subject,
                'subject' => $request->subject,
                'content' => $request->content,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Could not save email template', ['error' => $e->getMessage()]);
            // Don't throw the error, just log it
        }
    }

    /**
     * Get custom recipients from email addresses
     */
    private function getCustomRecipients(Request $request)
    {
        $emails = array_filter(array_map('trim', explode(',', str_replace(["\n", "\r"], ',', $request->custom_emails))));
        $names = [];
        
        if ($request->custom_names) {
            $names = array_filter(array_map('trim', explode(',', str_replace(["\n", "\r"], ',', $request->custom_names))));
        }
        
        $recipients = collect();
        $user = auth()->user();
        
        foreach ($emails as $index => $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Create a temporary member object for custom recipients
                $recipient = new \stdClass();
                $recipient->id = 'custom_' . $index;
                $recipient->email = $email;
                $recipient->first_name = isset($names[$index]) ? $names[$index] : 'Guest';
                $recipient->last_name = '';
                $recipient->membership_id = 'CUSTOM';
                $recipient->membershipType = (object) ['name' => 'Custom Recipient'];
                $recipient->hotel = $user->hotel; // Add hotel information
                
                $recipients->push($recipient);
            }
        }
        
        return $recipients;
    }

    /**
     * Get recent email templates
     */
    private function getRecentTemplates()
    {
        try {
            $user = auth()->user();
            return DB::table('email_templates')
                ->where('hotel_id', $user->hotel->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Email templates table not found, returning empty collection', ['error' => $e->getMessage()]);
            return collect();
        }
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
     * Get bounced members for email composer
     */
    public function getBouncedMembers()
    {
        $user = auth()->user();
        $hotel = $user->hotel;

        // Get members whose emails have bounced or failed in the last 30 days
        $bouncedMembers = Member::where('members.hotel_id', $hotel->id)
            ->join('email_logs', 'members.id', '=', 'email_logs.member_id')
            ->where('email_logs.hotel_id', $hotel->id)
            ->whereIn('email_logs.status', ['failed', 'bounced'])
            ->where('email_logs.created_at', '>=', now()->subDays(30))
            ->select('members.*')
            ->distinct()
            ->orderBy('members.first_name')
            ->orderBy('members.last_name')
            ->get();

        return response()->json([
            'status' => 'success',
            'members' => $bouncedMembers->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'membership_id' => $member->membership_id,
                    'hotel' => $member->hotel->name
                ];
            })
        ]);
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

    /**
     * Get recipient count for AJAX requests
     */
    public function getRecipientCount(Request $request)
    {
        try {
            $user = auth()->user();
            $hotel = $user->hotel;
            $type = $request->get('type');
            
            $query = Member::where('hotel_id', $hotel->id)->whereNotNull('email');
            
            switch ($type) {
                case 'all':
                    $count = $query->count();
                    break;
                    
                case 'active':
                    $count = $query->where('status', 'active')->count();
                    break;
                    
                case 'inactive':
                    $count = $query->where('status', 'inactive')->count();
                    break;
                    
                case 'filtered':
                    $membershipTypeIds = $request->get('membership_type_ids');
                    $statusFilter = $request->get('status');
                    
                    if ($membershipTypeIds) {
                        $typeIds = explode(',', $membershipTypeIds);
                        $query->whereIn('membership_type_id', $typeIds);
                    }
                    
                    if ($statusFilter && $statusFilter !== 'all') {
                        $query->where('status', $statusFilter);
                    }
                    
                    $count = $query->count();
                    break;
                    
                default:
                    $count = 0;
            }
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting recipient count', [
                'error' => $e->getMessage(),
                'type' => $type ?? 'unknown'
            ]);
            
            return response()->json(['count' => 0, 'error' => 'Error calculating recipients']);
        }
    }
}
