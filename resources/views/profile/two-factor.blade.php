@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
            </h1>
            <p class="text-muted mb-0">Secure your account with an extra layer of protection</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4 fade-in-scale">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-mobile-alt me-2"></i>Two-Factor Authentication Status
                    </h6>
                </div>
                <div class="card-body">
                    @if($status['enabled'])
                        <!-- 2FA Enabled State -->
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
                                        <small class="text-muted">{{ $status['recovery_codes_count'] }} codes remaining</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-laptop fa-2x text-info me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Trusted Devices</h6>
                                        <small class="text-muted">{{ $status['trusted_devices_count'] }} devices</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button class="btn btn-warning" id="generateRecoveryCodesBtn">
                                <i class="fas fa-sync me-2"></i>Generate New Recovery Codes
                            </button>
                            <button class="btn btn-info" id="trustCurrentDeviceBtn">
                                <i class="fas fa-laptop me-2"></i>Trust This Device
                            </button>
                            <button class="btn btn-danger" id="disableTwoFactorBtn">
                                <i class="fas fa-times me-2"></i>Disable 2FA
                            </button>
                        </div>

                    @elseif($status['pending_confirmation'])
                        <!-- 2FA Setup Pending State -->
                        <div class="text-center mb-4">
                            <i class="fas fa-clock fa-4x text-warning mb-3"></i>
                            <h5 class="text-warning">Setup Pending Confirmation</h5>
                            <p class="text-muted">Please complete 2FA setup to enable protection</p>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" id="showSetupBtn">
                                <i class="fas fa-shield-alt me-2"></i>Complete Setup
                            </button>
                        </div>

                    @else
                        <!-- 2FA Disabled State -->
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
                            <button class="btn btn-primary btn-lg" id="enableTwoFactorBtn">
                                <i class="fas fa-shield-alt me-2"></i>Enable Two-Factor Authentication
                            </button>
                        </div>
                    @endif

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
                </div>
            </div>
        </div>

        <!-- Trusted Devices Card -->
        <div class="card shadow mb-4 fade-in-scale" id="trustedDevicesCard" style="display: {{ count($trustedDevices) > 0 ? 'block' : 'none' }};">
            <div class="card-header py-3 bg-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-laptop me-2"></i>Trusted Devices
                </h6>
            </div>
            <div class="card-body">
                <div id="trustedDevicesList">
                    @if(count($trustedDevices) > 0)
                        @foreach($trustedDevices as $device)
                            <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-laptop fa-2x text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $device['device_name'] }}</h6>
                                        <small class="text-muted">
                                            {{ $device['browser'] }} - {{ $device['platform'] }}<br>
                                            Last used: {{ \Carbon\Carbon::parse($device['last_used_at'])->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger revoke-device-btn" data-device-id="{{ $device['id'] }}" title="Remove Trust">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-laptop fa-3x mb-3"></i>
                            <p>No trusted devices</p>
                        </div>
                    @endif
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

        <!-- Security Level -->
        <div class="card shadow mb-4 fade-in-scale">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-shield-alt me-2"></i>Security Level
                </h6>
            </div>
            <div class="card-body text-center">
                @if($status['enabled'])
                    <i class="fas fa-shield-check fa-3x text-success mb-3"></i>
                    <h5 class="text-success">High Security</h5>
                    <p class="text-muted">2FA protection enabled</p>
                @elseif($status['pending_confirmation'])
                    <i class="fas fa-shield-slash fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Medium Security</h5>
                    <p class="text-muted">2FA setup in progress</p>
                @else
                    <i class="fas fa-shield-slash fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Basic Security</h5>
                    <p class="text-muted">Password protection only</p>
                @endif
            </div>
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
    fetch('{{ route("profile.two-factor.enable") }}', {
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
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-qrcode me-2 text-primary"></i>1. Scan QR Code</h6>
                <div class="text-center mb-3">
                    <div class="qr-code-container mx-auto d-inline-block p-3 bg-white border rounded shadow-sm" style="max-width: 250px;">
                        ${setupData.qr_code_svg}
                    </div>
                </div>
                <p class="small text-muted text-center">Scan this QR code with your authenticator app</p>
                <div class="text-center">
                    <small class="text-muted d-block">Recommended apps:</small>
                    <small class="text-muted">Google Authenticator, Microsoft Authenticator, Authy</small>
                </div>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-mobile-alt me-2 text-success"></i>2. Enter Verification Code</h6>
                <form id="confirmSetupForm">
                    <div class="mb-3">
                        <label for="verificationCode" class="form-label">6-digit code</label>
                        <input type="text" class="form-control text-center" id="verificationCode" name="code"
                               pattern="[0-9]{6}" maxlength="6" placeholder="000000" required autocomplete="off">
                        <div class="form-text">Enter the code from your authenticator app</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Confirm & Enable
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <h6><i class="fas fa-shield-alt me-2 text-warning"></i>3. Save Recovery Codes</h6>
                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Important:</strong> Save these codes in a safe place. You can use them to access your account if you lose your device.
                    </small>
                </div>
                <div class="recovery-codes-container bg-light p-3 rounded">
                    ${setupData.recovery_codes.map(code => `<code class="d-block p-1 mb-1">${code}</code>`).join('')}
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="copyRecoveryCodes()">
                        <i class="fas fa-copy me-1"></i>Copy All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="downloadRecoveryCodes()">
                        <i class="fas fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    `;

    // Show modal
    showModal('twoFactorSetupModal');

    // Handle verification form
    document.getElementById('confirmSetupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('verificationCode').value;
        if (code.length !== 6) {
            show_notification('error', 'Please enter a 6-digit code');
            return;
        }
        confirmTwoFactor(code);
    });

    // Auto-format code input
    document.getElementById('verificationCode').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
            confirmTwoFactor(this.value);
        }
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
            show_notification('success', 'Two-factor authentication has been disabled.');
            hideModal('disableTwoFactorModal');
            setTimeout(() => location.reload(), 2000);
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
    showInputModal(
        'Trust Device',
        'Enter a name for this device:',
        'My Device',
        function(deviceName) {
            if (!deviceName.trim()) {
                show_notification('warning', 'Device name is required.');
                return;
            }
            trustDeviceWithName(deviceName);
        }
    );
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
            show_notification('success', 'Device has been added to your trusted devices.');
            setTimeout(() => location.reload(), 1500);
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
    showConfirmationModal(
        'Remove Trusted Device',
        'Are you sure you want to remove this device from your trusted devices?',
        'danger',
        function() {
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
                    show_notification('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                show_notification('error', 'An error occurred while revoking the device.');
            });
        }
    );
}

