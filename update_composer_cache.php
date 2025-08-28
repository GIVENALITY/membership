<?php

/**
 * Script to update composer cache directory for domain migration
 * Run this script after updating your domain configuration
 */

echo "Updating composer cache directory...\n";

// Read the current composer.json
$composerJson = file_get_contents('composer.json');
$composerData = json_decode($composerJson, true);

// Update the cache directory path
if (isset($composerData['config']['cache-dir'])) {
    $oldPath = $composerData['config']['cache-dir'];
    $newPath = str_replace('membership.kinara.co.tz', 'members.co.tz', $oldPath);
    
    $composerData['config']['cache-dir'] = $newPath;
    
    // Write back to composer.json
    $updatedJson = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents('composer.json', $updatedJson);
    
    echo "Updated composer cache directory:\n";
    echo "From: $oldPath\n";
    echo "To: $newPath\n";
} else {
    echo "No cache-dir found in composer.json\n";
}

echo "Done!\n";
echo "\nNext steps:\n";
echo "1. Update your .env file with APP_URL=https://members.co.tz\n";
echo "2. Configure your web server for the new domain\n";
echo "3. Update DNS records\n";
echo "4. Obtain SSL certificate\n";
echo "5. Test the application\n";
