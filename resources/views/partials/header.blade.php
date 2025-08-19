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

    <!-- Restaurant Branding CSS -->
    <style>
        :root {
            --restaurant-primary-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }};
            --restaurant-secondary-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->secondary_color ?? '#6c757d') : '#6c757d' }};
        }
        
        /* Apply restaurant branding colors to all buttons */
        .btn-primary,
        .btn-success,
        .btn-info,
        .btn-warning {
            background-color: var(--restaurant-primary-color) !important;
            border-color: var(--restaurant-primary-color) !important;
            color: white !important;
        }
        
        .btn-primary:hover,
        .btn-success:hover,
        .btn-info:hover,
        .btn-warning:hover {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}dd !important;
            border-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}dd !important;
        }
        
        .btn-outline-primary,
        .btn-outline-success,
        .btn-outline-info,
        .btn-outline-warning {
            color: var(--restaurant-primary-color) !important;
            border-color: var(--restaurant-primary-color) !important;
            background-color: transparent !important;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-info:hover,
        .btn-outline-warning:hover {
            background-color: var(--restaurant-primary-color) !important;
            border-color: var(--restaurant-primary-color) !important;
            color: white !important;
        }
        
        /* Secondary buttons use the secondary color */
        .btn-secondary {
            background-color: var(--restaurant-secondary-color) !important;
            border-color: var(--restaurant-secondary-color) !important;
            color: white !important;
        }
        
        .btn-secondary:hover {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->secondary_color ?? '#6c757d') : '#6c757d' }}dd !important;
            border-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->secondary_color ?? '#6c757d') : '#6c757d' }}dd !important;
        }
        
        .btn-outline-secondary {
            color: var(--restaurant-secondary-color) !important;
            border-color: var(--restaurant-secondary-color) !important;
            background-color: transparent !important;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--restaurant-secondary-color) !important;
            border-color: var(--restaurant-secondary-color) !important;
            color: white !important;
        }
        
        /* Danger buttons keep red color for destructive actions */
        .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .btn-danger:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
        }
        
        .btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
            background-color: transparent !important;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .text-primary {
            color: var(--restaurant-primary-color) !important;
        }
        
        .text-secondary {
            color: var(--restaurant-secondary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--restaurant-primary-color) !important;
        }
        
        .bg-secondary {
            background-color: var(--restaurant-secondary-color) !important;
        }
        
        .border-primary {
            border-color: var(--restaurant-primary-color) !important;
        }
        
        .border-secondary {
            border-color: var(--restaurant-secondary-color) !important;
        }
        
        .bg-label-primary {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}15 !important;
            color: var(--restaurant-primary-color) !important;
        }
        
        .bg-label-secondary {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->secondary_color ?? '#6c757d') : '#6c757d' }}15 !important;
            color: var(--restaurant-secondary-color) !important;
        }
        
        .badge.bg-primary {
            background-color: var(--restaurant-primary-color) !important;
        }
        
        .badge.bg-secondary {
            background-color: var(--restaurant-secondary-color) !important;
        }
        
        .nav-link.active {
            background-color: var(--restaurant-primary-color) !important;
        }
        
        .menu-item.active > .menu-link {
            background-color: var(--restaurant-primary-color) !important;
        }
        
        /* Override the default template gradient with brand colors */
        .menu-item.active > .menu-link:not(.menu-toggle) {
            background: linear-gradient(270deg, var(--restaurant-primary-color) 0%, color-mix(in sRGB, var(--restaurant-primary-color) 52%, var(--bs-white)) 100%) !important;
            box-shadow: var(--bs-box-shadow-sm) !important;
            color: var(--bs-menu-active-color) !important;
        }
        
        .form-check-input:checked {
            background-color: var(--restaurant-primary-color) !important;
            border-color: var(--restaurant-primary-color) !important;
        }
        
        .form-control:focus {
            border-color: var(--restaurant-primary-color) !important;
            box-shadow: 0 0 0 0.2rem {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}40 !important;
        }
        
        .form-select:focus {
            border-color: var(--restaurant-primary-color) !important;
            box-shadow: 0 0 0 0.2rem {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}40 !important;
        }
        
        .page-link {
            color: var(--restaurant-primary-color) !important;
        }
        
        .page-item.active .page-link {
            background-color: var(--restaurant-primary-color) !important;
            border-color: var(--restaurant-primary-color) !important;
        }
        
        /* Additional brand color applications */
        .alert-primary {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}15 !important;
            border-color: var(--restaurant-primary-color) !important;
            color: var(--restaurant-primary-color) !important;
        }
        
        .alert-info {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}15 !important;
            border-color: var(--restaurant-primary-color) !important;
            color: var(--restaurant-primary-color) !important;
        }
        
        .alert-success {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}15 !important;
            border-color: var(--restaurant-primary-color) !important;
            color: var(--restaurant-primary-color) !important;
        }
        
        .alert-warning {
            background-color: {{ Auth::check() && Auth::user()->hotel ? (Auth::user()->hotel->primary_color ?? '#007bff') : '#007bff' }}15 !important;
            border-color: var(--restaurant-primary-color) !important;
            color: var(--restaurant-primary-color) !important;
        }
        
        .progress-bar {
            background-color: var(--restaurant-primary-color) !important;
        }
        
        .spinner-border.text-primary {
            color: var(--restaurant-primary-color) !important;
        }
        
        .spinner-grow.text-primary {
            color: var(--restaurant-primary-color) !important;
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