<!doctype html>
<html
  lang="{{ app()->getLocale() }}"
  class="layout-menu-fixed layout-compact"
  data-assets-path="{{ asset('assets/') }}"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

    <title>@yield('title', 'Dashboard') - Membership MS</title>

    <meta name="description" content="@yield('description', '')" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->
    @stack('page-css')

    <!-- Hotel Branding CSS -->
    <style>
        :root {
            --hotel-primary-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }};
            --hotel-secondary-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->secondary_color ?? '#6c757d') : '#6c757d' }};
        }
        
        /* Apply hotel branding colors */
        .btn-primary {
            background-color: var(--hotel-primary-color) !important;
            border-color: var(--hotel-primary-color) !important;
        }
        
        .btn-primary:hover {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }}dd !important;
            border-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }}dd !important;
        }
        
        .text-primary {
            color: var(--hotel-primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--hotel-primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--hotel-primary-color) !important;
        }
        
        .bg-label-primary {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }}15 !important;
            color: var(--hotel-primary-color) !important;
        }
        
        .badge.bg-primary {
            background-color: var(--hotel-primary-color) !important;
        }
        
        .nav-link.active {
            background-color: var(--hotel-primary-color) !important;
        }
        
        .menu-item.active > .menu-link {
            background-color: var(--hotel-primary-color) !important;
        }
        
        .form-check-input:checked {
            background-color: var(--hotel-primary-color) !important;
            border-color: var(--hotel-primary-color) !important;
        }
        
        .form-control:focus {
            border-color: var(--hotel-primary-color) !important;
            box-shadow: 0 0 0 0.2rem {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }}40 !important;
        }
        
        .form-select:focus {
            border-color: var(--hotel-primary-color) !important;
            box-shadow: 0 0 0 0.2rem {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#000000') : '#000000' }}40 !important;
        }
        
        .page-link {
            color: var(--hotel-primary-color) !important;
        }
        
        .page-item.active .page-link {
            background-color: var(--hotel-primary-color) !important;
            border-color: var(--hotel-primary-color) !important;
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    
    <!-- Config: Mandatory theme config file contain global vars & default theme options -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @stack('head')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container"> 