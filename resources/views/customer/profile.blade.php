@extends('layouts.customer')

@section('title', 'Customer Profile')

@section('content')
    <div class="container-fluid">
        <!-- Compact Header -->
        <div class="mb-3">
            <h5 class="mb-0 fw-bold">My Profile</h5>
            <small class="text-muted">Account information and settings</small>
        </div>

        <!-- Customer Profile Row -->
        <div class="row">
            <!-- Personal Information -->
            <div class="col-xl-8 col-lg-12">
                <div class="card fade-in mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-id-card me-2"></i>Personal Information
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <div class="info-value">{{ $customer->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <div class="info-value">
                                        {{ $customer->email }}
                                        @if ($customer->hasVerifiedEmail())
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
                                    <div class="info-value">{{ $customer->mobile_number ?? 'Not provided' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-user-tag me-2"></i>Customer Type
                                    </label>
                                    <div class="info-value">
                                        <span
                                            class="badge bg-{{ $customer->type == 'Retail' ? 'info' : 'primary' }} px-3 py-2">
                                            <i
                                                class="fas fa-{{ $customer->type == 'Retail' ? 'user' : 'building' }} me-1"></i>
                                            {{ $customer->type ?? 'Not specified' }}
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
                                        {{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-toggle-on me-2"></i>Account Status
                                    </label>
                                    <div class="info-value">
                                        @if ($customer->status)
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
                                    <div class="info-value">{{ $customer->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-edit me-2"></i>Last Updated
                                    </label>
                                    <div class="info-value">{{ $customer->updated_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($customer->type == 'Retail')
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
                                            <div class="info-value">{{ $customer->getMaskedPanNumber() ?? 'Not provided' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label class="form-label text-brand fw-bold">
                                                <i class="fas fa-id-card me-2"></i>Aadhar Card Number
                                            </label>
                                            <div class="info-value">
                                                {{ $customer->aadhar_card_number ? '****' . substr($customer->aadhar_card_number, -4) : 'Not provided' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($customer->type == 'Corporate')
                            <div class="border-top pt-4 mt-4">
                                <h5 class="text-brand mb-4">
                                    <i class="fas fa-building me-2"></i>Corporate Information
                                </h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label class="form-label text-brand fw-bold">
                                                <i class="fas fa-receipt me-2"></i>GST Number
                                            </label>
                                            <div class="info-value">{{ $customer->gst_number ?? 'Not provided' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4 col-lg-12">
                @if ($familyGroup && $isHead)
                    <!-- Family Members Management (Only for Family Head) -->
                    <div class="card fade-in mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-users-cog me-2"></i>Family Members Management
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            @if ($familyMembers->count() > 1)
                                @foreach ($familyMembers->where('id', '!=', $customer->id) as $member)
                                    <div class="family-member-card mb-3 p-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-white">{{ $member->name }}</h6>
                                                        <small class="text-muted">{{ $member->email }}</small>
                                                        @if ($member->familyMember?->relationship)
                                                            <span
                                                                class="badge bg-secondary ms-2">{{ $member->familyMember->relationship }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="btn-group-vertical w-100" role="group">
                                                    <a href="{{ route('customer.family-member.profile', $member->id) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>View Profile
                                                    </a>
                                                    <a href="{{ route('customer.family-member.change-password', $member->id) }}"
                                                        class="btn btn-outline-warning btn-sm mt-1">
                                                        <i class="fas fa-key me-1"></i>Change Password
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                                    <p class="mb-0">No other family members added yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($familyGroup)
                    <!-- Family Information -->
                    <div class="card fade-in mb-4">
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
                                    <i class="fas fa-crown me-2"></i>Your Role
                                </label>
                                <div class="info-value">
                                    @if ($isHead)
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-crown me-1"></i>Family Head
                                        </span>
                                    @else
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-user me-1"></i>Family Member
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-4">
                                <label class="form-label text-brand fw-bold">
                                    <i class="fas fa-users me-2"></i>Total Members
                                </label>
                                <div class="info-value">
                                    <span class="badge bg-primary px-3 py-2">
                                        {{ $familyMembers->count() }} Members
                                    </span>
                                </div>
                            </div>

                            @if ($customer->familyMember && $customer->familyMember->relationship && !$isHead)
                                <div class="info-item">
                                    <label class="form-label text-brand fw-bold">
                                        <i class="fas fa-heart me-2"></i>Relationship
                                    </label>
                                    <div class="info-value">{{ $customer->familyMember->relationship }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- No Family Group -->
                    <div class="card fade-in mb-4" style="border-left: 4px solid var(--warning-color);">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-users fa-2x text-warning"></i>
                                </div>
                            </div>
                            <h5 class="text-warning mb-3">Not Part of Family Group</h5>
                            <p class="text-muted small mb-0">You are not currently assigned to any family group.</p>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="card fade-in">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.change-password') }}" class="btn btn-webmonks btn-sm">
                                <i class="fas fa-key me-1"></i>Change Password
                            </a>

                            @if (!$customer->hasVerifiedEmail())
                                <form action="{{ route('customer.verification.send') }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="fas fa-envelope me-1"></i>Resend Verification Email
                                    </button>
                                </form>
                                <a href="{{ route('customer.verify-email-notice') }}"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-info-circle me-1"></i>Email Verification Info
                                </a>
                            @endif

                            <a href="{{ route('customer.policies') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-shield-alt me-1"></i>View Policies
                            </a>
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
            transition: all 0.3s ease;
        }

        .family-member-card:hover {
            border-color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.1);
            transform: translateY(-1px);
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .btn-group-vertical .btn+.btn {
            margin-top: 0.5rem;
        }
    </style>
@endsection
