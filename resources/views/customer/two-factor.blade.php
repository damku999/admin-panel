@extends('layouts.customer')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
                        </h1>
                        <p class="text-muted mb-0">Secure your account with an extra layer of protection</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- 2FA Status Card -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card shadow mb-4 fade-in-scale">
                            <div class="card-header py-3 bg-primary">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-mobile-alt me-2"></i>Two-Factor Authentication Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="twoFactorStatus">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading 2FA status...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trusted Devices Card -->
                        <div class="card shadow mb-4 fade-in-scale" id="trustedDevicesCard" style="display: none;">
                            <div class="card-header py-3 bg-info">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-laptop me-2"></i>Trusted Devices
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="trustedDevicesList">
                                    <!-- Trusted devices will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <!-- Security Tips -->
                        <div class="card shadow mb-4 fade-in-scale">
                            <div class="card-header py-3 bg-success">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-lightbulb me-2"></i>Security Tips
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-mobile-alt fa-2x text-success me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Use an Authenticator App</h6>
                                        <small class="text-muted">Google Authenticator, Microsoft Authenticator, or Authy</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-save fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Save Recovery Codes</h6>
                                        <small class="text-muted">Store backup codes in a safe place</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-laptop fa-2x text-info me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Trust Your Devices</h6>
                                        <small class="text-muted">Skip 2FA on trusted devices for 30 days</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Security Level -->
                        <div class="card shadow mb-4 fade-in-scale">
                            <div class="card-header py-3 bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-shield-alt me-2"></i>Security Level
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div id="securityLevel">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Disable Modal -->
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

<!-- Trust Device Modal -->
<div class="modal fade" id="trustDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Trust This Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trustDeviceForm">
                    <div class="mb-3">
                        <label for="deviceName" class="form-label">Device Name (Optional)</label>
                        <input type="text" class="form-control" id="deviceName" name="device_name"
                               placeholder="My Laptop" maxlength="100">
                        <div class="form-text">Give this device a memorable name</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You won't need to enter 2FA codes on this device for 30 days.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmTrustBtn">Trust Device</button>
            </div>
        </div>
    </div>
</div>

<!-- Generate Recovery Codes Modal -->
<div class="modal fade" id="generateRecoveryCodesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Recovery Codes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will replace all your existing recovery codes.
                    Make sure to save the new codes safely.
                </div>
                <form id="generateRecoveryForm">
                    <div class="mb-3">
                        <label for="recoveryPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="recoveryPassword" name="current_password"
                               placeholder="Enter your current password" required>
                        <div class="form-text">We need to verify your identity to generate new recovery codes</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmGenerateRecoveryBtn">
                    <i class="fas fa-sync me-2"></i>Generate New Codes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmationModalAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🔧 [Customer 2FA] Document ready, about to load status');
    console.log('🔧 [Customer 2FA] jQuery version:', $.fn.jquery);
    console.log('🔧 [Customer 2FA] CSRF token:', document.querySelector('meta[name="csrf-token"]')?.content);
    loadTwoFactorStatus();
});

