{{-- Common Claims JavaScript Functions --}}
<script>
    console.log('Loading claims common functions...');

    // Global utility function to show alerts
    window.showClaimAlert = function(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert-custom');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'loading' ? 'alert-info' : 'alert-warning';
        const icon = type === 'success' ? 'fas fa-check-circle' : type === 'error' ? 'fas fa-exclamation-triangle' : type === 'loading' ? 'fas fa-spinner fa-spin' : 'fas fa-info-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-custom" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${icon} me-2"></i>${message}
                ${type !== 'loading' ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : ''}
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto remove after time (except loading alerts)
        if (type !== 'loading') {
            setTimeout(() => {
                const alert = document.querySelector('.alert-custom');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Return function to manually remove the alert
        return function() {
            const alert = document.querySelector('.alert-custom');
            if (alert) {
                alert.remove();
            }
        };
    }

    // Modal helper function that waits for Bootstrap to be available
    window.showClaimModal = function(modalId, callback = null) {
        const checkBootstrap = () => {
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
                if (callback) callback(modal);
            } else {
                setTimeout(checkBootstrap, 100); // Check again in 100ms
            }
        };
        checkBootstrap();
    }

    // Hide modal helper
    window.hideClaimModal = function(modalId) {
        const checkBootstrap = () => {
            if (typeof bootstrap !== 'undefined') {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            } else {
                setTimeout(checkBootstrap, 100);
            }
        };
        checkBootstrap();
    }

    // Common AJAX helper with error handling
    window.claimAjaxRequest = function(url, options = {}) {
        const defaultOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        return fetch(url, { ...defaultOptions, ...options })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                window.showClaimAlert('error', 'An error occurred. Please try again.');
                throw error;
            });
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Claims common functions - DOM ready');

        // Simple approach: just log Bootstrap availability
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap is available:', bootstrap);
            } else {
                console.error('Bootstrap is not available!');
            }
        }, 500);
    });
</script>