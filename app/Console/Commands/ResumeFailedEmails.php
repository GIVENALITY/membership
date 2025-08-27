<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailLog;
use App\Models\Member;
use App\Mail\MemberEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ResumeFailedEmails extends Command
{
    protected $signature = 'emails:resume-failed 
                            {--subject= : Email subject}
                            {--content= : Email content}
                            {--batch-size=60 : Number of emails per hour}';

    protected $description = 'Resume sending emails from the failed ones (based on the screenshot)';

    public function handle()
    {
        $subject = $this->option('subject');
        $content = $this->option('content');
        $batchSize = (int) $this->option('batch-size');

        if (!$subject || !$content) {
            $this->error("❌ Subject and content are required!");
            $this->info("Usage: php artisan emails:resume-failed --subject='Your Subject' --content='Your content'");
            return 1;
        }

        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            $this->error("❌ User not authenticated or no hotel associated!");
            return 1;
        }

        // Based on the screenshot, these are the emails that failed
        $failedEmails = [
            'md@kamal-group.co.tz',
            'haidarkavira@gmail.com', 
            'venance@mirumbani.com',
            'mustafa@361africa.com',
            'mr_manyanga@yahoo.com',
            'xiaoyu.catic@gmail.com',
            'neghestiluca@gmail.com'
        ];

        $this->info("🔄 Resuming failed emails...");
        $this->info("📧 Found " . count($failedEmails) . " failed emails to retry");
        $this->info("📊 Batch size: {$batchSize} emails per hour");

        // Find members with these emails
        $members = Member::where('hotel_id', $user->hotel_id)
            ->whereIn('email', $failedEmails)
            ->get();

        if ($members->isEmpty()) {
            $this->warn("⚠️  No members found with the failed email addresses.");
            $this->info("Available members in your hotel:");
            $availableMembers = Member::where('hotel_id', $user->hotel_id)->get(['email', 'first_name', 'last_name']);
            foreach ($availableMembers as $member) {
                $this->line("  - {$member->email} ({$member->first_name} {$member->last_name})");
            }
            return 0;
        }

        $this->info("✅ Found " . $members->count() . " members to retry");

        // Create progress bar
        $progressBar = $this->output->createProgressBar($members->count());
        $progressBar->start();

        $batchStartTime = now();
        $batchCount = 0;
        $sentCount = 0;
        $failedCount = 0;

        foreach ($members as $member) {
            try {
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
                    'email_type' => 'retry',
                    'subject' => $subject,
                    'content' => $content,
                    'recipient_email' => $member->email,
                    'recipient_name' => $member->full_name,
                    'member_id' => $member->id,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'metadata' => [
                        'retry_batch' => true,
                        'original_failure' => 'rate_limit_exceeded'
                    ]
                ]);

                $sentCount++;
                $batchCount++;

                // Check if we need to pause for rate limiting
                if ($batchCount >= $batchSize) {
                    $progressBar->advance();
                    $this->pauseForRateLimit($batchStartTime);
                    $batchStartTime = now();
                    $batchCount = 0;
                }

                $progressBar->advance();

            } catch (\Exception $e) {
                $failedCount++;
                
                // Log the error
                EmailLog::create([
                    'hotel_id' => $member->hotel_id,
                    'email_type' => 'retry',
                    'subject' => $subject,
                    'content' => $content,
                    'recipient_email' => $member->email,
                    'recipient_name' => $member->full_name,
                    'member_id' => $member->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                    'metadata' => [
                        'retry_batch' => true,
                        'original_failure' => 'rate_limit_exceeded'
                    ]
                ]);

                $this->error("\n❌ Failed to send to {$member->email}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("📊 Resume Summary:");
        $this->info("✅ Successfully sent: {$sentCount}");
        $this->info("❌ Failed: {$failedCount}");
        $this->info("📈 Rate: " . round($sentCount / max(1, $batchStartTime->diffInMinutes(now()) / 60), 2) . " emails per hour");
        
        return 0;
    }

    private function pauseForRateLimit($batchStartTime)
    {
        $elapsed = $batchStartTime->diffInSeconds(now());
        $requiredDelay = 3600; // 1 hour in seconds
        
        if ($elapsed < $requiredDelay) {
            $remainingDelay = $requiredDelay - $elapsed;
            
            $this->newLine();
            $this->warn("⏸️  Rate limit reached. Pausing for " . gmdate('H:i:s', $remainingDelay));
            
            // Show countdown
            for ($i = $remainingDelay; $i > 0; $i--) {
                $this->write("\r⏳ Resuming in " . gmdate('H:i:s', $i) . "    ");
                sleep(1);
            }
            
            $this->newLine();
        }
    }
}
