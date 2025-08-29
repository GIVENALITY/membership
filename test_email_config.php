<?php

/**
 * Test script to verify email configuration
 * Run this from your Laravel application directory
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Email Configuration\n";
echo "==========================\n\n";

// Test 1: Check mail configuration
echo "1. Mail Configuration:\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "   MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "   MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "   MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "   MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Test 2: Check if password is set (don't show the actual password)
$password = config('mail.mailers.smtp.password');
echo "2. Password Status: " . ($password ? "✅ Set" : "❌ Not set") . "\n\n";

// Test 3: Try to send a test email
echo "3. Testing Email Send:\n";
try {
    $testEmail = 'your-test-email@gmail.com'; // Change this to your test email
    
    \Mail::raw('Test email from Laravel application', function($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Test Email - ' . now()->format('Y-m-d H:i:s'));
    });
    
    echo "   ✅ Test email sent successfully!\n";
    echo "   Check your inbox at: $testEmail\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Failed to send test email:\n";
    echo "   Error: " . $e->getMessage() . "\n\n";
    
    // Provide specific troubleshooting tips
    if (strpos($e->getMessage(), '535') !== false) {
        echo "   🔧 Troubleshooting Tips:\n";
        echo "   - Make sure you're using an App Password, not your regular password\n";
        echo "   - Ensure 2-Factor Authentication is enabled for the email account\n";
        echo "   - Verify the email account exists in Google Workspace\n";
        echo "   - Check that the email address is correct\n\n";
    }
}

echo "Test completed!\n";
