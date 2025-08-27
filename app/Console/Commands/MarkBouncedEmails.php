<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailLog;
use App\Models\Member;
use Carbon\Carbon;

class MarkBouncedEmails extends Command
{
    protected $signature = 'emails:mark-bounced {--dry-run : Show what would be marked without making changes}';
    protected $description = 'Mark specific failed emails from hosting provider as bounced';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No changes will be made");
        }

        $this->info("🔧 Marking failed emails as bounced...");

        // List of failed emails from the hosting provider delivery log
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
            'annette.kanora@gmail.com',
            'okeish44@gmail.com',
            'kimweri.mhita@gmail.com',
            'jazzmanjr@gmail.com',
            'francisdaniel1986@gmail.com',
            'fredrickmtui@gmail.com',
            'kivikehr@gmail.com',
            'albertvenchar@gmail.com',
            'jsheffu@gmail.com',
            'okirubai@gmail.com',
            'mwangafakihi@gmail.com',
            'floraminja@gmail.com',
            'mwanaheritraders@gmail.com',
            'peter.laizer0107@gmail.com',
            'nkyajjnkya@gmail.com',
            'ayzee2000@gmail.com',
            'ayubnuur81@gmail.com',
            'tumbop17@gmail.com',
            'stanleymwangomile143@gmail.com',
            'novartytenga92@gmail.com',
            'laurachittenden@gmail.com',
            'ozgeozturkdb@gmail.com',
            'mponda1family@gmail.com',
            'mikecollin478@gmail.com',
            'dwashalex@gmail.com',
            'mautae32@gmail.com',
            'kyaruzitibawa@gmail.com',
            'jackynjovu@gmail.com',
            'piushenry13@gmail.com',
            'asinaomari@gmail.com',
            'kiluwashein@gmail.com',
            'rennyjoe84@gmail.com',
            'senkoro@gmail.com',
            'givenality@gmail.com',
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

        $this->info("📧 Found " . count($failedEmails) . " failed emails to mark as bounced");

        $markedCount = 0;
        $notFoundCount = 0;

        foreach ($failedEmails as $email) {
            // Find members with this email
            $members = Member::where('email', $email)->get();
            
            if ($members->isEmpty()) {
                $this->warn("⚠️  No members found with email: {$email}");
                $notFoundCount++;
                continue;
            }

            foreach ($members as $member) {
                // Check if there's already a failed email log for this member
                $existingLog = EmailLog::where('member_id', $member->id)
                    ->where('status', 'failed')
                    ->where('created_at', '>=', now()->subDays(7)) // Recent failures
                    ->first();

                if ($existingLog) {
                    if (!$isDryRun) {
                        $existingLog->update([
                            'status' => 'bounced',
                            'bounced_at' => now(),
                            'error_message' => $existingLog->error_message . ' (Marked as bounced from hosting provider failure)'
                        ]);
                    }
                    
                    $this->line("✅ Marked as bounced: {$email} (Member: {$member->full_name})");
                    $markedCount++;
                } else {
                    // Create a new bounced log entry
                    if (!$isDryRun) {
                        EmailLog::create([
                            'hotel_id' => $member->hotel_id,
                            'email_type' => 'member_email',
                            'subject' => 'Previous Email (Bounced)',
                            'content' => 'Email marked as bounced from hosting provider failure',
                            'recipient_email' => $member->email,
                            'recipient_name' => $member->full_name,
                            'member_id' => $member->id,
                            'status' => 'bounced',
                            'bounced_at' => now(),
                            'error_message' => 'Email bounced due to hosting provider rate limit exceeded',
                            'metadata' => [
                                'marked_as_bounced' => true,
                                'original_failure' => 'rate_limit_exceeded',
                                'marked_at' => now()->toISOString()
                            ]
                        ]);
                    }
                    
                    $this->line("✅ Created bounced log: {$email} (Member: {$member->full_name})");
                    $markedCount++;
                }
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("✅ Marked as bounced: {$markedCount}");
        $this->info("⚠️  Not found: {$notFoundCount}");
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN - No changes were made");
            $this->info("Run without --dry-run to apply the changes");
        } else {
            $this->info("✅ Bounced emails marked successfully!");
            $this->info("These members will now appear in the 'Bounced List Members' option in the email composer.");
        }

        return 0;
    }
}
