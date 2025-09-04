@extends('layouts.customer')

@section('title', 'Change Password - ' . $member->name)

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header with back button -->
        <div class="mb-2">
            <div class="d-flex align-items-center">
                <a href="{{ route('customer.family-member.profile', $member->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <div>
                    <h6 class="mb-0 fw-bold">Change {{ $member->name }}'s Password</h6>
                    <small class="text-muted">Family member password management</small>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Family Member Authority Notice -->
                <div class="alert alert-info py-2 px-3 mb-3">
                    <small>
                        <i class="fas fa-users me-2"></i>
                        <strong>Family Access:</strong> Changing password for <strong>{{ $member->name }}</strong> - No current password required.
                    </small>
                </div>

                <!-- Password Change Form -->
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-key me-2"></i>New Password for {{ $member->name }}
                        </h6>
                    </div>
                    <div class="card-body p-3">
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="bg-light rounded p-2">
                                        <small class="text-muted"><i class="fas fa-user me-1"></i>Member</small>
                                        <div class="fw-bold">{{ $member->name }}</div>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light rounded p-2">
                                        <small class="text-muted"><i class="fas fa-heart me-1"></i>Relationship</small>
                                        <div class="fw-bold">{{ $member->familyMember?->relationship ?? 'Not specified' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- New Password Field -->
                            <div class="mb-2">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>New Password
                                </label>
                                <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" 
                                       id="password" name="password" required placeholder="Enter new password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Minimum 8 characters
                                </small>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm Password
                                </label>
                                <input type="password" class="form-control form-control-sm" id="password_confirmation" 
                                       name="password_confirmation" required placeholder="Re-enter password">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Re-enter to confirm
                                </small>
                            </div>

                            <!-- Security Notice -->
                            <div class="alert alert-warning py-2 px-3 mb-3">
                                <small>
                                    <i class="fas fa-shield-alt me-1"></i>
                                    <strong>Note:</strong> This action is logged. {{ $member->name }} must login again with new password.
                                </small>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning btn-sm flex-grow-1">
                                    <i class="fas fa-key me-1"></i>Update Password
                                </button>
                                <a href="{{ route('customer.family-member.profile', $member->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar with family info -->
            <div class="col-lg-2">
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-1"></i>Family Info
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <small class="text-muted"><i class="fas fa-home me-1"></i>Group</small>
                            <div class="fw-bold small">{{ $familyGroup->name }}</div>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted"><i class="fas fa-crown me-1"></i>Head</small>
                            <div class="fw-bold small">{{ $customer->name }}</div>
                        </div>

                        <div>
                            <small class="text-muted"><i class="fas fa-users me-1"></i>Members</small>
                            <div class="fw-bold small">
                                {{ $familyGroup->members ? $familyGroup->members->count() : 0 }}
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