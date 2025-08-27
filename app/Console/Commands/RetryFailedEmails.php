<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\EmailLog;
use App\Mail\MemberEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RetryFailedEmails extends Command
{
    protected $signature = 'emails:retry-failed 
                            {--subject= : Email subject}
                            {--content= : Email content}
                            {--batch-size=50 : Number of emails per hour (default: 50)}
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Retry sending emails to the specific failed email addresses';

    private $sentCount = 0;
    private $failedCount = 0;
    private $startTime;
    private $batchSize;

    public function handle()
    {
        $this->startTime = now();
        $this->batchSize = (int) $this->option('batch-size');
        
        $subject = $this->option('subject');
        $content = $this->option('content');
        $isDryRun = $this->option('dry-run');

        if (!$subject || !$content) {
            $this->error("❌ Subject and content are required!");
            $this->info("Usage: php artisan emails:retry-failed --subject='Your Subject' --content='Your content'");
            return 1;
        }

        $user = auth()->user();
        if (!$user || !$user->hotel_id) {
            $this->error("❌ User not authenticated or no hotel associated!");
            return 1;
        }

        // List of failed emails from the delivery log
        $failedEmails = [
            // From 3:06:15 PM
            'marketing@bravococobeach.co.tz',
            
            // From 2:58:14 PM
            'marketing@bravococobeach.co.tz',
            
            // From 2:54:14 PM (Main batch)
            'bellahus@yahoo.com',
            'asinaomari@gmail.com',
            'mlola747@yahoo.com',
            'wrsneder@yahoo.com',
            'dondoglous@yahoo.com',
            'dwashalex@gmail.com',
            'adedoyinadeola@yahoo.com',
            'kyaruzitibawa@gmail.com',
            'claudismlwilo@yahoo.com',
            'piushenry13@gmail.com',
            'laurachittenden@gmail.com',
            'mponda1family@gmail.com',
            'nelson.msuya@pwc.com',
            'hbteri@gmail.com',
            'gmramba@gmail.com',
            'kokubanzajj@gmail.com',
            'cossymakale@gmail.com',
            'abbas.abdurahamani@gmail.com',
            'gfasha@gmail.com',
            'frseif@gmail.com',
            'masalakevin@gmail.com',
            'floryokandju@gmail.com',
            'dmdenisjr5@gmail.com',
            'hkasekende@gmail.com',
            'saad.usangu@gmail.com',
            'tambwedmond@gmail.com',
            'godkessy1@outlook.com',
            'audax.kaasa@gmail.com',
            'raymond.munisi@gmail.com',
            'hans.ezra.teri@gmail.com',
            'elvinkaizer@gmail.com',
            'davidmalisa4@gmail.com',
            'muttajessen@gmail.com',
            'dainafrida@gmail.com',
            'groth@glgroup.co.tz',
            'lockettcl15@gmail.com',
            'anoranthony84@gmail.com',
            'gulamalih786@gmail.com',
            'mobhare@gmail.com',
            'masoudaq@gmail.com',
            'niznurz83@gmail.com',
            'leevanmaro@gmail.com',
            'emaravegas@gmail.com',
            'nanaphillip10@gmail.com',
            'jorgpotreck@gmail.com',
            'sophia.bukha@ports.go.tz',
            'boniphacemagigemanko@gmail.com',
            'noory.larry@gmail.com',
            'asamugabo@gmail.com',
            'irenemkini@gmail.com',
            'mugaisibruce@gmail.com',
            'jameskimario6@gmail.com',
            'ehnhuus@gmail.com',
            'huwaidajumbe@icloud.com',
            'joykalamera7@gmail.com',
            'jacksonkessy@gmail.com',
            'abdulrazaqdullah4@gmail.com',
            'rizmringo65@gmail.com',
            'jamesmbalwe@gmail.com',
            'onyaoro@gmail.com',
            'jnuwamanya@gmail.com',
            'liliageorgieva1@gmail.com',
            'josephine@brainwell.co.tz',
            'daniel@tanin.co.tz',
            'kusazesimon@gmail.com',
            'naftal49@gmail.com',
            'vicentmrema123@gmail.com',
            'elisha.msengi@icloud.com',
            'sheirun.kassam@fmctravel.co.tz',
            'omarykimossa8@gmail.com',
            'ebraebra519@gmail.com',
            'timothy@brainwell.co.tz',
            'nyongo.stanslaus@gmail.com',
            'herve.mugemane@gmail.com',
            'hmusingi@yahoo.com',
            'fatximohamed@gmail.com',
            'careonecoltd@gmail.com',
            'mwandulushabani@gmail.com',
            'donaldrusimbi@gmail.com',
            'monicanaali44@gmail.com',
            'walter.waljay@gmail.com',
            'adeyemi.fajobi@dangote.com',
            'mmbagajohnsstanley50@gmail.com',
            'asmassera@gmail.com',
            'kihampa16@gmail.com',
            'ampungwe@mweb.co.za',
            'shayoevance060@gmail.com',
            'nurummanyi210@gmail.com',
            'russellrourke66@gmail.com',
            'tgkak47@gmail.com',
            'dmwakapanda@gmail.com',
            'bbizine@gmail.com',
            'vivasupermarket38@gmail.com',
            'gsikira@gmail.com',
            'joshuaisaacc@gmail.com',
            'shiwachibya@gmail.com',
            'efsgroup@163.com',
            'mwenesny@gmail.com',
            'priscillazengeni@gmail.com',
            'salumterry@gmail.com',
            'costag777@gmail.com',
            'bbusunzu@gmail.com',
            'mndewa@gmail.com',
            'edward@dolphin.africa',
            'meshacktz04@gmail.com',
            'charles.mtawali12@gmail.com',
            'iskar@hotmail.co.uk',
            'gm@alfazulutravels.co.tz',
            'maveretukai@gmail.com',
            'tthabisi@gmail.com',
            'chenpeiwen921120@icloud.com',
            'louise.kazi07@gmail.com',
            'younousscisse78@gmail.com',
            'lugenge13@gmail.com',
            'maziz@fortris.co.tz',
            
            // From 2:37:14 PM
            'jazzmanjr@gmail.com',
            'jjackson_9@yahoo.com',
            'francisdaniel1986@gmail.com',
            'hans.ezra.teri@gmail.com',
            'gmramba@gmail.com',
            'hkasekende@gmail.com',
            'omar@progressgroup.co.tz',
            'annette.kanora@gmail.com',
            'saad.usangu@gmail.com',
            'okeish44@gmail.com',
            'kimweri.mhita@gmail.com',
            'fredrickmtui@gmail.com',
            'nelson.msuya@pwc.com',
            'floryokandju@gmail.com',
            'tambwedmond@gmail.com',
            'cossymakale@gmail.com',
            'audax.kaasa@gmail.com',
            'gfasha@gmail.com',
            'frseif@gmail.com',
            'masalakevin@gmail.com',
            'dmdenisjr5@gmail.com',
            'godkessy1@outlook.com',
            'kokubanzajj@gmail.com',
            'abbas.abdurahamani@gmail.com',
            'raymond.munisi@gmail.com',
            'hbteri@gmail.com',
            
            // From 2:15:14 PM
            'marketing@bravococobeach.co.tz',
            
            // From 12:55:12 PM
            'gm@bravococobeach.co.tz',
        ];

        $this->info("🔄 Retrying failed emails...");
        $this->info("📧 Found " . count($failedEmails) . " failed emails to retry");
        $this->info("📊 Batch size: {$this->batchSize} emails per hour");
        $this->info("⏰ Started at: " . $this->startTime->format('Y-m-d H:i:s'));

        // Find members with these emails
        $members = Member::where('hotel_id', $user->hotel_id)
            ->whereIn('email', $failedEmails)
            ->get();

        if ($members->isEmpty()) {
            $this->warn("⚠️  No members found with the failed email addresses in your hotel.");
            $this->info("Available members in your hotel:");
            $availableMembers = Member::where('hotel_id', $user->hotel_id)->get(['email', 'first_name', 'last_name']);
            foreach ($availableMembers as $member) {
                $this->line("  - {$member->email} ({$member->first_name} {$member->last_name})");
            }
            return 0;
        }

        $this->info("✅ Found " . $members->count() . " members to retry");
        
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
                            'original_failure' => 'rate_limit_exceeded',
                            'batch_id' => $this->startTime->timestamp
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
                        'original_failure' => 'rate_limit_exceeded',
                        'batch_id' => $this->startTime->timestamp
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
        
        $this->info("📊 Retry Summary:");
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
