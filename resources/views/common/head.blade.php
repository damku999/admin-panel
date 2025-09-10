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
    
    <!-- Third-party CSS -->
    <link rel="stylesheet" href="{{ asset('admin/toastr/toastr.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

    <!-- Additional page-specific styles -->
    @yield('stylesheets')
    
    <!-- Performance optimization for critical rendering path -->
    <style>
        /* Critical CSS for above-the-fold content */
        .sidebar { transition: width 0.3s ease; }
        .topbar { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
    </style>
</head>
