/**
 * Admin Panel Utility Functions
 * Modern replacements for alert() and prompt() dialogs
 */

// Initialize toastr configuration
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

/**
 * Show success notification
 */
function showSuccess(message) {
    toastr.success(message);
}

/**
 * Show error notification
 */
function showError(message) {
    toastr.error(message);
}

/**
 * Show warning notification
 */
function showWarning(message) {
    toastr.warning(message);
}

/**
 * Show info notification
 */
function showInfo(message) {
    toastr.info(message);
}

/**
 * Show confirmation modal
 */
function showConfirmModal(title, message, onConfirm, onCancel = null) {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    document.getElementById('confirmModal')?.remove();

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));

    // Handle confirm button
    document.getElementById('confirmBtn').onclick = function() {
        modal.hide();
        if (onConfirm) onConfirm();
    };

    // Handle cancel/close
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        if (onCancel) onCancel();
        this.remove();
    });

    modal.show();
}

/**
 * Show input modal (replacement for prompt)
 */
function showInputModal(title, message, placeholder = '', onConfirm, onCancel = null) {
    const modalHtml = `
        <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                        <input type="text" class="form-control" id="inputValue" placeholder="${placeholder}" value="${placeholder}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="inputConfirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    document.getElementById('inputModal')?.remove();

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('inputModal'));
    const inputField = document.getElementById('inputValue');

    // Focus input field when modal is shown
    document.getElementById('inputModal').addEventListener('shown.bs.modal', function() {
        inputField.focus();
        inputField.select();
    });

    // Handle confirm button
    document.getElementById('inputConfirmBtn').onclick = function() {
        const value = inputField.value.trim();
        if (value) {
            modal.hide();
            if (onConfirm) onConfirm(value);
        }
    };

    // Handle Enter key in input
    inputField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('inputConfirmBtn').click();
        }
    });

    // Handle cancel/close
    document.getElementById('inputModal').addEventListener('hidden.bs.modal', function() {
        if (onCancel) onCancel();
        this.remove();
    });

    modal.show();
}

/**
 * Show password input modal (special case for password prompts)
 */
function showPasswordModal(title, message, onConfirm, onCancel = null) {
    const modalHtml = `
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                        <input type="password" class="form-control" id="passwordValue" placeholder="Enter your password">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="passwordConfirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    document.getElementById('passwordModal')?.remove();

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    const passwordField = document.getElementById('passwordValue');

    // Focus password field when modal is shown
    document.getElementById('passwordModal').addEventListener('shown.bs.modal', function() {
        passwordField.focus();
    });

    // Handle confirm button
    document.getElementById('passwordConfirmBtn').onclick = function() {
        const value = passwordField.value.trim();
        if (value) {
            modal.hide();
            if (onConfirm) onConfirm(value);
        } else {
            showError('Please enter your password.');
        }
    };

    // Handle Enter key in input
    passwordField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('passwordConfirmBtn').click();
        }
    });

    // Handle cancel/close
    document.getElementById('passwordModal').addEventListener('hidden.bs.modal', function() {
        if (onCancel) onCancel();
        this.remove();
    });

    modal.show();
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Loading...') {
    const loadingHtml = `
        <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    document.getElementById('loadingModal')?.remove();

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', loadingHtml);

    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();

    return modal;
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const loadingModal = document.getElementById('loadingModal');
    if (loadingModal) {
        const modal = bootstrap.Modal.getInstance(loadingModal);
        if (modal) {
            modal.hide();
        }
        setTimeout(() => loadingModal?.remove(), 300);
    }
}