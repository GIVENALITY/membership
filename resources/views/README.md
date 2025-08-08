# Laravel Blade Partials Structure

This directory contains Blade partials based on the Materio Bootstrap Admin Template HTML structure.

## File Structure

```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout file
├── partials/
│   ├── header.blade.php        # HTML head section with meta tags and CSS
│   ├── navbar.blade.php        # Top navigation bar
│   ├── sidebar.blade.php       # Left sidebar menu
│   ├── footer.blade.php        # Footer section
│   └── content-wrapper.blade.php # Content area wrapper
└── dashboard.blade.php         # Example dashboard page
```

## How to Use

### 1. Extend the Main Layout

```php
@extends('layouts.app')

@section('title', 'Your Page Title')

@section('content')
    <!-- Your page content here -->
    <div class="row">
        <div class="col-12">
            <h1>Welcome to your page</h1>
        </div>
    </div>
@endsection
```

### 2. Add Page-Specific CSS

```php
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/your-page.css') }}">
@endpush
```

### 3. Add Page-Specific JavaScript

```php
@push('page-js')
<script src="{{ asset('assets/js/your-page.js') }}"></script>
@endpush
```

### 4. Add Custom Head Content

```php
@push('head')
<meta name="custom-meta" content="your value">
@endpush
```

### 5. Add Custom Scripts

```php
@push('scripts')
<script>
    // Your custom JavaScript
</script>
@endpush
```

## Available Sections

- `@section('title')` - Page title
- `@section('description')` - Page description
- `@section('content')` - Main page content
- `@stack('page-css')` - Page-specific CSS
- `@stack('page-js')` - Page-specific JavaScript
- `@stack('head')` - Additional head content
- `@stack('scripts')` - Additional scripts before closing body tag
- `@stack('sidebar-menu')` - Additional sidebar menu items

## Features

### Header Partial (`partials/header.blade.php`)
- HTML5 doctype and meta tags
- Responsive viewport settings
- Font loading (Inter from Google Fonts)
- Core CSS and vendor stylesheets
- JavaScript helpers and config

### Navbar Partial (`partials/navbar.blade.php`)
- Mobile menu toggle
- Search functionality
- Notifications dropdown
- User profile dropdown with logout
- Responsive design

### Sidebar Partial (`partials/sidebar.blade.php`)
- Brand logo and name
- Navigation menu with active states
- Collapsible menu items
- Icon support (Remix Icons)

### Footer Partial (`partials/footer.blade.php`)
- Copyright information
- Theme attribution links
- Responsive layout

### Content Wrapper (`partials/content-wrapper.blade.php`)
- Includes navbar and footer
- Main content area
- Backdrop overlay

## Customization

### Adding Menu Items

To add custom menu items to the sidebar, use the `@stack('sidebar-menu')` in your views:

```php
@push('sidebar-menu')
<li class="menu-item">
    <a href="{{ route('your.route') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-settings-line"></i>
        <div data-i18n="Your Menu">Your Menu</div>
    </a>
</li>
@endpush
```

### Active Menu States

The sidebar automatically highlights active menu items based on route names:
- `request()->routeIs('dashboard')` for dashboard
- `request()->routeIs('users.*')` for user-related pages
- `request()->routeIs('settings.*')` for settings pages

### User Authentication

The navbar includes user authentication features:
- Displays user name and email
- Profile and settings links
- Logout functionality

## Assets

Make sure your assets are properly linked in the `public/assets/` directory:
- CSS files in `public/assets/vendor/css/`
- JavaScript files in `public/assets/vendor/js/`
- Images in `public/assets/img/`

## Dependencies

This template requires:
- Bootstrap 5
- Remix Icons
- Perfect Scrollbar
- ApexCharts (for charts)
- jQuery (for some components)

All dependencies are included in the asset paths and loaded automatically. 