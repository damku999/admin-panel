@extends('layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-shield-alt"></i> Two-Factor Authentication</h4>
                    <p class="mb-0 text-muted">Add an extra layer of security to your account</p>
                </div>
                <div class="card-body">
                    @if($status['enabled'])
                        <!-- 2FA Enabled State -->
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Two-factor authentication is enabled</strong> for your account.
                        </div>

                        <div class="mb-4">
                            <h5>Recovery Codes</h5>
                            <p class="text-muted">You have <strong>{{ $status['recovery_codes_count'] }}</strong> recovery codes remaining.</p>
                            <button type="button" class="btn btn-warning btn-sm" id="generateRecoveryCodesBtn">
                                <i class="fas fa-refresh"></i> Generate New Recovery Codes
                            </button>
                        </div>

                        <div class="mb-4">
                            <h5>Disable Two-Factor Authentication</h5>
                            <p class="text-muted">This will remove the extra security layer from your account.</p>
                            <button type="button" class="btn btn-danger" id="disableTwoFactorBtn">
                                <i class="fas fa-times"></i> Disable Two-Factor Authentication
                            </button>
                        </div>

                    @elseif($status['pending_confirmation'])
                        <!-- 2FA Setup Pending State -->
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i>
                            <strong>Two-factor authentication setup is pending.</strong> Please complete the setup below.
                        </div>

                        <div id="setupPendingSection">
                            <h5>Complete Setup</h5>
                            <p>Scan the QR code with your authenticator app and enter the verification code.</p>
                            <!-- QR code and confirmation form will be loaded here -->
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
                            <h5>Enable Two-Factor Authentication</h5>
                            <p class="text-muted">
                                Two-factor authentication adds an extra layer of security to your account.
                                You'll need an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator.
                            </p>
                            <button type="button" class="btn btn-success" id="enableTwoFactorBtn">
                                <i class="fas fa-shield-alt"></i> Enable Two-Factor Authentication
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

        <div class="col-md-4">
            <!-- Trusted Devices -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-devices"></i> Trusted Devices</h5>
                </div>
                <div class="card-body">
                    @if(count($trustedDevices) > 0)
                        <div id="trustedDevicesList">
                            @foreach($trustedDevices as $device)
                                <div class="border rounded p-3 mb-3" data-device-id="{{ $device['id'] }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $device['device_name'] }}</h6>
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
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No trusted devices</p>
                    @endif

                    <button type="button" class="btn btn-sm btn-outline-primary" id="trustCurrentDeviceBtn">
                        <i class="fas fa-plus"></i> Trust This Device
                    </button>
                </div>
            </div>

            <!-- Security Status -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line"></i> Security Status</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="text-muted">2FA Status</h6>
                                <span class="badge {{ $status['enabled'] ? 'bg-success' : 'bg-warning' }}">
                                    {{ $status['enabled'] ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Trusted Devices</h6>
                            <strong>{{ count($trustedDevices) }}</strong>
                        </div>
                    </div>
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
                <strong>Important:</strong> Save these recovery codes in a safe place. You can use them to access your account if you lose your device.
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

function downloadRecoveryCodes() {
    const codes = Array.from(document.querySelectorAll('#recoveryCodesBody code')).map(el => el.textContent);
    const content = 'Two-Factor Authentication Recovery Codes\n\n' + codes.join('\n');
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
@endsection