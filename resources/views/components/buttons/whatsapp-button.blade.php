{{--
    WhatsApp Action Button Component
    
    Usage:
    <x-buttons.whatsapp-button 
        :item-id="$customer->id"
        action="send"
        :mobile="$customer->mobile_number"
        :message="$whatsappMessage"
        :document-url="$documentUrl"
        size="sm"
        title="Send WhatsApp">
        Send WhatsApp
    </x-buttons.whatsapp-button>
--}}

@props([
    'itemId' => null,
    'action' => 'send', // send, resend, preview
    'mobile' => '',
    'message' => '',
    'documentUrl' => '',
    'size' => 'sm',
    'variant' => 'success',
    'icon' => 'fab fa-whatsapp',
    'disabled' => false,
    'title' => '',
    'onclick' => '',
    'modalId' => 'whatsappModal'
])

@php
    $hasValidMobile = !empty($mobile) && strlen(preg_replace('/[^0-9]/', '', $mobile)) >= 10;
    $buttonDisabled = $disabled || !$hasValidMobile;
    $buttonVariant = $hasValidMobile ? $variant : 'outline-secondary';
    $buttonTitle = $title ?: ($hasValidMobile ? ucfirst($action) . ' WhatsApp' : 'No valid mobile number');
    
    // Generate onclick handler if not provided
    if (!$onclick && $itemId) {
        $functionName = match($action) {
            'send' => 'showSendWhatsAppModal',
            'resend' => 'showResendWhatsAppModal',
            'preview' => 'showWhatsAppPreview',
            default => 'showWhatsAppModal'
        };
        $onclick = "{$functionName}({$itemId})";
    }
@endphp

<button type="button" 
        class="btn btn-{{ $buttonVariant }} btn-{{ $size }} whatsapp-btn"
        @if($onclick) onclick="{{ $onclick }}" @endif
        @if($buttonDisabled) disabled @endif
        title="{{ $buttonTitle }}"
        data-item-id="{{ $itemId }}"
        data-action="{{ $action }}"
        data-mobile="{{ $mobile }}"
        data-message="{{ base64_encode($message) }}"
        data-document-url="{{ $documentUrl }}"
        data-modal-id="{{ $modalId }}">
    
    <i class="{{ $icon }}"></i>
    @if($slot->isNotEmpty())
        {{ $slot }}
    @else
        <span class="d-none d-sm-inline">{{ ucfirst($action) }}</span>
    @endif
</button>

<script>
// Global WhatsApp button handlers
function showSendWhatsAppModal(itemId) {
    const button = document.querySelector(`[data-item-id="${itemId}"][data-action="send"]`);
    if (!button) return;
    
    const data = getWhatsAppButtonData(button);
    showWhatsAppModal(data.modalId, {
        id: itemId,
        mobile_number: data.mobile
    }, data.message, data.documentUrl);
}

function showResendWhatsAppModal(itemId) {
    const button = document.querySelector(`[data-item-id="${itemId}"][data-action="resend"]`);
    if (!button) return;
    
    const data = getWhatsAppButtonData(button);
    showWhatsAppModal(data.modalId, {
        id: itemId,
        mobile_number: data.mobile
    }, data.message, data.documentUrl);
}

function showWhatsAppPreview(itemId) {
    const button = document.querySelector(`[data-item-id="${itemId}"][data-action="preview"]`);
    if (!button) return;
    
    const data = getWhatsAppButtonData(button);
    showWhatsAppModal(data.modalId, {
        id: itemId,
        mobile_number: data.mobile
    }, data.message, data.documentUrl);
}

function getWhatsAppButtonData(button) {
    return {
        itemId: button.dataset.itemId,
        action: button.dataset.action,
        mobile: button.dataset.mobile,
        message: button.dataset.message ? atob(button.dataset.message) : '',
        documentUrl: button.dataset.documentUrl,
        modalId: button.dataset.modalId || 'whatsappModal'
    };
}

// Send WhatsApp message function
function sendWhatsAppMessage(itemId, customMessage) {
    const button = document.querySelector(`[data-item-id="${itemId}"]`);
    if (!button) return;
    
    const data = getWhatsAppButtonData(button);
    const message = customMessage || getWhatsAppMessage(data.modalId) || data.message;
    
    if (!message.trim()) {
        show_notification('error', 'Please enter a message');
        return;
    }
    
    if (!data.mobile) {
        show_notification('error', 'Customer mobile number is required');
        return;
    }
    
    // Set loading state
    setWhatsAppLoading(data.modalId, true);
    
    // Prepare AJAX request
    const formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('message', message);
    formData.append('mobile', data.mobile);
    if (data.documentUrl) {
        formData.append('document_url', data.documentUrl);
    }
    
    // Determine endpoint based on current page or button data
    let endpoint = `/whatsapp/send/${itemId}`;
    if (window.location.pathname.includes('/claims/')) {
        endpoint = `/whatsapp/send/claim/${itemId}`;
    } else if (window.location.pathname.includes('/quotations/')) {
        endpoint = `/whatsapp/send/quotation/${itemId}`;
    } else if (window.location.pathname.includes('/customers/')) {
        endpoint = `/whatsapp/send/customer/${itemId}`;
    }
    
    // Send AJAX request
    $.ajax({
        url: endpoint,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            setWhatsAppLoading(data.modalId, false);
            hideModal(data.modalId);
            
            if (response.success) {
                show_notification('success', response.message || 'WhatsApp sent successfully');
            } else {
                show_notification('error', response.message || 'Failed to send WhatsApp');
            }
        },
        error: function(xhr) {
            setWhatsAppLoading(data.modalId, false);
            
            let errorMessage = 'Failed to send WhatsApp';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            show_notification('error', errorMessage);
        }
    });
}

// Initialize WhatsApp buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to disabled buttons
    document.querySelectorAll('.whatsapp-btn[disabled]').forEach(button => {
        button.addEventListener('mouseenter', function() {
            const mobile = this.dataset.mobile;
            if (!mobile || mobile.trim() === '') {
                this.title = 'No mobile number available';
            } else {
                this.title = 'Invalid mobile number format';
            }
        });
    });
});
</script>

<style>
.whatsapp-btn {
    transition: all 0.2s ease;
}

.whatsapp-btn:not([disabled]):hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.whatsapp-btn.btn-success {
    background-color: #25d366;
    border-color: #25d366;
}

.whatsapp-btn.btn-success:hover {
    background-color: #20b954;
    border-color: #20b954;
}

.whatsapp-btn[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

.whatsapp-btn .fab.fa-whatsapp {
    color: #25d366;
}

.whatsapp-btn.btn-success .fab.fa-whatsapp {
    color: white;
}

/* Responsive text hiding */
@media (max-width: 576px) {
    .whatsapp-btn .d-none.d-sm-inline {
        display: none !important;
    }
}
</style>