function loadTwoFactorStatus() {
    console.log('🔧 [Customer 2FA] Loading status from:', '{{ route("customer.two-factor.status") }}');

    fetch('{{ route("customer.two-factor.status") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(response => {
            console.log('🔧 [Customer 2FA] Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('🔧 [Customer 2FA] Response data:', data);
            if (data.success) {
                displayTwoFactorStatus(data.data);
            } else {
                console.error('🚨 [Customer 2FA] API returned success: false', data);
                $('#twoFactorStatus').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Failed to load 2FA status: ${data.message || 'Unknown error'}
                    </div>
                `);
                $('#securityLevel').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Error loading security level
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('🚨 [Customer 2FA] Fetch error:', error);
            $('#twoFactorStatus').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error loading 2FA status: ${error.message}
                </div>
            `);
            $('#securityLevel').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Network error
                </div>
            `);
        });
}

function displayTwoFactorStatus(data) {
    const status = data.status;
    const trustedDevices = data.trusted_devices;
    const currentDeviceTrusted = data.current_device_trusted;

    let statusHtml = '';
    let securityLevel = '';

    if (status.enabled) {
        // 2FA is enabled
        statusHtml = `
            <div class="text-center mb-4">
                <i class="fas fa-shield-check fa-4x text-success mb-3"></i>
                <h5 class="text-success">Two-Factor Authentication is Enabled</h5>
                <p class="text-muted">Your account is protected with 2FA</p>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-key fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Recovery Codes</h6>
                            <small class="text-muted">${status.recovery_codes_count} codes remaining</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-laptop fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="mb-0">Trusted Devices</h6>
                            <small class="text-muted">${status.trusted_devices_count} devices</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                <button class="btn btn-warning" onclick="generateRecoveryCodes()">
                    <i class="fas fa-sync me-2"></i>Generate New Recovery Codes
                </button>
                ${!currentDeviceTrusted ? `
                <button class="btn btn-info" onclick="showTrustDeviceModal()">
                    <i class="fas fa-laptop me-2"></i>Trust This Device
                </button>
                ` : ''}
                <button class="btn btn-danger" onclick="showDisableModal()">
                    <i class="fas fa-times me-2"></i>Disable 2FA
                </button>
            </div>
        `;
        securityLevel = `
            <i class="fas fa-shield-check fa-3x text-success mb-3"></i>
            <h5 class="text-success">High Security</h5>
            <p class="text-muted">2FA protection enabled</p>
        `;
    } else if (status.pending_confirmation) {
        // 2FA setup is pending
        statusHtml = `
            <div class="text-center mb-4">
                <i class="fas fa-clock fa-4x text-warning mb-3"></i>
                <h5 class="text-warning">Setup Pending Confirmation</h5>
                <p class="text-muted">Please complete 2FA setup to enable protection</p>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" onclick="enableTwoFactor()">
                    <i class="fas fa-shield-alt me-2"></i>Complete Setup
                </button>
            </div>
        `;
        securityLevel = `
            <i class="fas fa-shield-slash fa-3x text-warning mb-3"></i>
            <h5 class="text-warning">Medium Security</h5>
            <p class="text-muted">2FA setup in progress</p>
        `;
    } else {
        // 2FA is disabled
        statusHtml = `
            <div class="text-center mb-4">
                <i class="fas fa-shield-slash fa-4x text-danger mb-3"></i>
                <h5 class="text-danger">Two-Factor Authentication is Disabled</h5>
                <p class="text-muted">Your account is only protected by your password</p>
            </div>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Security Recommendation:</strong> Enable 2FA to add an extra layer of protection to your account.
            </div>
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" onclick="enableTwoFactor()">
                    <i class="fas fa-shield-alt me-2"></i>Enable Two-Factor Authentication
                </button>
            </div>
        `;
        securityLevel = `
            <i class="fas fa-shield-slash fa-3x text-danger mb-3"></i>
            <h5 class="text-danger">Basic Security</h5>
            <p class="text-muted">Password protection only</p>
        `;
    }

    $('#twoFactorStatus').html(statusHtml);
    $('#securityLevel').html(securityLevel);

    // Display trusted devices
    if (trustedDevices.length > 0) {
        displayTrustedDevices(trustedDevices);
        $('#trustedDevicesCard').show();
    } else {
        $('#trustedDevicesCard').hide();
    }
}

function displayTrustedDevices(devices) {
    let devicesHtml = '';

    if (devices.length === 0) {
        devicesHtml = `
            <div class="text-center text-muted">
                <i class="fas fa-laptop fa-3x mb-3"></i>
                <p>No trusted devices</p>
            </div>
        `;
    } else {
        devicesHtml = devices.map(device => `
            <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-${device.device_type === 'mobile' ? 'mobile-alt' : device.device_type === 'tablet' ? 'tablet-alt' : 'laptop'} fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">${device.device_name}</h6>
                        <small class="text-muted">
                            ${device.browser} - ${device.platform}<br>
                            Last used: ${device.last_used_at ? new Date(device.last_used_at).toLocaleDateString() : 'Never'}
                        </small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="revokeDevice(${device.id})" title="Remove Trust">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');
    }

    $('#trustedDevicesList').html(devicesHtml);
}

function enableTwoFactor() {
    fetch('{{ route("customer.two-factor.enable") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSetupModal(data.data);
        } else {
            show_notification('error', data.message || 'Failed to enable 2FA');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred while enabling 2FA');
    });
}

function showSetupModal(setupData) {
    const modalBody = `
        <div class="row">
            <div class="col-md-6">
                <h6>1. Scan QR Code</h6>
                <div class="text-center mb-3">
                    ${setupData.qr_code_svg}
                </div>
                <p class="small text-muted text-center">Scan this QR code with your authenticator app</p>
            </div>
            <div class="col-md-6">
                <h6>2. Enter Verification Code</h6>
                <form id="confirmTwoFactorForm">
                    <div class="mb-3">
                        <label for="confirmationCode" class="form-label">6-digit code</label>
                        <input type="text" class="form-control text-center" id="confirmationCode" name="code"
                               pattern="[0-9]{6}" maxlength="6" placeholder="000000" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Confirm & Enable
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <h6>3. Save Recovery Codes</h6>
                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Save these codes in a safe place. You can use them to access your account if you lose your device.
                    </small>
                </div>
                <div class="recovery-codes-container">
                    ${setupData.recovery_codes.map(code => `<code class="d-block p-1 mb-1">${code}</code>`).join('')}
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="copyRecoveryCodes()">
                    <i class="fas fa-copy me-1"></i>Copy All Codes
                </button>
            </div>
        </div>
    `;

    $('#setupModalBody').html(modalBody);
    $('#twoFactorSetupModal').modal('show');

    // Handle form submission
    $('#confirmTwoFactorForm').on('submit', function(e) {
        e.preventDefault();
        confirmTwoFactor();
    });

    // Auto-format code input
    $('#confirmationCode').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
            confirmTwoFactor();
        }
    });
}

