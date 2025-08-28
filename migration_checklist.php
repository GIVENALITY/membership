<?php

/**
 * Domain Migration Verification Script
 * Run this script to verify your domain migration is complete
 */

echo "=== Domain Migration Verification ===\n\n";

// Check 1: Environment Configuration
echo "1. Checking environment configuration...\n";
$envFile = '.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_URL=https://members.co.tz') !== false) {
        echo "   ✅ APP_URL is correctly set to https://members.co.tz\n";
    } else {
        echo "   ❌ APP_URL needs to be updated to https://members.co.tz\n";
    }
} else {
    echo "   ⚠️  .env file not found\n";
}

// Check 2: Application Configuration
echo "\n2. Checking application configuration...\n";
$configFile = 'config/app.php';
if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, "'url' => env('APP_URL', 'https://members.co.tz')") !== false) {
        echo "   ✅ config/app.php is correctly configured\n";
    } else {
        echo "   ❌ config/app.php needs to be updated\n";
    }
}

// Check 3: Hardcoded Domain References
echo "\n3. Checking for hardcoded domain references...\n";
$oldDomain = 'membership.kinara.co.tz';
$newDomain = 'members.co.tz';

$filesToCheck = [
    'resources/views/onboarding/index.blade.php',
    'resources/views/emails/welcome-hotel.blade.php',
    'composer.json'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, $oldDomain) !== false) {
            echo "   ❌ Found old domain in: $file\n";
        } else {
            echo "   ✅ $file is clean\n";
        }
    }
}

// Check 4: Composer Cache Directory
echo "\n4. Checking composer cache directory...\n";
$composerFile = 'composer.json';
if (file_exists($composerFile)) {
    $composerData = json_decode(file_get_contents($composerFile), true);
    if (isset($composerData['config']['cache-dir'])) {
        $cacheDir = $composerData['config']['cache-dir'];
        if (strpos($cacheDir, $newDomain) !== false) {
            echo "   ✅ Composer cache directory is updated\n";
        } else {
            echo "   ❌ Composer cache directory needs updating\n";
        }
    }
}

// Check 5: .htaccess Configuration
echo "\n5. Checking .htaccess configuration...\n";
$htaccessFile = '.htaccess';
if (file_exists($htaccessFile)) {
    $htaccessContent = file_get_contents($htaccessFile);
    if (strpos($htaccessContent, 'RedirectMatch 301 ^/$ https://members.co.tz/') !== false) {
        echo "   ✅ .htaccess redirect is configured\n";
    } else {
        echo "   ❌ .htaccess redirect needs to be updated\n";
    }
}

echo "\n=== Migration Checklist ===\n";
echo "Before going live, ensure you have:\n";
echo "□ Updated DNS records for members.co.tz\n";
echo "□ Configured web server virtual hosts\n";
echo "□ Obtained SSL certificate for members.co.tz\n";
echo "□ Updated external service configurations\n";
echo "□ Tested all functionality on new domain\n";
echo "□ Set up redirects from old subdomain\n";
echo "□ Updated email configurations\n";
echo "□ Cleared application cache\n";
echo "□ Monitored error logs\n";

echo "\n=== Quick Commands ===\n";
echo "Clear Laravel cache:\n";
echo "php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear\n\n";

echo "Test domain resolution:\n";
echo "nslookup members.co.tz\n";
echo "dig members.co.tz\n\n";

echo "Check SSL certificate:\n";
echo "openssl s_client -connect members.co.tz:443 -servername members.co.tz\n\n";

echo "Migration verification complete!\n";
