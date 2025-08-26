<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 sticky-top">
    <style>
        .navbar {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95)) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            color: #6b7280 !important;
            border-radius: 10px;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: rgba(79, 70, 229, 0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            color: white !important;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
        }

        .nav-link.active:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(79, 70, 229, 0.4);
        }

        .navbar-toggler {
            border: 2px solid var(--primary-color);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }

        .btn-outline-danger {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        }

        .badge {
            font-size: 0.7em;
            border-radius: 6px;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-top: 1rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            .nav-link {
                text-align: center;
                margin: 0.25rem 0;
            }

            .navbar-text {
                text-align: center;
                margin: 1rem 0;
                padding: 1rem;
                background: rgba(79, 70, 229, 0.05);
                border-radius: 10px;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Brand Logo -->
        @auth('customer')
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <img src="{{ asset('images/parth_logo.png') }}" style="max-height: 40px;" alt="Company Logo">
            </a>
            <!-- Mobile toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation Links -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-bold {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                            href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.policies*') ? 'active' : '' }}"
                            href="{{ route('customer.policies') }}">
                            <i class="fas fa-shield-alt me-2"></i> My Policies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.quotations*') ? 'active' : '' }}"
                            href="{{ route('customer.quotations') }}">
                            <i class="fas fa-calculator me-2"></i> Quotations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}"
                            href="{{ route('customer.profile') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>
                </ul>

                <!-- User Info & Actions -->
                <ul class="navbar-nav ms-auto">
                    <!-- Welcome Message - Desktop -->
                    <li class="nav-item d-none d-lg-flex align-items-center">
                        <span class="navbar-text me-3">
                            <small class="text-muted">Welcome back,</small>
                            <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                            @if (Auth::guard('customer')->user()->isFamilyHead())
                                <span class="badge bg-success ms-1">Family Head</span>
                            @endif
                        </span>
                    </li>

                    <!-- Welcome Message - Mobile -->
                    <li class="nav-item d-lg-none">
                        <div class="navbar-text text-center py-2">
                            <div class="text-muted small">Welcome,</div>
                            <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                            @if (Auth::guard('customer')->user()->isFamilyHead())
                                <div class="mt-1">
                                    <span class="badge bg-success">Family Head</span>
                                </div>
                            @endif
                        </div>
                    </li>

                    <!-- Logout Button -->
                    <li class="nav-item">
                        <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth

    </div>
</nav>
