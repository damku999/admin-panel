<nav class="navbar navbar-expand navbar-light bg-white border-bottom shadow-sm py-2">
    <div class="container-fluid px-3">
        <!-- Left side - Sidebar Toggle -->
        <div class="d-flex align-items-center">
            <!-- Sidebar Toggle (All Devices) -->
            <button id="sidebarToggleTop" class="btn btn-outline-secondary btn-sm me-2" style="border: none;">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Right side - User Profile Only -->
        <ul class="navbar-nav">
            <!-- User Profile Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center py-1 px-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                    <!-- Simple User Avatar -->
                    @if(auth()->user()->profile_photo_path ?? false)
                        <img class="rounded-circle me-2" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" 
                             style="width: 28px; height: 28px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                             style="width: 28px; height: 28px; font-size: 11px; font-weight: 500;">
                            {{ strtoupper(substr(auth()->user()->full_name ?? 'G', 0, 1)) }}
                        </div>
                    @endif
                    
                    <!-- Simple User Name (desktop only) -->
                    <span class="d-none d-md-inline text-dark" style="font-size: 13px; font-weight: 500;">
                        {{ explode(' ', auth()->user()->full_name ?? 'Guest')[0] }}
                    </span>
                </a>
                
                <!-- Simplified Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 200px;">
                    <!-- User Info Header -->
                    <li class="dropdown-header bg-light px-3 py-2">
                        <strong>{{ auth()->user()->full_name ?? 'Guest User' }}</strong><br>
                        <small class="text-muted">{{ auth()->user()->email ?? 'guest@example.com' }}</small>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <!-- Menu Items -->
                    <li><a class="dropdown-item py-2" href="{{ route('profile.detail') }}">
                        <i class="fas fa-user me-2 text-primary"></i> My Profile
                    </a></li>
                    <li><a class="dropdown-item py-2" href="#">
                        <i class="fas fa-cog me-2 text-secondary"></i> Settings
                    </a></li>
                    <li><a class="dropdown-item py-2" href="#">
                        <i class="fas fa-question-circle me-2 text-info"></i> Help
                    </a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <li><a class="dropdown-item py-2 text-danger" href="#" onclick="showLogoutModal()">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
