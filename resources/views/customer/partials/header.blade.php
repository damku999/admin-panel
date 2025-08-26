<nav class="navbar navbar-expand-lg navbar-light bg-white topbar mb-4 static-top shadow">
    <style>
        /* Ensure navbar stays on top with proper z-index */
        .navbar {
            position: relative;
            z-index: 1030;
            background-color: #fff !important;
            border-bottom: 1px solid #e3e6f0;
        }
        
        /* Active navigation link styling */
        .navbar-nav .nav-link.active {
            background-color: rgba(78, 115, 223, 0.1);
            border-radius: 5px;
            font-weight: bold !important;
        }
        
        /* Mobile responsive improvements */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding: 1rem 0;
                background-color: #fff;
                border-top: 1px solid #e3e6f0;
                margin-top: 0.5rem;
            }
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.25rem 0;
                text-align: center;
                border-radius: 5px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .navbar-brand img {
                max-height: 35px;
            }
            .navbar-collapse {
                background-color: #fff;
                border-radius: 0.375rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
                margin-top: 0.5rem;
            }
        }
        
        /* Touch-friendly button sizes */
        @media (max-width: 767.98px) {
            .btn-sm {
                min-height: 44px;
                padding: 0.5rem 1rem;
            }
            .navbar-toggler {
                padding: 0.5rem;
                font-size: 1.1rem;
            }
        }
        
        /* Navbar toggler styling */
        .navbar-toggler {
            border: 1px solid rgba(0,0,0,.1);
            border-radius: 0.375rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Ensure content flows below navbar */
        body {
            padding-top: 0;
        }
        
        #content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>

    <div class="container-fluid">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
            <img src="{{ asset('images/parth_logo.png') }}" style="max-height: 40px;" alt="Company Logo">
        </a>

        <!-- Mobile toggle button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Main Navigation Links -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-primary font-weight-bold {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" 
                       href="{{ route('customer.dashboard') }}">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary {{ request()->routeIs('customer.policies*') ? 'active' : '' }}" 
                       href="{{ route('customer.policies') }}">
                        <i class="fas fa-file-alt mr-1"></i> My Policies
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary {{ request()->routeIs('customer.quotations*') ? 'active' : '' }}" 
                       href="{{ route('customer.quotations') }}">
                        <i class="fas fa-calculator mr-1"></i> Quotations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary {{ request()->routeIs('customer.profile*') ? 'active' : '' }}" 
                       href="{{ route('customer.profile') }}">
                        <i class="fas fa-user mr-1"></i> Profile
                    </a>
                </li>
            </ul>

            <!-- User Info & Actions -->
            <ul class="navbar-nav ml-auto">
                <!-- Welcome Message - Desktop -->
                <li class="nav-item d-none d-lg-flex align-items-center">
                    <span class="navbar-text mr-3">
                        <small class="text-muted">Welcome back,</small> 
                        <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                        @if(Auth::guard('customer')->user()->isFamilyHead())
                            <span class="badge badge-success ml-1">Family Head</span>
                        @endif
                    </span>
                </li>

                <!-- Welcome Message - Mobile -->
                <li class="nav-item d-lg-none">
                    <div class="navbar-text text-center py-2">
                        <div class="text-muted small">Welcome,</div>
                        <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                        @if(Auth::guard('customer')->user()->isFamilyHead())
                            <div class="mt-1">
                                <span class="badge badge-success">Family Head</span>
                            </div>
                        @endif
                    </div>
                </li>

                <!-- Logout Button -->
                <li class="nav-item">
                    <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> 
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>