function generateRecoveryCodes() {
    fetch('{{ route("profile.two-factor.recovery-codes") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showRecoveryCodesModal(data.recovery_codes);
        } else {
            show_notification('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        show_notification('error', 'An error occurred while generating recovery codes.');
    });
}

function showRecoveryCodesModal(recoveryCodes) {
    const modalBody = document.getElementById('recoveryCodesBody');
    modalBody.innerHTML = `
        <div class="alert alert-warning">
            <strong>Important:</strong> Save these recovery codes in a safe place. You can use them to access your account if you lose your device.
        </div>
        <div class="recovery-codes bg-light p-3 rounded">
            ${recoveryCodes.map(code => `<code class="d-block">${code}</code>`).join('')}
        </div>
        <div class="mt-3">
            <button type="button" class="btn btn-primary btn-sm" onclick="downloadRecoveryCodes()">
                <i class="fas fa-download"></i> Download Codes
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="printRecoveryCodes()">
                <i class="fas fa-print"></i> Print Codes
            </button>
        </div>
    `;
    showModal('recoveryCodesModal');
}

function copyRecoveryCodes() {
    // Get codes from setup modal or recovery codes modal
    const setupCodes = Array.from(document.querySelectorAll('.recovery-codes-container code'));
    const modalCodes = Array.from(document.querySelectorAll('#recoveryCodesBody code'));
    const codes = setupCodes.length > 0 ? setupCodes : modalCodes;

    if (codes.length === 0) {
        show_notification('error', 'No recovery codes found to copy');
        return;
    }

    const codeText = codes.map(el => el.textContent).join('\n');

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(codeText).then(() => {
            show_notification('success', 'Recovery codes copied to clipboard');
        }).catch(() => {
            fallbackCopyTextToClipboard(codeText);
        });
    } else {
        fallbackCopyTextToClipboard(codeText);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        show_notification('success', 'Recovery codes copied to clipboard');
    } catch (err) {
        show_notification('error', 'Failed to copy recovery codes');
    }

    document.body.removeChild(textArea);
}

function downloadRecoveryCodes() {
    // Get codes from setup modal or recovery codes modal
    const setupCodes = Array.from(document.querySelectorAll('.recovery-codes-container code'));
    const modalCodes = Array.from(document.querySelectorAll('#recoveryCodesBody code'));
    const codes = setupCodes.length > 0 ? setupCodes : modalCodes;

    if (codes.length === 0) {
        show_notification('error', 'No recovery codes found to download');
        return;
    }

    const codeText = codes.map(el => el.textContent);
    const content = 'Two-Factor Authentication Recovery Codes\n\n' + codeText.join('\n');
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'recovery-codes.txt';
    a.click();
    window.URL.revokeObjectURL(url);
}

