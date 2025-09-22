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

        {{-- Security Settings --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security Settings</h5>
                        <p class="text-muted mb-0 mt-1">Manage your account security and authentication methods</p>
                    </div>
                    <div class="card-body">
                        <!-- Security Options Row -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-lock me-2"></i>Password Security</h6>
                                        <p class="card-text text-muted">Update your account password</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#passwordSection">
                                            <i class="fas fa-key me-1"></i>Change Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-mobile-alt me-2"></i>Two-Factor Authentication</h6>
                                        <p class="card-text text-muted">Add extra security to your account</p>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="collapse" data-bs-target="#twoFactorSection">
                                            <i class="fas fa-shield-alt me-1"></i>Manage 2FA
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password Form (Collapsible) -->
                        <div class="collapse" id="passwordSection">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="fas fa-key me-2"></i>Change Password</h6>
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

                        <!-- Two-Factor Authentication Section (Collapsible) -->
                        <div class="collapse" id="twoFactorSection">
                            <div class="card bg-light">
                                <div class="card-body">
                                    @php
                                        $user = Auth::user();
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
                <button type="button" class="btn-close" onclick="hideModal('twoFactorSetupModal')" aria-label="Close"></button>
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
                <button type="button" class="btn-close" onclick="hideModal('disableTwoFactorModal')" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" onclick="hideModal('disableTwoFactorModal')">Cancel</button>
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
                <button type="button" class="btn-close" onclick="hideModal('recoveryCodesModal')" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="recoveryCodesBody">
                <!-- Recovery codes will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModal('recoveryCodesModal')">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
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
        showModal('disableTwoFactorModal');
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
    console.log('🔧 [2FA Enable] Starting 2FA enable process...');
    console.log('🔧 [2FA Enable] CSRF Token:', '{{ csrf_token() }}');
    console.log('🔧 [2FA Enable] Route URL:', '{{ route("profile.two-factor.enable") }}');

    fetch('{{ route("profile.two-factor.enable") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        console.log('🔧 [2FA Enable] Response received:', {
            status: response.status,
            statusText: response.statusText,
            url: response.url,
            redirected: response.redirected,
            type: response.type
        });

        if (!response.ok) {
            console.error('🚨 [2FA Enable] HTTP Error:', response.status, response.statusText);
        }

        return response.json();
    })
    .then(data => {
        console.log('🔧 [2FA Enable] Response data:', data);

        if (data.success) {
            console.log('✅ [2FA Enable] Success - showing setup modal with data:', data.data);
            showSetupModal(data.data);
        } else {
            console.error('❌ [2FA Enable] Server error:', data.message);
            console.error('❌ [2FA Enable] Full error data:', data);
            showError('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('🚨 [2FA Enable] Network/Parse error:', error);
        console.error('🚨 [2FA Enable] Error stack:', error.stack);
        showError('An error occurred while enabling two-factor authentication. Check console for details.');
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
    showModal('twoFactorSetupModal');

    // Handle verification form
    document.getElementById('confirmSetupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('verificationCode').value;
        confirmTwoFactor(code);
    });
}

function confirmTwoFactor(code) {
    fetch('{{ route("profile.two-factor.confirm") }}', {
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
            showSuccess('Two-factor authentication has been enabled successfully!');
            location.reload();
        } else {
            showError('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while confirming the setup.');
    });
}

function disableTwoFactor() {
    const form = document.getElementById('disableTwoFactorForm');
    const formData = new FormData(form);

    fetch('{{ route("profile.two-factor.disable") }}', {
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
            showSuccess('Two-factor authentication has been disabled.');
            location.reload();
        } else {
            showError('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while disabling two-factor authentication.');
    });
}

function trustCurrentDevice() {
    const deviceName = prompt('Enter a name for this device:', 'My Device');
    if (!deviceName || !deviceName.trim()) {
        show_notification('warning', 'Device name is required.');
        return;
    }
    trustDeviceWithName(deviceName);
}

function trustDeviceWithName(deviceName) {
    fetch('{{ route("profile.two-factor.trust-device") }}', {
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
            showError('Error: ' + data.message);
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

    fetch(`{{ route("profile.two-factor.revoke-device", ":id") }}`.replace(':id', deviceId), {
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
            showError('Error: ' + data.message);
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
    generateRecoveryCodesWithPassword(password);
}

function generateRecoveryCodesWithPassword(password) {
    fetch('{{ route("profile.two-factor.recovery-codes") }}', {
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
            showModal('recoveryCodesModal');
        } else {
            showError('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred while generating recovery codes.');
    });
}
</script>
@endpush
