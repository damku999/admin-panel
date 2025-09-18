@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profile Management</h4>
                <p class="text-muted mb-0">Manage your account settings and security preferences</p>
            </div>
        </div>

        {{-- Profile Overview Card --}}
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Profile Overview</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="d-flex flex-column align-items-center">
                            <div class="profile-avatar mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                     style="width: 100px; height: 100px; font-size: 2.5rem; color: white;">
                                    {{ strtoupper(substr(auth()->user()->first_name ?? 'G', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <h5 class="fw-bold mb-1">{{ auth()->user()->full_name ?? 'Guest User' }}</h5>
                            <span class="badge bg-primary mb-2">
                                <i class="fas fa-user-tag me-1"></i>
                                {{ auth()->user()->roles ? auth()->user()->roles->pluck('name')->first() : 'N/A' }}
                            </span>
                            <div class="text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                {{ auth()->user()->email }}
                            </div>
                            @if(auth()->user()->mobile_number)
                                <div class="text-muted mt-1">
                                    <i class="fas fa-phone me-1"></i>
                                    {{ auth()->user()->mobile_number }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="col-lg-8 col-md-6 mb-4">
                <div class="row h-100">
                    <div class="col-md-6 mb-3">
                        <div class="card card-metric card-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="metric-label">Account Status</div>
                                        <div class="metric-value" style="font-size: 1.5rem;">Active</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-check-circle fa-2x text-info opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card card-metric card-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="metric-label">Member Since</div>
                                        <div class="metric-value" style="font-size: 1.5rem;">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M Y') : 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-plus fa-2x text-success opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card card-metric card-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="metric-label">Last Login</div>
                                        <div class="metric-value" style="font-size: 1.2rem;">{{ auth()->user()->last_login ? auth()->user()->last_login->diffForHumans() : 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card card-metric card-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="metric-label">Profile Completion</div>
                                        <div class="metric-value" style="font-size: 1.5rem;">
                                            {{ auth()->user()->mobile_number ? '100%' : '75%' }}
                                        </div>
                                    </div>
                                    <div>
                                        <i class="fas fa-chart-pie fa-2x text-primary opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Update Form --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Profile Information</h5>
                        <p class="text-muted mb-0 mt-1">Keep your personal information up to date</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>First Name
                                    </label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" placeholder="Enter your first name"
                                        value="{{ old('first_name') ? old('first_name') : auth()->user()->first_name }}">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>Last Name
                                    </label>
                                    <input type="text" name="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name') ? old('last_name') : auth()->user()->last_name }}"
                                        placeholder="Enter your last name">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-mobile-alt me-1"></i>Mobile Number
                                    </label>
                                    <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                                        name="mobile_number"
                                        value="{{ old('mobile_number') ? old('mobile_number') : auth()->user()->mobile_number }}"
                                        placeholder="Enter your mobile number">
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-save me-1"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password Form --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security Settings</h5>
                        <p class="text-muted mb-0 mt-1">Update your password to keep your account secure</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.change-password') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Current Password
                                    </label>
                                    <input type="password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Enter current password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-key me-1"></i>New Password
                                    </label>
                                    <input type="password" name="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror" required
                                        placeholder="Enter new password">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-check-double me-1"></i>Confirm Password
                                    </label>
                                    <input type="password" name="new_confirm_password"
                                        class="form-control @error('new_confirm_password') is-invalid @enderror" required
                                        placeholder="Confirm new password">
                                    @error('new_confirm_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-shield-alt me-1"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
