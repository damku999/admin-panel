/**
 * UI Helpers - Modal and Toast Utilities
 * Provides consistent modal dialogs and toast notifications across the application
 */

// Primary notification function - use this throughout the app
window.show_notification = function(type, message, title = '') {
    // Use toastr if available
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        switch(type.toLowerCase()) {
            case 'success':
                toastr.success(message, title);
                break;
            case 'error':
            case 'danger':
                toastr.error(message, title);
                break;
            case 'warning':
                toastr.warning(message, title);
                break;
            case 'info':
                toastr.info(message, title);
                break;
            default:
                toastr.info(message, title);
        }
        return;
    }

    // Fallback to Bootstrap Toast if toastr not available
    if (typeof bootstrap !== 'undefined') {
        createBootstrapToast(type, message, title);
        return;
    }

    // Final fallback - create a simple toast notification
    createSimpleToast(type, message);
};

// Bootstrap Toast fallback
function createBootstrapToast(type, message, title) {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    const iconMap = {
        'success': 'fas fa-check-circle text-success',
        'error': 'fas fa-exclamation-circle text-danger',
        'danger': 'fas fa-exclamation-circle text-danger',
        'warning': 'fas fa-exclamation-triangle text-warning',
        'info': 'fas fa-info-circle text-info'
    };

    const toastHtml = `
        <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="${iconMap[type] || iconMap.info} me-2"></i>
                <strong class="me-auto">${title || type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();

    // Remove from DOM after hiding
    document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Create toast container if not exists
function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Simple toast fallback
function createSimpleToast(type, message) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.simple-toast');
    existingToasts.forEach(toast => toast.remove());

    const colorMap = {
        'success': '#28a745',
        'error': '#dc3545',
        'danger': '#dc3545',
        'warning': '#ffc107',
        'info': '#17a2b8'
    };

    const toast = document.createElement('div');
    toast.className = 'simple-toast';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colorMap[type] || colorMap.info};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        max-width: 350px;
        word-wrap: break-word;
        animation: slideInRight 0.3s ease-out;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    `;

    toast.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
            <span style="flex: 1;">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()"
                    style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 0; line-height: 1;">×</button>
        </div>
    `;

    // Add animation styles if not already present
    if (!document.querySelector('#simple-toast-styles')) {
        const style = document.createElement('style');
        style.id = 'simple-toast-styles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes fadeOut {
                to { opacity: 0; transform: translateX(100%); }
            }
            .simple-toast.fade-out {
                animation: fadeOut 0.3s ease-out forwards;
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

// Generic confirmation modal
window.showConfirmationModal = function(title, message, variant = 'primary', onConfirm = null) {
    const modalId = 'confirmModal-' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-${variant}" data-confirm="true">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalElement = document.getElementById(modalId);
    const modal = new bootstrap.Modal(modalElement);

    // Handle confirmation
    modalElement.querySelector('[data-confirm="true"]').addEventListener('click', function() {
        modal.hide();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });

    // Clean up after modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
        this.remove();
    });

    modal.show();
};

// Password input modal
window.showPasswordModal = function(title, message, onSubmit) {
    const modalId = 'passwordModal-' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" data-password-input placeholder="Enter your password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" data-submit="true">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalElement = document.getElementById(modalId);
    const modal = new bootstrap.Modal(modalElement);
    const passwordInput = modalElement.querySelector('[data-password-input]');

    // Focus on password input when modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
        passwordInput.focus();
    });

    // Handle form submission
    function submitForm() {
        const password = passwordInput.value.trim();
        if (!password) {
            showToast('warning', 'Password is required');
            passwordInput.focus();
            return;
        }

        modal.hide();
        if (typeof onSubmit === 'function') {
            onSubmit(password);
        }
    }

    // Submit on button click
    modalElement.querySelector('[data-submit="true"]').addEventListener('click', submitForm);

    // Submit on Enter key
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitForm();
        }
    });

    // Clean up after modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
        this.remove();
    });

    modal.show();
};

// Device name input modal
window.showDeviceNameModal = function(title, defaultName = 'My Device', onSubmit) {
    const modalId = 'deviceNameModal-' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Device Name</label>
                            <input type="text" class="form-control" data-device-name-input value="${defaultName}"
                                   placeholder="Enter device name" required>
                            <div class="form-text">Give this device a memorable name</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" data-submit="true">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalElement = document.getElementById(modalId);
    const modal = new bootstrap.Modal(modalElement);
    const deviceNameInput = modalElement.querySelector('[data-device-name-input]');

    // Focus and select all text when modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
        deviceNameInput.focus();
        deviceNameInput.select();
    });

    // Handle form submission
    function submitForm() {
        const deviceName = deviceNameInput.value.trim();
        if (!deviceName) {
            showToast('warning', 'Device name is required');
            deviceNameInput.focus();
            return;
        }

        modal.hide();
        if (typeof onSubmit === 'function') {
            onSubmit(deviceName);
        }
    }

    // Submit on button click
    modalElement.querySelector('[data-submit="true"]').addEventListener('click', submitForm);

    // Submit on Enter key
    deviceNameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitForm();
        }
    });

    // Clean up after modal is hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
        this.remove();
    });

    modal.show();
};

// Alternative function name (alias)
window.showToast = function(type, message, title = '') {
    show_notification(type, message, title);
};

// Utility function to replace all confirm() calls with modal
window.confirmModal = function(message, callback) {
    showConfirmationModal('Confirm Action', message, 'primary', callback);
    return false; // Prevent default form submission
};

// Replace browser's confirm function (optional, use carefully)
// window.confirm = function(message) {
//     showConfirmationModal('Confirm', message, 'primary', function() {
//         // This doesn't work well with synchronous confirm() expectations
//         // Better to use confirmModal() explicitly
//     });
//     return false;
// };

console.log('UI Helpers loaded: show_notification (primary), showConfirmationModal, showPasswordModal, showDeviceNameModal');