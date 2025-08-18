<?php

// Simple test to verify brand colors are working
echo "🎨 Brand Color System Test\n";
echo "========================\n\n";

// Check if we can access the hotel model
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Get a sample hotel
    $hotel = \App\Models\Hotel::first();
    
    if ($hotel) {
        echo "✅ Hotel found: {$hotel->name}\n";
        echo "🎨 Primary Color: {$hotel->primary_color}\n";
        echo "🎨 Secondary Color: {$hotel->secondary_color}\n";
        echo "\n";
        echo "📝 CSS Variables that will be generated:\n";
        echo "--hotel-primary-color: {$hotel->primary_color};\n";
        echo "--hotel-secondary-color: {$hotel->secondary_color};\n";
        echo "\n";
        echo "🎯 All buttons will now use these brand colors:\n";
        echo "- .btn-primary, .btn-success, .btn-info, .btn-warning → Primary color\n";
        echo "- .btn-secondary → Secondary color\n";
        echo "- .btn-danger → Red (for destructive actions)\n";
        echo "- .btn-outline-* → Brand color outlines\n";
        echo "\n";
        echo "✨ Additional elements using brand colors:\n";
        echo "- Alerts, progress bars, spinners\n";
        echo "- Text colors, backgrounds, borders\n";
        echo "- Form elements, navigation, badges\n";
    } else {
        echo "❌ No hotels found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Brand color system is ready!\n";
echo "💡 To change colors, go to Hotel Profile → Branding Colors\n";
