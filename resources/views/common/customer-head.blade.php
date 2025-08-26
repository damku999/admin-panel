<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

    {{-- ICON --}}
    <link rel="shortcut icon" type="image/jpg" href="{{ asset('images/icon.png') }}" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <!-- Custom Customer Portal Styles -->
    <style>
        :root {
            /* WebMonks Brand Colors */
            --primary-color: #20b2aa;
            --primary-dark: #1a9695;
            --primary-light: #4dd4cb;
            --secondary-color: #6b7280;
            --accent-color: #00ced1;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #20b2aa;
            --light-bg: #f0fdfc;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-light: #e2e8f0;
            --card-shadow: 0 1px 3px 0 rgba(32, 178, 170, 0.1), 0 1px 2px 0 rgba(32, 178, 170, 0.06);
            --card-shadow-lg: 0 10px 25px -3px rgba(32, 178, 170, 0.1), 0 4px 6px -2px rgba(32, 178, 170, 0.05);
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            --gradient-accent: linear-gradient(135deg, var(--accent-color), var(--primary-color));
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdfc, #e6fffa);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Compact Card Styles */
        .compact-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(32, 178, 170, 0.15);
            border-top: 4px solid #20b2aa;
            margin: 1rem auto;
        }

        /* WebMonks Button */
        .btn-webmonks {
            background: linear-gradient(135deg, #20b2aa, #1a9695);
            border: none;
            color: white;
            padding: 0.6rem 1rem;
            font-weight: 500;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-webmonks:hover {
            background: linear-gradient(135deg, #1a9695, #178b8a);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);
            color: white;
        }

        /* Compact Welcome Card */
        .compact-welcome-card {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(32, 178, 170, 0.1);
            border-left: 4px solid #20b2aa;
        }

        /* Compact styles */
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 1rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Scroll to Top Button */
        .scroll-to-top {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #20b2aa, #1a9695);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }

        .scroll-to-top:hover {
            background: linear-gradient(135deg, #1a9695, #178b8a);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 20px rgba(32, 178, 170, 0.5);
            color: white;
        }

        .scroll-to-top i {
            font-size: 20px;
        }

        /* Modern Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            background-color: var(--card-bg);
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--card-shadow-lg);
            transform: translateY(-4px);
        }

        .card-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 0 !important;
            border: none;
            font-weight: 600;
        }

        .card-header.bg-success {
            background: linear-gradient(135deg, var(--success-color), #059669) !important;
        }

        .card-header.bg-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706) !important;
        }

        .card-header.bg-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626) !important;
        }

        /* Custom Button Styles */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--gradient-accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);
        }

        .btn-info {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-info:hover {
            background: var(--gradient-accent);
            transform: translateY(-2px);
        }

        /* Modern Table Styles */
        .table {
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .table thead th {
            background: var(--gradient-primary);
            border: none;
            font-weight: 600;
            color: white;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-light);
        }

        .table tbody tr:hover {
            background-color: rgba(32, 178, 170, 0.05);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.5em 1em;
            border-radius: 20px;
            font-size: 0.75em;
        }

        /* Form Styles */
        .form-control {
            border-radius: 10px;
            border: 2px solid var(--border-light);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(32, 178, 170, 0.25);
            transform: translateY(-1px);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left-color: var(--success-color);
            color: #065f46;
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-left-color: var(--warning-color);
            color: #92400e;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left-color: var(--danger-color);
            color: #991b1b;
        }

        .alert-info {
            background-color: rgba(32, 178, 170, 0.1);
            border-left-color: var(--primary-color);
            color: #0f5a56;
        }

        /* Navigation Improvements */
        .navbar {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(32, 178, 170, 0.1);
        }

        /* Auth Pages Styles */
        .auth-card {
            max-width: 450px;
            margin: 2rem auto;
            box-shadow: var(--card-shadow-lg);
        }

        .auth-header {
            background: var(--gradient-primary);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .auth-body {
            padding: 2rem;
        }

        /* Responsive Utilities */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
                border-radius: 12px;
            }
            
            .btn-group .btn {
                margin-bottom: 0.25rem;
            }

            .auth-card {
                margin: 1rem;
                max-width: none;
            }

            .auth-header, .auth-body {
                padding: 1.5rem;
            }
        }

        /* Loading Animation */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Status Indicators */
        .status-active {
            color: var(--success-color);
        }

        .status-warning {
            color: var(--warning-color);
        }

        .status-danger {
            color: var(--danger-color);
        }

        .status-info {
            color: var(--primary-color);
        }

        /* Custom Utilities */
        .text-brand {
            color: var(--primary-color) !important;
        }

        .bg-brand {
            background: var(--gradient-primary) !important;
        }

        .border-brand {
            border-color: var(--primary-color) !important;
        }

        /* Page Header */
        .page-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
    </style>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('admin/toastr/toastr.css') }}">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Date Picker CSS -->
    <link href="{{ asset('datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

    @yield('stylesheets')
</head>