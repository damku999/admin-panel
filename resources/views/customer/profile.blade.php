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

                            <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#customerTwoFactorSection">
                                <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
                            </button>

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

                <!-- Two-Factor Authentication Section (Collapsible) -->
                <div class="collapse" id="customerTwoFactorSection">
                    <div class="card mb-4 bg-light fade-in-scale">
                        <div class="card-header bg-success">
                            <h5 class="mb-0 text-white fw-bold">
                                <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $user = Auth::guard('customer')->user();
                                $status = app(\App\Services\TwoFactorAuthService::class)->getTwoFactorStatus($user);
                                $trustedDevices = app(\App\Services\TwoFactorAuthService::class)->getTrustedDevices($user);
                            @endphp

                            <h6 class="mb-3"><i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication</h6>

                            @if($status['enabled'])
                                <!-- 2FA Enabled State -->
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Two-factor authentication is enabled</strong> for your account.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Recovery Codes</h6>
                                                <p class="card-text">You have <strong>{{ $status['recovery_codes_count'] }}</strong> recovery codes remaining.</p>
                                                <button type="button" class="btn btn-warning btn-sm" id="generateRecoveryCodesBtn">
                                                    <i class="fas fa-refresh"></i> Generate New Codes
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Disable 2FA</h6>
                                                <p class="card-text">Remove the extra security layer from your account.</p>
                                                <button type="button" class="btn btn-danger btn-sm" id="disableTwoFactorBtn">
                                                    <i class="fas fa-times"></i> Disable 2FA
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @elseif($status['pending_confirmation'])
                                <!-- 2FA Setup Pending State -->
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i>
                                    <strong>Two-factor authentication setup is pending.</strong> Please complete the setup below.
                                </div>

                                <div id="setupPendingSection">
                                    <h6>Complete Setup</h6>
                                    <p>Scan the QR code with your authenticator app and enter the verification code.</p>
                                    <button type="button" class="btn btn-primary" id="showSetupBtn">
                                        <i class="fas fa-qrcode"></i> Show Setup Instructions
                                    </button>
                                </div>

                            @else
                                <!-- 2FA Disabled State -->
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Two-factor authentication is not enabled</strong> for your account.
                                </div>

                                <div class="mb-4">
                                    <h6>Enable Two-Factor Authentication</h6>
                                    <p class="text-muted">
                                        Two-factor authentication adds an extra layer of security to your account.
                                        You'll need an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator.
                                    </p>
                                    <button type="button" class="btn btn-success" id="enableTwoFactorBtn">
                                        <i class="fas fa-shield-alt"></i> Enable Two-Factor Authentication
                                    </button>
                                </div>
                            @endif

                            <!-- Trusted Devices Section -->
                            @if(count($trustedDevices) > 0)
                                <div class="mt-4">
                                    <h6>Trusted Devices</h6>
                                    <div class="row">
                                        @foreach($trustedDevices as $device)
                                            <div class="col-md-6 mb-2">
                                                <div class="card border-left-success" data-device-id="{{ $device['id'] }}">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $device['device_name'] }}</strong><br>
                                                                <small class="text-muted">
                                                                    {{ $device['browser'] }} on {{ $device['platform'] }}<br>
                                                                    Last used: {{ \Carbon\Carbon::parse($device['last_used_at'])->diffForHumans() }}
                                                                </small>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-outline-danger revoke-device-btn"
                                                                    data-device-id="{{ $device['id'] }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="trustCurrentDeviceBtn">
                                    <i class="fas fa-plus"></i> Trust This Device
                                </button>
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

@push('scripts')
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

<!-- Disable Confirmation Modal -->
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

