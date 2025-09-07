/**
 * Claims Module - Shared JavaScript Functions
 * Used across index, show, and edit pages
 */

// Global variables
window.currentClaim = null;
window.currentClaimId = null;

/**
 * Show assign claim number modal
 */
function showAssignClaimNumberModal(claimId, claimData = null) {
    window.currentClaimId = claimId;
    
    if (claimData) {
        window.currentClaim = claimData;
        // Populate modal with claim data for list page
        $('#modal-customer-name').text(claimData.customerName);
        $('#modal-customer-mobile').text(claimData.customerMobile);
        $('#modal-insurance-type').text(claimData.insuranceType);
        $('#modal-vehicle-policy').text(claimData.vehiclePolicy);
    }
    
    // Clear form
    document.getElementById('insurance_claim_number').value = '';
    document.getElementById('whatsapp-preview').innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
    
    // Show modal
    showModal('assignClaimNumberModal', {
        closeOnBackdrop: false,
        closeOnEscape: false
    });
}

/**
 * Submit assign claim number via AJAX
 */
function submitAssignClaim() {
    const claimNumber = document.getElementById('insurance_claim_number').value.trim();
    
    if (!claimNumber) {
        show_notification('error', 'Please enter the insurance claim number');
        return;
    }
    
    if (!window.currentClaimId) {
        show_notification('error', 'Claim ID not found');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('assignClaimBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Submit via AJAX
    $.ajax({
        url: `/claims/assign-claim-number/${window.currentClaimId}`,
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            insurance_claim_number: claimNumber
        },
        success: function(response) {
            if (response.success) {
                show_notification('success', response.message || 'Claim number assigned successfully');
                hideModal('assignClaimNumberModal');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                show_notification('error', response.message || 'Failed to assign claim number');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while assigning the claim number';
            
            if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            show_notification('error', errorMessage);
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Update claim number WhatsApp preview
 */
function updateClaimNumberPreview() {
    const claimNumber = document.getElementById('insurance_claim_number').value.trim();
    const previewDiv = document.getElementById('whatsapp-preview');
    
    if (!claimNumber) {
        previewDiv.innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
        return;
    }
    
    // Generate preview message based on available data
    let customerName = 'Customer';
    let vehicleText = '';
    
    if (window.currentClaim) {
        customerName = window.currentClaim.customerName;
        if (window.currentClaim.vehicleNumber) {
            vehicleText = ` against your vehicle number *${window.currentClaim.vehicleNumber}*`;
        }
    } else if (window.claimCustomerName) {
        customerName = window.claimCustomerName;
        if (window.claimVehicleNumber) {
            vehicleText = ` against your vehicle number *${window.claimVehicleNumber}*`;
        }
    }
    
    const message = `Dear *${customerName}*,

Your Claim Number *${claimNumber}* is generated${vehicleText}. For further assistance kindly contact me.

Best regards,
Insurance Advisor
Contact Phone`;
    
    previewDiv.textContent = message;
}

/**
 * Show close claim modal
 */
function closeClaimModal(claimId) {
    window.currentClaimId = claimId;
    
    // Clear form and preview
    document.getElementById('closure_reason').value = '';
    document.getElementById('whatsapp-preview-close').innerHTML = '<em class="text-muted">Enter closure reason to see message preview</em>';
    
    // Show modal
    showModal('closeClaimModal', {
        closeOnBackdrop: false,
        closeOnEscape: false
    });
}

/**
 * Submit close claim via AJAX
 */
function submitCloseClaim() {
    const closureReason = document.getElementById('closure_reason').value.trim();
    
    if (!closureReason) {
        show_notification('error', 'Please provide a closure reason');
        return;
    }
    
    if (!window.currentClaimId) {
        show_notification('error', 'Claim ID not found');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('closeClaimBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Submit via AJAX
    $.ajax({
        url: `/claims/close-claim/${window.currentClaimId}`,
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            closure_reason: closureReason,
            send_whatsapp: true
        },
        success: function(response) {
            if (response.success) {
                show_notification('success', response.message);
                hideModal('closeClaimModal');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                show_notification('error', response.message || 'Failed to close claim');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while closing the claim';
            
            if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            show_notification('error', errorMessage);
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Update close claim WhatsApp preview
 */
function updateClaimClosurePreview() {
    const closureReason = document.getElementById('closure_reason').value.trim();
    const previewDiv = document.getElementById('whatsapp-preview-close');
    
    if (!closureReason) {
        previewDiv.innerHTML = '<em class="text-muted">Enter closure reason to see message preview</em>';
        return;
    }
    
    // Generate preview message
    let customerName = 'Customer';
    let claimReference = 'Claim';
    let vehicleText = '';
    
    if (window.currentClaim) {
        customerName = window.currentClaim.customerName;
        claimReference = window.currentClaim.claimNumber || `ID: ${window.currentClaimId}`;
        if (window.currentClaim.vehicleNumber) {
            vehicleText = ` for vehicle number *${window.currentClaim.vehicleNumber}*`;
        }
    } else if (window.claimCustomerName) {
        customerName = window.claimCustomerName;
        claimReference = window.claimReference || `ID: ${window.currentClaimId}`;
        if (window.claimVehicleNumber) {
            vehicleText = ` for vehicle number *${window.claimVehicleNumber}*`;
        }
    }
    
    const message = `Dear *${customerName}*,

Your Claim *${claimReference}*${vehicleText} has been closed.

*Closure Reason:* ${closureReason}

If you have any questions regarding this claim closure, please feel free to contact us.

Best regards,
Insurance Advisor
Website
Your Trusted Insurance Advisor
"Tagline"
Contact Phone`;
    
    previewDiv.textContent = message;
}

/**
 * Resend claim number via AJAX
 */
function resendClaimNumber(claimId) {
    showConfirmationModal({
        title: 'Resend Claim Number',
        message: 'Are you sure you want to resend the claim number via WhatsApp?',
        confirmText: 'Yes, Resend',
        confirmClass: 'btn-success',
        onConfirm: function() {
            showLoading('Sending WhatsApp message...');
            
            $.ajax({
                url: `/claims/resend-claim-number/${claimId}`,
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        show_notification('success', response.message);
                    } else {
                        show_notification('error', response.message || 'Failed to resend message');
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while sending the message';
                    show_notification('error', errorMessage);
                }
            });
        }
    });
}

/**
 * Send document list via WhatsApp
 */
function sendDocumentList(claimId) {
    showConfirmationModal({
        title: 'Send Document List',
        message: 'Are you sure you want to send the document list via WhatsApp?',
        confirmText: 'Yes, Send',
        confirmClass: 'btn-success',
        onConfirm: function() {
            showLoading('Sending WhatsApp message...');
            
            $.ajax({
                url: `/claims/intimate-document/${claimId}`,
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        show_notification('success', response.message);
                    } else {
                        show_notification('error', response.message || 'Failed to send document list');
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while sending the document list';
                    show_notification('error', errorMessage);
                }
            });
        }
    });
}