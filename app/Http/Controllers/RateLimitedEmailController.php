<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Models\Member;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RateLimitedEmailController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get recent email logs for resumption
        $recentLogs = EmailLog::where('hotel_id', $user->hotel_id)
            ->where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Get failed emails that can be retried
        $failedEmails = EmailLog::where('hotel_id', $user->hotel_id)
            ->where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        // Get current batch status
        $currentBatch = Cache::get('email_batch_' . $user->hotel_id);
        
        return view('rate-limited-emails.index', compact('recentLogs', 'failedEmails', 'currentBatch'));
    }
    
    public function startBatch(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'resume_from' => 'nullable|exists:email_logs,id',
            'batch_size' => 'nullable|integer|min:1|max:75',
            'dry_run' => 'boolean'
        ]);
        
        $user = auth()->user();
        
        // Check if there's already a batch running
        if (Cache::has('email_batch_' . $user->hotel_id)) {
            return back()->withErrors(['error' => 'An email batch is already running. Please wait for it to complete.']);
        }
        
        // Prepare command arguments
        $args = [
            '--subject' => $request->subject,
            '--content' => $request->content,
            '--type' => 'custom',
            '--batch-size' => $request->batch_size ?: 60
        ];
        
        if ($request->member_ids) {
            $args['--member-ids'] = $request->member_ids;
        }
        
        if ($request->resume_from) {
            $args['--resume-from'] = $request->resume_from;
        }
        
        if ($request->dry_run) {
            $args['--dry-run'] = true;
        }
        
        // Store batch info in cache
        $batchInfo = [
            'started_at' => now(),
            'subject' => $request->subject,
            'member_count' => $this->getMemberCount($request->member_ids, $request->resume_from),
            'batch_size' => $request->batch_size ?: 60,
            'dry_run' => $request->dry_run,
            'status' => 'running'
        ];
        
        Cache::put('email_batch_' . $user->hotel_id, $batchInfo, 3600); // 1 hour
        
        // Start the command in background
        $this->runCommandInBackground($args);
        
        $message = $request->dry_run 
            ? 'Dry run started successfully. Check the progress below.'
            : 'Email batch started successfully. Check the progress below.';
            
        return back()->with('success', $message);
    }
    
    public function getProgress()
    {
        $user = auth()->user();
        $batchInfo = Cache::get('email_batch_' . $user->hotel_id);
        
        if (!$batchInfo) {
            return response()->json(['status' => 'completed', 'message' => 'No active batch found']);
        }
        
        // Get recent email logs for this batch
        $recentLogs = EmailLog::where('hotel_id', $user->hotel_id)
            ->where('created_at', '>=', $batchInfo['started_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $sentCount = $recentLogs->where('status', 'sent')->count();
        $failedCount = $recentLogs->where('status', 'failed')->count();
        
        $progress = [
            'status' => $batchInfo['status'],
            'started_at' => $batchInfo['started_at']->format('Y-m-d H:i:s'),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'total_members' => $batchInfo['member_count'],
            'batch_size' => $batchInfo['batch_size'],
            'dry_run' => $batchInfo['dry_run'],
            'recent_logs' => $recentLogs->map(function($log) {
                return [
                    'email' => $log->recipient_email,
                    'status' => $log->status,
                    'sent_at' => $log->sent_at?->format('H:i:s'),
                    'error' => $log->error_message
                ];
            })
        ];
        
        return response()->json($progress);
    }
    
    public function stopBatch()
    {
        $user = auth()->user();
        Cache::forget('email_batch_' . $user->hotel_id);
        
        return back()->with('success', 'Email batch stopped successfully.');
    }
    
    public function retryFailed(Request $request)
    {
        $request->validate([
            'email_log_ids' => 'required|array',
            'email_log_ids.*' => 'exists:email_logs,id'
        ]);
        
        $user = auth()->user();
        
        // Get failed email logs
        $failedLogs = EmailLog::whereIn('id', $request->email_log_ids)
            ->where('hotel_id', $user->hotel_id)
            ->where('status', 'failed')
            ->get();
            
        if ($failedLogs->isEmpty()) {
            return back()->withErrors(['error' => 'No failed emails found to retry.']);
        }
        
        // Create a new batch for retrying
        $batchInfo = [
            'started_at' => now(),
            'subject' => 'Retry: ' . $failedLogs->first()->subject,
            'member_count' => $failedLogs->count(),
            'batch_size' => 60,
            'dry_run' => false,
            'status' => 'running',
            'is_retry' => true
        ];
        
        Cache::put('email_batch_' . $user->hotel_id, $batchInfo, 3600);
        
        // Start retry command
        $memberIds = $failedLogs->pluck('member_id')->toArray();
        
        $args = [
            '--subject' => $failedLogs->first()->subject,
            '--content' => $failedLogs->first()->content,
            '--type' => 'retry',
            '--member-ids' => $memberIds,
            '--batch-size' => 60
        ];
        
        $this->runCommandInBackground($args);
        
        return back()->with('success', 'Retry batch started for ' . $failedLogs->count() . ' failed emails.');
    }
    
    private function getMemberCount($memberIds, $resumeFromId)
    {
        $user = auth()->user();
        $query = Member::where('hotel_id', $user->hotel_id);
        
        if ($memberIds) {
            $query->whereIn('id', $memberIds);
        }
        
        if ($resumeFromId) {
            $lastEmailLog = EmailLog::find($resumeFromId);
            if ($lastEmailLog) {
                $query->where('id', '>', $lastEmailLog->member_id);
            }
        }
        
        return $query->count();
    }
    
    private function runCommandInBackground($args)
    {
        // In a real implementation, you'd use a job queue
        // For now, we'll simulate it with a simple command execution
        $command = 'php artisan emails:send-rate-limited';
        
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $command .= " {$key} {$v}";
                }
            } else {
                $command .= " {$key} " . escapeshellarg($value);
            }
        }
        
        // Execute in background
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B " . $command, "r"));
        } else {
            exec($command . " > /dev/null 2>&1 &");
        }
    }
}