<script>
    function resendVerification() {
        if (confirm('Send verification email to {{ $customer->email }}?')) {
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
    }

    // Add animation to cards
    $(document).ready(function() {
        $('.card').each(function(index) {
            if (!$(this).hasClass('fade-in-scale')) {
                $(this).css('animation-delay', (index * 150) + 'ms').addClass('fade-in-scale');
            }
        });
    });

    // 2FA JavaScript functions
    document.addEventListener('DOMContentLoaded', function() {
        // Enable 2FA button
        document.getElementById('enableTwoFactorBtn')?.addEventListener('click', function() {
            enableTwoFactor();
        });

        // Show setup button (for pending state)
        document.getElementById('showSetupBtn')?.addEventListener('click', function() {
            enableTwoFactor();
        });

        // Disable 2FA button
        document.getElementById('disableTwoFactorBtn')?.addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('disableTwoFactorModal')).show();
        });

        // Confirm disable button
        document.getElementById('confirmDisableBtn')?.addEventListener('click', function() {
            disableTwoFactor();
        });

        // Generate recovery codes button
        document.getElementById('generateRecoveryCodesBtn')?.addEventListener('click', function() {
            generateRecoveryCodes();
        });

        // Trust current device button
        document.getElementById('trustCurrentDeviceBtn')?.addEventListener('click', function() {
            trustCurrentDevice();
        });

        // Revoke device buttons
        document.querySelectorAll('.revoke-device-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const deviceId = this.dataset.deviceId;
                revokeDevice(deviceId);
            });
        });
    });

    function enableTwoFactor() {
        fetch('{{ route("customer.two-factor.enable") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSetupModal(data.data);
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while enabling two-factor authentication.');
        });
    }

    function showSetupModal(setupData) {
        const modalBody = document.getElementById('setupModalBody');
        modalBody.innerHTML = `
            <div class="text-center mb-4">
                <h6>Step 1: Scan QR Code</h6>
                <p class="text-muted">Scan this QR code with your authenticator app</p>
                <div class="qr-code-container mb-3">
                    ${setupData.qr_code_svg}
                </div>
            </div>

            <div class="mb-4">
                <h6>Step 2: Save Recovery Codes</h6>
                <div class="alert alert-warning">
                    <strong>Important:</strong> Save these recovery codes in a safe place.
                </div>
                <div class="recovery-codes bg-light p-3 rounded">
                    ${setupData.recovery_codes.map(code => `<code class="d-block">${code}</code>`).join('')}
                </div>
            </div>

            <div class="mb-3">
                <h6>Step 3: Verify Setup</h6>
                <p class="text-muted">Enter the 6-digit code from your authenticator app</p>
                <form id="confirmSetupForm">
                    <div class="input-group">
                        <input type="text" class="form-control" id="verificationCode" name="code"
                               placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                        <button type="submit" class="btn btn-success">Verify & Enable</button>
                    </div>
                </form>
            </div>
        `;

        // Show modal
        new bootstrap.Modal(document.getElementById('twoFactorSetupModal')).show();

        // Handle verification form
        document.getElementById('confirmSetupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const code = document.getElementById('verificationCode').value;
            confirmTwoFactor(code);
        });
    }

    function confirmTwoFactor(code) {
        fetch('{{ route("customer.two-factor.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                show_notification('success', 'Two-factor authentication has been enabled successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while confirming the setup.');
        });
    }

    function disableTwoFactor() {
        const form = document.getElementById('disableTwoFactorForm');
        const formData = new FormData(form);

        fetch('{{ route("customer.two-factor.disable") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                current_password: formData.get('current_password'),
                confirmation: formData.get('confirmation') ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                show_notification('success', 'Two-factor authentication has been disabled.');
                setTimeout(() => location.reload(), 1500);
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while disabling two-factor authentication.');
        });
    }

    function trustCurrentDevice() {
        const deviceName = prompt('Enter a name for this device:', 'My Device');
        if (!deviceName || !deviceName.trim()) {
            show_notification('warning', 'Device name is required.');
            return;
        }

        fetch('{{ route("customer.two-factor.trust-device") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ device_name: deviceName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                show_notification('success', data.message);
                if (!data.data.was_already_trusted) {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while trusting the device.');
        });
    }

    function revokeDevice(deviceId) {
        if (!confirm('Are you sure you want to remove this device from your trusted devices?')) {
            return;
        }

        fetch(`{{ route("customer.two-factor.revoke-device", ":id") }}`.replace(':id', deviceId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-device-id="${deviceId}"]`).remove();
                show_notification('success', 'Device has been removed from your trusted devices.');
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while revoking the device.');
        });
    }

    function generateRecoveryCodes() {
        const password = prompt('Enter your current password to generate new recovery codes:');
        if (!password || !password.trim()) {
            show_notification('warning', 'Password is required.');
            return;
        }

        fetch('{{ route("customer.two-factor.recovery-codes") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ current_password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modalBody = document.getElementById('recoveryCodesBody');
                modalBody.innerHTML = `
                    <div class="alert alert-success">
                        <strong>New recovery codes generated!</strong> Please save these in a safe place.
                    </div>
                    <div class="recovery-codes bg-light p-3 rounded">
                        ${data.data.recovery_codes.map(code => `<code class="d-block">${code}</code>`).join('')}
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('recoveryCodesModal')).show();
            } else {
                show_notification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            show_notification('error', 'An error occurred while generating recovery codes.');
        });
    }
</script>
@endpush