<?php

// Simple test to check if the route is accessible
echo "Testing route accessibility...\n";

// Test the route pattern
$testUrl = "/public/events/bravo-coco/1/register";
echo "Testing URL: $testUrl\n";

// Check if this matches our route pattern
if (preg_match('/^\/public\/events\/([^\/]+)\/(\d+)\/register$/', $testUrl, $matches)) {
    echo "URL pattern matches!\n";
    echo "Hotel Slug: " . $matches[1] . "\n";
    echo "Event ID: " . $matches[2] . "\n";
} else {
    echo "URL pattern does not match!\n";
}

echo "Test complete.\n";
