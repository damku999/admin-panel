<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Brand Logo -->
    <div class="navbar-brand">
        <img src="{{ asset('images/parth_logo.png') }}" style="max-height: 40px;" alt="Company Logo">
    </div>

    <!-- Customer Navigation -->
    <ul class="navbar-nav mr-auto ml-4">
        <li class="nav-item">
            <a class="nav-link text-primary font-weight-bold" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-primary" href="{{ route('customer.policies') }}">
                <i class="fas fa-file-alt"></i> My Policies
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-primary" href="{{ route('customer.profile') }}">
                <i class="fas fa-user"></i> Profile
            </a>
        </li>
    </ul>

    <!-- Customer User Info & Logout -->
    <ul class="navbar-nav ml-auto">
        <!-- Welcome Message -->
        <li class="nav-item d-none d-lg-inline">
            <span class="navbar-text mr-3">
                <small class="text-muted">Welcome back,</small> 
                <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                @if(Auth::guard('customer')->user()->isFamilyHead())
                    <span class="badge badge-success ml-1">Family Head</span>
                @endif
            </span>
        </li>

        <!-- Logout Button -->
        <li class="nav-item">
            <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span class="d-none d-md-inline">Logout</span>
                </button>
            </form>
        </li>
    </ul>

</nav>