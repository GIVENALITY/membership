<?php

// Simple test to check if events route works
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

// Test if the route is registered
$routes = Route::getRoutes();
$eventRoutes = [];

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'events')) {
        $eventRoutes[] = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName(),
            'controller' => $route->getController()
        ];
    }
}

echo "Event Routes Found:\n";
foreach ($eventRoutes as $route) {
    echo "- {$route['uri']} (" . implode(',', $route['methods']) . ") -> {$route['name']}\n";
    if ($route['controller']) {
        echo "  Controller: " . get_class($route['controller']) . "\n";
    }
    echo "\n";
}
