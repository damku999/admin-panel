@extends('layouts.customer')

@section('title', 'My Profile')

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-xl-8 col-lg-12">
                <!-- Profile Header Card -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 0ms">
                    <div class="card-header bg-primary">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white fw-bold">{{ $customer->name }}</h4>
                                <div class="d-flex align-items-center mt-2">
                                    <i class="fas fa-envelope text-white me-2"></i>
                                    <span class="text-white">{{ $customer->email }}</span>
                                    @if ($customer->hasVerifiedEmail())
                                        <span class="badge bg-light text-success ms-2">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </span>
                                    @else
                                        <span class="badge bg-light text-warning ms-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>Not Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Details Card -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 100ms">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-user-circle me-2"></i>Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ $customer->name }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ $customer->email }}
                                    </div>
                                </div>
                            </div>

                            @if($customer->phone)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-phone me-2"></i>Phone Number
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ $customer->phone }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($customer->date_of_birth)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-calendar me-2"></i>Date of Birth
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ formatDateForUi($customer->date_of_birth) }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($customer->address)
                            <div class="col-12">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-map-marker-alt me-2"></i>Address
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ $customer->address }}
                                        @if($customer->city), {{ $customer->city }}@endif
                                        @if($customer->state), {{ $customer->state }}@endif
                                        @if($customer->pincode) - {{ $customer->pincode }}@endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-calendar-plus me-2"></i>Member Since
                                    </label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {{ formatDateForUi($customer->created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Information & Actions -->
            <div class="col-xl-4 col-lg-12">
                @if($familyGroup)
                <!-- Family Information -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 200ms">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-users me-2"></i>Family Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-home fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Family Group</h6>
                                <p class="mb-0 text-muted">{{ $familyGroup->name }}</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-user-friends fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Members</h6>
                                <p class="mb-0 text-muted">{{ $familyMembers->count() }} total members</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($isHead)
                                    <i class="fas fa-crown fa-2x text-warning"></i>
                                @else
                                    <i class="fas fa-user fa-2x text-secondary"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0">Your Role</h6>
                                <p class="mb-0 text-muted">
                                    @if($isHead)
                                        Family Head
                                    @else
                                        Family Member
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($isHead && $familyMembers->count() > 1)
                <!-- Family Members Management (Family Head Only) -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 300ms">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-users-cog me-2"></i>Manage Family Members
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($familyMembers as $member)
                            @if($member->id !== $customer->id)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $member->name }}</h6>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('customer.family-member.profile', $member->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer.family-member.change-password', $member->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Change Password">
                                        <i class="fas fa-key"></i>
                                    </a>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                As family head, you can view profiles and change passwords for all family members.
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 400ms">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.change-password') }}" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>

                            <a href="{{ route('customer.two-factor.index') }}" class="btn btn-success">
                                <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
                            </a>

                            <a href="{{ route('customer.policies') }}" class="btn btn-info">
                                <i class="fas fa-shield-alt me-2"></i>View My Policies
                            </a>

                            <a href="{{ route('customer.quotations') }}" class="btn btn-warning">
                                <i class="fas fa-calculator me-2"></i>View Quotations
                            </a>

                            @if(!$customer->hasVerifiedEmail())
                            <button type="button" class="btn btn-warning" onclick="resendVerification()">
                                <i class="fas fa-envelope-circle-check me-2"></i>Verify Email
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Account Security Status -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 350ms">
                    <div class="card-header bg-success">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-shield-alt me-2"></i>Account Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="text-muted">Account Status</h6>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Email Status</h6>
                                @if ($customer->hasVerifiedEmail())
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="card mb-0 fade-in-scale" style="animation-delay: 500ms">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-chart-pie me-2"></i>Account Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 text-success mb-1">{{ $activePoliciesCount ?? 0 }}</div>
                                    <small class="text-muted">Active Policies</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 text-info mb-1">{{ $quotationsCount ?? 0 }}</div>
                                    <small class="text-muted">Quotations</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Setup Modal -->
<div class="modal fade" id="twoFactorSetupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Up Two-Factor Authentication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="setupModalBody">
                <!-- Setup content will be loaded here -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="disableTwoFactorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will remove the extra security layer from your account.
                </div>
                <form id="disableTwoFactorForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmDisable" name="confirmation" value="1" required>
                        <label class="form-check-label" for="confirmDisable">
                            I understand that this will disable two-factor authentication
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDisableBtn">Disable 2FA</button>
            </div>
        </div>
    </div>
</div>

<!-- Recovery Codes Modal -->
<div class="modal fade" id="recoveryCodesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recovery Codes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="recoveryCodesBody">
                <!-- Recovery codes will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resendVerification() {
        showConfirmationModal(
            'Send Verification Email',
            'Send verification email to {{ $customer->email }}?',
            'primary',
            function() {
                fetch('{{ route("customer.verification.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        show_notification('success', 'Verification email sent successfully!');
                    } else {
                        show_notification('error', 'Failed to send verification email. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    show_notification('error', 'An error occurred. Please try again.');
                });
            }
        );
    }

    // Generic confirmation modal
    function showConfirmationModal(title, message, variant = 'primary', onConfirm = null) {
        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-${variant}" onclick="confirmAction()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        document.getElementById('confirmModal')?.remove();
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        window.confirmModalCallback = onConfirm;
        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    }

    function confirmAction() {
        if (window.confirmModalCallback) {
            bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
            window.confirmModalCallback();
        }
    }

    // Fallback notification function if ui-helpers.js is not loaded
    if (typeof show_notification === 'undefined') {
        function show_notification(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type] && toastr[type](message);
            } else {
                alert(`${type.toUpperCase()}: ${message}`);
            }
        }
    }

    // Add animation to cards
    $(document).ready(function() {
        $('.card').each(function(index) {
            if (!$(this).hasClass('fade-in-scale')) {
                $(this).css('animation-delay', (index * 150) + 'ms').addClass('fade-in-scale');
            }
        });
    });

    // 2FA functionality is handled by the dedicated /customer/two-factor page
</script>
@endpush