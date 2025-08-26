@extends('layouts.customer')

@section('title', 'Change Password - ' . $member->name)

@section('content')
    <div class="container-fluid">
        <!-- Header with back button -->
        <div class="mb-3">
            <div class="d-flex align-items-center">
                <a href="{{ route('customer.family-member.profile', $member->id) }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="fas fa-arrow-left me-1"></i>Back to {{ $member->name }}'s Profile
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">Change {{ $member->name }}'s Password</h5>
                    <small class="text-muted">As family head, you can change family member passwords without their current password</small>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Family Head Authority Notice -->
                <div class="alert alert-warning d-flex align-items-center mb-4">
                    <i class="fas fa-crown me-3 fa-lg"></i>
                    <div>
                        <strong>Family Head Authority:</strong> You are changing the password for your family member 
                        <strong>{{ $member->name }}</strong>. No current password verification is required for family head actions.
                    </div>
                </div>

                <!-- Password Change Form -->
                <div class="card fade-in">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-key me-2"></i>New Password for {{ $member->name }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Display alerts -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer.family-member.update-password', $member->id) }}">
                            @csrf
                            
                            <!-- Member Info Display -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-display p-3 bg-light rounded">
                                        <label class="form-label text-brand fw-bold">
                                            <i class="fas fa-user me-2"></i>Family Member
                                        </label>
                                        <div class="fw-bold">{{ $member->name }}</div>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-display p-3 bg-light rounded">
                                        <label class="form-label text-brand fw-bold">
                                            <i class="fas fa-heart me-2"></i>Relationship
                                        </label>
                                        <div class="fw-bold">{{ $member->familyMember?->relationship ?? 'Not specified' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- New Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label text-brand fw-bold">
                                    <i class="fas fa-lock me-2"></i>New Password
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Password must be at least 8 characters long
                                </small>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label text-brand fw-bold">
                                    <i class="fas fa-lock me-2"></i>Confirm New Password
                                </label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Please re-enter the password to confirm
                                </small>
                            </div>

                            <!-- Security Notice -->
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-shield-alt me-2"></i>
                                <strong>Security Notice:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>This action will be logged in the audit trail for both you and {{ $member->name }}</li>
                                    <li>{{ $member->name }} will be required to log in again with the new password</li>
                                    <li>Any active sessions for {{ $member->name }} will be terminated</li>
                                    <li>The "must change password" flag will be reset for this member</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-key me-2"></i>Change {{ $member->name }}'s Password
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('customer.family-member.profile', $member->id) }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar with family info -->
            <div class="col-lg-4">
                <div class="card fade-in">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Family Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item mb-4">
                            <label class="form-label text-brand fw-bold">
                                <i class="fas fa-home me-2"></i>Family Group
                            </label>
                            <div class="info-value">{{ $familyGroup->name }}</div>
                        </div>
                        
                        <div class="info-item mb-4">
                            <label class="form-label text-brand fw-bold">
                                <i class="fas fa-crown me-2"></i>Family Head
                            </label>
                            <div class="info-value">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-crown me-1"></i>{{ $customer->name }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <label class="form-label text-brand fw-bold">
                                <i class="fas fa-users me-2"></i>Total Members
                            </label>
                            <div class="info-value">
                                <span class="badge bg-primary px-3 py-2">
                                    {{ $familyGroup->members->count() }} Members
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add custom styles -->
    <style>
        .info-item {
            margin-bottom: 1.5rem;
        }
        
        .info-item .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .info-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 500;
            padding: 0.75rem 1rem;
            background-color: var(--light-bg);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .info-display {
            border-left: 3px solid var(--primary-color);
        }
    </style>
@endsection