function confirmTwoFactor() {
    const code = $('#confirmationCode').val();

    if (code.length !== 6) {
        show_notification('error', 'Please enter a 6-digit code');
        return;
    }

    fetch('{{ route("customer.two-factor.confirm") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#twoFactorSetupModal').modal('hide');
            show_notification('success', 'Two-factor authentication has been enabled successfully!');
            loadTwoFactorStatus();
        } else {
            show_notification('error', data.message || 'Invalid verification code');
            $('#confirmationCode').val('').focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred during confirmation');
    });
}

function showDisableModal() {
    $('#disableTwoFactorModal').modal('show');
}

$('#confirmDisableBtn').on('click', function() {
    const password = $('#currentPassword').val();
    const confirmed = $('#confirmDisable').is(':checked');

    if (!password || !confirmed) {
        show_notification('error', 'Please provide your password and confirm the action');
        return;
    }

    fetch('{{ route("customer.two-factor.disable") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            current_password: password,
            confirmation: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#disableTwoFactorModal').modal('hide');
            show_notification('success', 'Two-factor authentication has been disabled');
            loadTwoFactorStatus();
        } else {
            show_notification('error', data.message || 'Failed to disable 2FA');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred');
    });
});

function generateRecoveryCodes() {
    $('#generateRecoveryCodesModal').modal('show');
}

// Handle generate recovery codes form submission
$('#confirmGenerateRecoveryBtn').on('click', function() {
    const password = $('#recoveryPassword').val();

    if (!password) {
        show_notification('error', 'Please enter your current password');
        return;
    }

    // Show loading state
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Generating...');

    fetch('{{ route("customer.two-factor.recovery-codes") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ current_password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#generateRecoveryCodesModal').modal('hide');
            showRecoveryCodesModal(data.data.recovery_codes);
            loadTwoFactorStatus();
            show_notification('success', 'New recovery codes generated successfully');
        } else {
            show_notification('error', data.message || 'Failed to generate recovery codes');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred while generating recovery codes');
    })
    .finally(() => {
        // Reset button state
        $('#confirmGenerateRecoveryBtn').prop('disabled', false).html('<i class="fas fa-sync me-2"></i>Generate New Codes');
        $('#recoveryPassword').val('');
    });
});

function showRecoveryCodesModal(codes) {
    const codesHtml = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Important:</strong> Save these recovery codes in a safe place.
            Each code can only be used once.
        </div>
        <div class="recovery-codes-container mb-3">
            ${codes.map(code => `<code class="d-block p-2 mb-1 border">${code}</code>`).join('')}
        </div>
        <button type="button" class="btn btn-outline-primary" onclick="copyRecoveryCodes()">
            <i class="fas fa-copy me-1"></i>Copy All Codes
        </button>
    `;

    $('#recoveryCodesBody').html(codesHtml);
    $('#recoveryCodesModal').modal('show');
}

function showTrustDeviceModal() {
    $('#trustDeviceModal').modal('show');
}

$('#confirmTrustBtn').on('click', function() {
    const deviceName = $('#deviceName').val() || 'My Device';

    fetch('{{ route("customer.two-factor.trust-device") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ device_name: deviceName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#trustDeviceModal').modal('hide');
            show_notification('success', 'This device has been trusted for 30 days');
            loadTwoFactorStatus();
        } else {
            show_notification('error', data.message || 'Failed to trust device');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred');
    });
});

function revokeDevice(deviceId) {
    showConfirmationModal(
        'Remove Trusted Device',
        'Are you sure you want to remove trust from this device? You will need to enter 2FA codes on this device again.',
        'danger',
        function() {
            fetch(`{{ route("customer.two-factor.revoke-device", ":deviceId") }}`.replace(':deviceId', deviceId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    show_notification('success', 'Device trust has been revoked');
                    loadTwoFactorStatus();
                } else {
                    show_notification('error', data.message || 'Failed to revoke device trust');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                show_notification('error', 'An error occurred');
            });
        }
    );
}

function copyRecoveryCodes() {
    const codes = Array.from(document.querySelectorAll('.recovery-codes-container code'))
                     .map(el => el.textContent)
                     .join('\n');

    navigator.clipboard.writeText(codes).then(() => {
        show_notification('success', 'Recovery codes copied to clipboard');
    }).catch(() => {
        show_notification('error', 'Failed to copy codes');
    });
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

// Generic confirmation modal
function showConfirmationModal(title, message, variant = 'primary', onConfirm = null) {
    $('#confirmationModalTitle').text(title);
    $('#confirmationModalBody').html(`<p>${message}</p>`);

    const button = $('#confirmationModalAction');
    button.removeClass('btn-primary btn-danger btn-warning').addClass(`btn-${variant}`);

    // Remove previous event handlers and add new one
    button.off('click').on('click', function() {
        $('#confirmationModal').modal('hide');
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });

    $('#confirmationModal').modal('show');
}
</script>
@endpush