function printRecoveryCodes() {
    const codes = Array.from(document.querySelectorAll('#recoveryCodesBody code')).map(el => el.textContent);
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head><title>Recovery Codes</title></head>
            <body>
                <h2>Two-Factor Authentication Recovery Codes</h2>
                <p>Keep these codes safe. Each code can only be used once.</p>
                ${codes.map(code => `<p><code style="font-family: monospace; background: #f5f5f5; padding: 4px;">${code}</code></p>`).join('')}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Helper Functions for Modals and Toasts
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
</script>

<style>
/* Enhanced Card Animations */
.fade-in-scale {
    animation: fadeInScale 0.6s ease-out forwards;
    opacity: 0;
    transform: scale(0.95);
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Add staggered animation delays for cards */
.fade-in-scale:nth-child(1) { animation-delay: 0.1s; }
.fade-in-scale:nth-child(2) { animation-delay: 0.2s; }
.fade-in-scale:nth-child(3) { animation-delay: 0.3s; }
.fade-in-scale:nth-child(4) { animation-delay: 0.4s; }

/* QR Code Styling with Enhanced Effects */
.qr-code-container {
    background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%),
                linear-gradient(-45deg, #f8f9fa 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #f8f9fa 75%),
                linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    border: 2px solid #dee2e6;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.qr-code-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.qr-code-container:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
    border-color: #007bff;
}

.qr-code-container:hover::before {
    opacity: 1;
    animation: shimmer 1.5s ease-in-out;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}

.qr-code-container svg {
    width: 100% !important;
    height: auto !important;
    max-width: 220px;
    display: block;
    margin: 0 auto;
    transition: all 0.3s ease;
}

/* Recovery Codes Enhanced Styling */
.recovery-codes-container, .recovery-codes {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.recovery-codes-container::before, .recovery-codes::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.recovery-codes-container:hover::before, .recovery-codes:hover::before {
    left: 100%;
}

.recovery-codes-container code, .recovery-codes code {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace !important;
    font-size: 14px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    border: 1px solid #e9ecef !important;
    padding: 10px 15px !important;
    margin-bottom: 8px !important;
    border-radius: 8px;
    letter-spacing: 1.5px;
    color: #495057 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: block;
    position: relative;
    overflow: hidden;
}

.recovery-codes-container code:hover, .recovery-codes code:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%) !important;
    transform: translateX(8px) scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff !important;
}

/* Button Enhancements */
.btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    transition: all 0.3s ease;
    transform: translate(-50%, -50%);
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Card Header Gradient Effects */
.card-header.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}

.card-header.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
}

.card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
}

.card-header.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%) !important;
}

.card-header.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

/* Form Input Enhancements */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: scale(1.02);
    transition: all 0.3s ease;
}

/* Modal Enhancements */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-bottom: none;
    padding: 20px 30px;
}

.modal-body {
    padding: 30px;
}

.modal-lg {
    max-width: 900px;
}

/* Alert Enhancements */
.alert {
    border: none;
    border-radius: 12px;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
    animation: alertShimmer 2s infinite;
}

@keyframes alertShimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left-color: #28a745;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left-color: #ffc107;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left-color: #dc3545;
}

/* Icon Animations */
.fas, .fab {
    transition: all 0.3s ease;
}

.card-header .fas:hover, .btn .fas:hover {
    transform: scale(1.2) rotate(5deg);
}

/* Security Level Icon Pulse */
.fa-shield-check, .fa-shield-slash {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .qr-code-container {
        max-width: 200px !important;
    }

    .modal-lg {
        max-width: 95%;
        margin: 10px auto;
    }

    .recovery-codes-container code, .recovery-codes code {
        font-size: 12px;
        padding: 8px 12px !important;
        letter-spacing: 1px;
    }

    .card-body {
        padding: 20px 15px;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .qr-code-container {
        background: linear-gradient(45deg, #2d3748 25%, transparent 25%),
                    linear-gradient(-45deg, #2d3748 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, #2d3748 75%),
                    linear-gradient(-45deg, transparent 75%, #2d3748 75%);
        border-color: #4a5568;
    }

    .recovery-codes-container code, .recovery-codes code {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%) !important;
        color: #e2e8f0 !important;
        border-color: #4a5568 !important;
    }
}
</style>
@endsection