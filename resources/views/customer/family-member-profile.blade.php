@extends('layouts.customer')

@section('title', 'Family Member Profile - ' . $member->name)

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header with back button -->
        <div class="mb-2">
            <div class="d-flex align-items-center">
                <a href="{{ route('customer.profile') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $member->name }}'s Profile</h6>
                    <small class="text-muted">Family member view</small>
                </div>
            </div>
        </div>

        <!-- Readonly Notice -->
        <div class="alert alert-info py-2 px-3 mb-3">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                <strong>Read-Only:</strong> Family member profile - view only access.
            </small>
        </div>

        <!-- Family Member Profile -->
        <div class="row">
            <!-- Personal Information -->
            <div class="col-xl-10 col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-1"></i>Personal Information
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <div class="info-value">{{ $member->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <div class="info-value">
                                        {{ $member->email }}
                                        @if($member->hasVerifiedEmail())
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-check-circle me-1"></i>Verified
                                            </span>
                                        @else
                                            <span class="badge bg-warning ms-2">
                                                <i class="fas fa-exclamation-circle me-1"></i>Not Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-phone me-2"></i>Mobile Number
                                    </label>
                                    <div class="info-value">{{ $member->mobile_number ?? 'Not provided' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-heart me-2"></i>Relationship
                                    </label>
                                    <div class="info-value">
                                        <span class="badge bg-info px-3 py-2">
                                            {{ $member->familyMember?->relationship ?? 'Not specified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-birthday-cake me-2"></i>Date of Birth
                                    </label>
                                    <div class="info-value">
                                        {{ $member->date_of_birth ? $member->date_of_birth->format('d M Y') : 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-toggle-on me-2"></i>Account Status
                                    </label>
                                    <div class="info-value">
                                        @if($member->status)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-calendar-plus me-2"></i>Member Since
                                    </label>
                                    <div class="info-value">{{ $member->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-edit me-2"></i>Last Updated
                                    </label>
                                    <div class="info-value">{{ $member->updated_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>

                        @if($member->type == 'Retail')
                        <div class="border-top pt-4 mt-4">
                            <h5 class="text-brand mb-4">
                                <i class="fas fa-id-badge me-2"></i>Identity Documents
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="form-label text-brand fw-bold">
                                            <i class="fas fa-credit-card me-2"></i>PAN Card Number
                                        </label>
                                        <div class="info-value">{{ $member->getMaskedPanNumber() ?? 'Not provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="form-label text-brand fw-bold">
                                            <i class="fas fa-id-card me-2"></i>Aadhar Card Number
                                        </label>
                                        <div class="info-value">
                                            {{ $member->aadhar_card_number ? '****' . substr($member->aadhar_card_number, -4) : 'Not provided' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Management Actions Sidebar -->
            <div class="col-xl-2 col-lg-12">
                <!-- Family Actions -->
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-tools me-1"></i>Actions
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="d-grid gap-1">
                            <a href="{{ route('customer.family-member.change-password', $member->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-key me-1"></i>Change Password
                            </a>
                            
                            @if(!$member->hasVerifiedEmail())
                                <button class="btn btn-outline-info btn-sm" disabled>
                                    <i class="fas fa-envelope me-1"></i>Not Verified
                                </button>
                            @endif
                            
                            <a href="{{ route('customer.policies') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-shield-alt me-1"></i>Policies
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Family Information -->
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
                                    {{ $familyGroup->members ? $familyGroup->members->count() : 0 }} Members
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add custom styles for info items -->
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

        .family-member-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color) !important;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
        }
    </style>
@endsection