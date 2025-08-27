<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberEmail;
use App\Mail\WelcomeMemberMail;
use Carbon\Carbon;

class EmailLogController extends Controller
{
    /**
     * Display email logs with filtering
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $hotel = $user->hotel;

        $query = EmailLog::forHotel($hotel->id)->with(['member']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Get statistics
        $stats = [
            'total' => EmailLog::forHotel($hotel->id)->count(),
            'sent' => EmailLog::forHotel($hotel->id)->byStatus('sent')->count(),
            'delivered' => EmailLog::forHotel($hotel->id)->byStatus('delivered')->count(),
            'opened' => EmailLog::forHotel($hotel->id)->byStatus('opened')->count(),
            'failed' => EmailLog::forHotel($hotel->id)->failed()->count(),
            'bounced' => EmailLog::forHotel($hotel->id)->byStatus('bounced')->count(),
        ];

        // Get email types for filter
        $emailTypes = EmailLog::forHotel($hotel->id)
            ->select('email_type')
            ->distinct()
            ->pluck('email_type');

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('email-logs.index', compact('logs', 'stats', 'emailTypes'));
    }

    /**
     * Show detailed view of an email log
     */
    public function show(EmailLog $emailLog)
    {
        // Ensure user can only view logs from their hotel
        if ($emailLog->hotel_id !== auth()->user()->hotel->id) {
            abort(403);
        }

        return view('email-logs.show', compact('emailLog'));
    }

    /**
     * Retry sending a failed email
     */
    public function retry(EmailLog $emailLog)
    {
        // Ensure user can only retry logs from their hotel
        if ($emailLog->hotel_id !== auth()->user()->hotel->id) {
            abort(403);
        }

        if (!$emailLog->canRetry()) {
            return back()->withErrors(['error' => 'This email cannot be retried.']);
        }

        try {
            // Create new email log entry for retry
            $newLog = EmailLog::create([
                'hotel_id' => $emailLog->hotel_id,
                'email_type' => $emailLog->email_type,
                'subject' => $emailLog->subject,
                'content' => $emailLog->content,
                'recipient_email' => $emailLog->recipient_email,
                'recipient_name' => $emailLog->recipient_name,
                'member_id' => $emailLog->member_id,
                'status' => 'pending',
                'metadata' => ['retry_of' => $emailLog->id]
            ]);

            // Send the email based on type
            if ($emailLog->email_type === 'welcome' && $emailLog->member_id) {
                $member = Member::find($emailLog->member_id);
                if ($member) {
                    Mail::to($member->email)->send(new WelcomeMemberMail($member, $emailLog->subject, $emailLog->content));
                    $newLog->update(['status' => 'sent', 'sent_at' => now()]);
                }
            } else {
                // For member emails, create a temporary recipient object
                $recipient = new \stdClass();
                $recipient->email = $emailLog->recipient_email;
                $recipient->first_name = $emailLog->recipient_name ?: 'Guest';
                $recipient->last_name = '';
                $recipient->hotel = auth()->user()->hotel;

                $emailData = [
                    'subject' => $emailLog->subject,
                    'content' => $emailLog->content,
                    'sent_at' => now(),
                    'hotel_name' => auth()->user()->hotel->name
                ];

                Mail::to($emailLog->recipient_email)->send(new MemberEmail($recipient, $emailData));
                $newLog->update(['status' => 'sent', 'sent_at' => now()]);
            }

            return back()->with('success', 'Email retry initiated successfully.');

        } catch (\Exception $e) {
            $newLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to retry email: ' . $e->getMessage()]);
        }
    }

    /**
     * Get email statistics for dashboard
     */
    public function statistics()
    {
        $user = auth()->user();
        $hotel = $user->hotel;

        // Get statistics for the last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $stats = [
            'total_sent' => EmailLog::forHotel($hotel->id)->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'successful' => EmailLog::forHotel($hotel->id)->where('created_at', '>=', $thirtyDaysAgo)->successful()->count(),
            'failed' => EmailLog::forHotel($hotel->id)->where('created_at', '>=', $thirtyDaysAgo)->failed()->count(),
            'opened' => EmailLog::forHotel($hotel->id)->where('created_at', '>=', $thirtyDaysAgo)->byStatus('opened')->count(),
            'delivery_rate' => 0,
            'open_rate' => 0
        ];

        if ($stats['total_sent'] > 0) {
            $stats['delivery_rate'] = round(($stats['successful'] / $stats['total_sent']) * 100, 2);
            $stats['open_rate'] = round(($stats['opened'] / $stats['successful']) * 100, 2);
        }

        // Get daily statistics for chart
        $dailyStats = EmailLog::forHotel($hotel->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, 
                        SUM(CASE WHEN status IN ("sent", "delivered", "opened") THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status IN ("failed", "bounced") THEN 1 ELSE 0 END) as failed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('email-logs.statistics', compact('stats', 'dailyStats'));
    }

    /**
     * Export email logs
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $hotel = $user->hotel;

        $query = EmailLog::forHotel($hotel->id)->with(['member']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'email_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Date', 'Type', 'Subject', 'Recipient Email', 'Recipient Name', 
                'Status', 'Error Message', 'Sent At', 'Delivered At', 'Opened At'
            ]);

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->getEmailTypeLabel(),
                    $log->subject,
                    $log->recipient_email,
                    $log->recipient_name,
                    $log->status,
                    $log->error_message,
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '',
                    $log->delivered_at ? $log->delivered_at->format('Y-m-d H:i:s') : '',
                    $log->opened_at ? $log->opened_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
