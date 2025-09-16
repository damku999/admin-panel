<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Insurance Management System - Admin Panel">
    <meta name="author" content="Insurance Management System">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/jpg" href="{{ asset('images/icon.png') }}" />
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Admin Portal Compiled CSS (includes Bootstrap 5 + SB Admin 2 compatibility) -->
    <link href="{{ url('css/admin.css') }}" rel="stylesheet">
    
    <!-- Minimal Essential Styles -->
    <link href="{{ url('css/admin-minimal.css') }}" rel="stylesheet">
    
    <!-- Modern Close Button Styles -->
    <link href="{{ asset('css/modern-close-button.css') }}" rel="stylesheet">
    
    <!-- Third-party CSS -->
    <link rel="stylesheet" href="{{ asset('admin/toastr/toastr.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Simple Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <!-- Additional page-specific styles -->
    @yield('stylesheets')
    @stack('styles')

    <!-- Performance optimization for critical rendering path -->
    <style>
        /* Critical CSS for above-the-fold content */
        .sidebar { transition: width 0.3s ease; }
        .topbar { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        
        /* Simple Date Picker Calendar Icon */
        .form-control.datepicker {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%236c757d' viewBox='0 0 16 16'%3e%3cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }
    </style>
</head>
