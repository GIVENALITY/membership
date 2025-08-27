<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailLog;
use App\Models\Member;
use App\Mail\MemberEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendRateLimitedEmails extends Command
{
    protected $signature = 'emails:send-rate-limited 
                            {--type= : Email type (welcome, custom, etc.)}
                            {--subject= : Email subject}
                            {--content= : Email content}
                            {--member-ids=* : Specific member IDs to send to}
                            {--resume-from= : Resume from specific email log ID}
                            {--batch-size=60 : Number of emails per hour (default: 60)}
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send emails with rate limiting (60 per hour) and progress tracking';

    private $sentCount = 0;
    private $failedCount = 0;
    private $startTime;
    private $batchSize;

    public function handle()
    {
        $this->startTime = now();
        $this->batchSize = (int) $this->option('batch-size');
        
        $this->info("🚀 Starting rate-limited email sending...");
        $this->info("📊 Batch size: {$this->batchSize} emails per hour");
        $this->info("⏰ Started at: " . $this->startTime->format('Y-m-d H:i:s'));
        
        // Get email parameters
        $type = $this->option('type') ?: 'custom';
        $subject = $this->option('subject');
        $content = $this->option('content');
        $memberIds = $this->option('member-ids');
        $resumeFromId = $this->option('resume-from');
        $isDryRun = $this->option('dry-run');

        if (!$subject || !$content) {
            $this->error("❌ Subject and content are required!");
            return 1;
        }

        // Get members to send to
        $members = $this->getMembers($memberIds, $resumeFromId);
        
        if ($members->isEmpty()) {
            $this->warn("⚠️  No members found to send emails to.");
            return 0;
        }

        $this->info("📧 Found {$members->count()} members to process");
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No emails will be sent");
        }

        // Create progress bar
        $progressBar = $this->output->createProgressBar($members->count());
        $progressBar->start();

        $batchStartTime = now();
        $batchCount = 0;

        foreach ($members as $member) {
            try {
                if (!$isDryRun) {
                    // Send the email
                    Mail::to($member->email)->send(new MemberEmail([
                        'subject' => $subject,
                        'content' => $content,
                        'hotel_name' => $member->hotel->name ?? 'Hotel',
                        'sent_at' => now()
                    ], $member));

                    // Log the email
                    EmailLog::create([
                        'hotel_id' => $member->hotel_id,
                        'email_type' => $type,
                        'subject' => $subject,
                        'content' => $content,
                        'recipient_email' => $member->email,
                        'recipient_name' => $member->full_name,
                        'member_id' => $member->id,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'metadata' => [
                            'batch_id' => $this->startTime->timestamp,
                            'batch_position' => $this->sentCount + 1
                        ]
                    ]);

                    $this->sentCount++;
                } else {
                    $this->sentCount++;
                }

                $batchCount++;

                // Check if we need to pause for rate limiting
                if ($batchCount >= $this->batchSize) {
                    $progressBar->advance();
                    $this->pauseForRateLimit($batchStartTime, $progressBar);
                    $batchStartTime = now();
                    $batchCount = 0;
                }

                $progressBar->advance();

            } catch (\Exception $e) {
                $this->failedCount++;
                
                // Log the error
                EmailLog::create([
                    'hotel_id' => $member->hotel_id,
                    'email_type' => $type,
                    'subject' => $subject,
                    'content' => $content,
                    'recipient_email' => $member->email,
                    'recipient_name' => $member->full_name,
                    'member_id' => $member->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                    'metadata' => [
                        'batch_id' => $this->startTime->timestamp,
                        'batch_position' => $this->sentCount + 1
                    ]
                ]);

                $this->error("\n❌ Failed to send to {$member->email}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        
        $this->newLine(2);
        $this->showSummary($isDryRun);
        
        return 0;
    }

    private function getMembers($memberIds, $resumeFromId)
    {
        $user = auth()->user();
        $query = Member::where('hotel_id', $user->hotel_id);

        if ($memberIds) {
            $query->whereIn('id', $memberIds);
        }

        if ($resumeFromId) {
            // Resume from a specific email log
            $lastEmailLog = EmailLog::find($resumeFromId);
            if ($lastEmailLog) {
                $this->info("🔄 Resuming from email log ID: {$resumeFromId}");
                $this->info("📧 Last sent to: {$lastEmailLog->recipient_email}");
                
                // Get members after the last sent member
                $query->where('id', '>', $lastEmailLog->member_id);
            }
        }

        return $query->orderBy('id')->get();
    }

    private function pauseForRateLimit($batchStartTime, $progressBar)
    {
        $elapsed = $batchStartTime->diffInSeconds(now());
        $requiredDelay = 3600; // 1 hour in seconds
        
        if ($elapsed < $requiredDelay) {
            $remainingDelay = $requiredDelay - $elapsed;
            
            $this->newLine();
            $this->warn("⏸️  Rate limit reached. Pausing for " . gmdate('H:i:s', $remainingDelay));
            
            $progressBar->clear();
            
            // Show countdown
            for ($i = $remainingDelay; $i > 0; $i--) {
                $this->write("\r⏳ Resuming in " . gmdate('H:i:s', $i) . "    ");
                sleep(1);
            }
            
            $this->newLine();
            $progressBar->display();
        }
    }

    private function showSummary($isDryRun)
    {
        $endTime = now();
        $duration = $this->startTime->diffInMinutes($endTime);
        
        $this->info("📊 Email Sending Summary:");
        $this->info("⏱️  Duration: {$duration} minutes");
        $this->info("📧 Emails processed: " . ($this->sentCount + $this->failedCount));
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN - {$this->sentCount} emails would have been sent");
        } else {
            $this->info("✅ Successfully sent: {$this->sentCount}");
            $this->info("❌ Failed: {$this->failedCount}");
        }
        
        $this->info("📈 Rate: " . round($this->sentCount / max(1, $duration / 60), 2) . " emails per hour");
    }
}
