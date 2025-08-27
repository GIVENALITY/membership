<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class FixInvalidEmails extends Command
{
    protected $signature = 'emails:fix-invalid {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix invalid email addresses in the database';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No changes will be made");
        }

        $this->info("🔧 Fixing invalid email addresses...");

        // Find and fix common email issues
        $fixes = [
            // Missing dots in gmail addresses
            ['pattern' => 'gmailcom', 'replacement' => 'gmail.com'],
            ['pattern' => 'yahooom', 'replacement' => 'yahoo.com'],
            ['pattern' => 'hotmailom', 'replacement' => 'hotmail.com'],
            ['pattern' => 'outlookom', 'replacement' => 'outlook.com'],
            ['pattern' => 'icloudom', 'replacement' => 'icloud.com'],
            
            // Double dots
            ['pattern' => '..', 'replacement' => '.'],
            
            // Spaces in emails
            ['pattern' => ' ', 'replacement' => ''],
        ];

        $totalFixed = 0;
        $totalFound = 0;

        foreach ($fixes as $fix) {
            $members = Member::where('email', 'LIKE', '%' . $fix['pattern'] . '%')->get();
            
            if ($members->isNotEmpty()) {
                $this->info("\n📧 Found " . $members->count() . " emails with pattern: " . $fix['pattern']);
                
                foreach ($members as $member) {
                    $oldEmail = $member->email;
                    $newEmail = str_replace($fix['pattern'], $fix['replacement'], $oldEmail);
                    
                    // Basic email validation
                    if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                        $this->line("  {$oldEmail} → {$newEmail}");
                        
                        if (!$isDryRun) {
                            $member->update(['email' => $newEmail]);
                        }
                        
                        $totalFixed++;
                    } else {
                        $this->warn("  ⚠️  {$oldEmail} → {$newEmail} (still invalid)");
                    }
                    
                    $totalFound++;
                }
            }
        }

        // Specific fixes for known invalid emails
        $specificFixes = [
            'sujith.sairam@gmailcom' => 'sujith.sairam@gmail.com',
            'atibaamalile@gmailcom' => 'atibaamalile@gmail.com',
            'ijoshua888@gmailcom' => 'ijoshua888@gmail.com',
        ];

        $this->info("\n🎯 Applying specific fixes for known invalid emails:");
        
        foreach ($specificFixes as $oldEmail => $newEmail) {
            $member = Member::where('email', $oldEmail)->first();
            
            if ($member) {
                $this->line("  {$oldEmail} → {$newEmail}");
                
                if (!$isDryRun) {
                    $member->update(['email' => $newEmail]);
                }
                
                $totalFixed++;
                $totalFound++;
            } else {
                $this->warn("  ⚠️  {$oldEmail} not found in database");
            }
        }

        // Manual fixes for specific problematic emails
        $manualFixes = [
            'tsilindu@mkwawatanz.com/tsilindu39@yahoo.com' => 'tsilindu39@yahoo.com',
            'rkhamis@206@gmail.com' => 'rkhamis206@gmail.com',
            '..' => '', // This will be handled by the member update
        ];

        $this->info("\n🔧 Applying manual fixes for specific problematic emails:");
        
        foreach ($manualFixes as $oldEmail => $newEmail) {
            $member = Member::where('email', $oldEmail)->first();
            
            if ($member) {
                $this->line("  {$oldEmail} → {$newEmail}");
                
                if (!$isDryRun) {
                    if ($newEmail === '') {
                        // For empty emails, we'll mark them for manual review
                        $this->warn("  ⚠️  {$oldEmail} needs manual review (empty email)");
                    } else {
                        $member->update(['email' => $newEmail]);
                        $totalFixed++;
                    }
                }
                
                $totalFound++;
            }
        }

        // Show invalid emails that couldn't be fixed
        $this->info("\n🔍 Checking for remaining invalid emails:");
        $invalidMembers = Member::whereRaw('email NOT REGEXP "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"')
            ->orWhere('email', '')
            ->orWhereNull('email')
            ->get();
        
        if ($invalidMembers->isNotEmpty()) {
            $this->warn("⚠️  Found " . $invalidMembers->count() . " emails that still need manual fixing:");
            foreach ($invalidMembers as $member) {
                $email = $member->email ?: '(empty)';
                $this->line("  - {$email} (Member: {$member->full_name})");
            }
        } else {
            $this->info("✅ All emails are now valid!");
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("  Total emails found: {$totalFound}");
        $this->info("  Total emails fixed: {$totalFixed}");
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN - No changes were made");
            $this->info("Run without --dry-run to apply the fixes");
        } else {
            $this->info("✅ Email fixes completed!");
        }

        return 0;
    }
}
