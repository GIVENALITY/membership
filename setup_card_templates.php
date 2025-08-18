<?php

// Simple script to set up card templates directory
$cardTemplatesDir = __DIR__ . '/storage/app/public/card-templates';

if (!is_dir($cardTemplatesDir)) {
    if (mkdir($cardTemplatesDir, 0755, true)) {
        echo "✅ Card templates directory created successfully: {$cardTemplatesDir}\n";
    } else {
        echo "❌ Failed to create card templates directory: {$cardTemplatesDir}\n";
    }
} else {
    echo "✅ Card templates directory already exists: {$cardTemplatesDir}\n";
}

// Also ensure the storage link exists
$publicStorageLink = __DIR__ . '/public/storage';
if (!is_link($publicStorageLink)) {
    echo "⚠️  Storage link not found. Run: php artisan storage:link\n";
} else {
    echo "✅ Storage link exists: {$publicStorageLink}\n";
}

echo "\n🎉 Card template setup complete!\n";
