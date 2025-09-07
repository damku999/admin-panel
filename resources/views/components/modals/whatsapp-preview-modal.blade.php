{{--
    WhatsApp Preview Modal Component
    
    Usage:
    <x-modals.whatsapp-preview-modal 
        id="whatsappModal"
        title="Send WhatsApp Message"
        :customer="$customer"
        :message="$message"
        :document-url="$documentUrl"
        send-action="sendWhatsAppMessage"
        :show-preview="true">
    </x-modals.whatsapp-preview-modal>
--}}

@props([
    'id' => 'whatsappModal',
    'title' => 'Send WhatsApp Message',
    'customer' => null,
    'message' => '',
    'documentUrl' => '',
    'sendAction' => '',
    'showPreview' => true,
    'showCustomerInfo' => true,
    'showMessageEdit' => true,
    'showDocumentPreview' => true,
    'size' => 'lg'
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label">
    <div class="modal-dialog modal-{{ $size }}" role="document">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="fab fa-whatsapp me-2"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @if($showCustomerInfo && $customer)
                    <!-- Customer Information -->
                    <div class="customer-info mb-4 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-user text-primary me-2"></i>
                            Customer Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> {{ $customer->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Mobile:</strong> 
                                <span class="text-success">
                                    <i class="fas fa-phone me-1"></i>
                                    {{ $customer->mobile_number ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($showMessageEdit)
                    <!-- Message Editor -->
                    <div class="message-editor mb-4">
                        <label for="{{ $id }}_message" class="form-label">
                            <i class="fas fa-comment-dots text-success me-2"></i>
                            WhatsApp Message
                        </label>
                        <textarea id="{{ $id }}_message" 
                                  class="form-control whatsapp-message" 
                                  rows="6" 
                                  placeholder="Enter your WhatsApp message here..."
                                  oninput="updateMessagePreview('{{ $id }}')">{{ $message }}</textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Use *bold*, _italic_, and ~strikethrough~ for formatting
                        </small>
                    </div>
                @endif
                
                @if($showDocumentPreview && $documentUrl)
                    <!-- Document Preview -->
                    <div class="document-preview mb-4 p-3 border rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            Attached Document
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="document-icon me-3">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                            </div>
                            <div class="document-info flex-grow-1">
                                <div class="fw-bold">{{ basename($documentUrl) }}</div>
                                <small class="text-muted">PDF Document</small>
                            </div>
                            <div class="document-actions">
                                <a href="{{ $documentUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($showPreview)
                    <!-- WhatsApp Preview -->
                    <div class="whatsapp-preview">
                        <h6 class="mb-2">
                            <i class="fab fa-whatsapp text-success me-2"></i>
                            Message Preview
                        </h6>
                        <div class="whatsapp-chat-container">
                            <div class="whatsapp-message-bubble">
                                <div class="message-content" id="{{ $id }}_preview">
                                    {!! nl2br(e($message)) !!}
                                </div>
                                <div class="message-time">
                                    {{ now()->format('g:i A') }}
                                    <i class="fas fa-check-double text-primary ms-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                
                @if($sendAction)
                    <button type="button" 
                            class="btn btn-success" 
                            onclick="{{ $sendAction }}()"
                            id="{{ $id }}_sendBtn">
                        <i class="fab fa-whatsapp"></i> Send WhatsApp
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateMessagePreview(modalId) {
    const messageTextarea = document.getElementById(modalId + '_message');
    const previewDiv = document.getElementById(modalId + '_preview');
    
    if (!messageTextarea || !previewDiv) return;
    
    let message = messageTextarea.value;
    
    // Apply WhatsApp formatting
    message = message
        .replace(/\*(.*?)\*/g, '<strong>$1</strong>')  // Bold
        .replace(/_(.*?)_/g, '<em>$1</em>')            // Italic
        .replace(/~(.*?)~/g, '<del>$1</del>')          // Strikethrough
        .replace(/\n/g, '<br>');                       // Line breaks
    
    previewDiv.innerHTML = message || '<em class="text-muted">Your message preview will appear here...</em>';
}

// Helper functions for WhatsApp modal management
function showWhatsAppModal(modalId, customerData, message, documentUrl) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Update customer info if provided
    if (customerData) {
        const nameElement = modal.querySelector('.customer-info .col-md-6:first-child');
        const mobileElement = modal.querySelector('.customer-info .col-md-6:last-child span');
        
        if (nameElement) {
            nameElement.innerHTML = '<strong>Name:</strong> ' + (customerData.name || 'N/A');
        }
        if (mobileElement) {
            mobileElement.innerHTML = '<i class="fas fa-phone me-1"></i>' + (customerData.mobile_number || 'N/A');
        }
    }
    
    // Update message
    const messageTextarea = modal.querySelector('.whatsapp-message');
    if (messageTextarea && message) {
        messageTextarea.value = message;
        updateMessagePreview(modalId);
    }
    
    // Update document preview
    if (documentUrl) {
        const documentPreview = modal.querySelector('.document-preview');
        const documentLink = modal.querySelector('.document-preview a');
        
        if (documentPreview) {
            documentPreview.style.display = 'block';
        }
        if (documentLink) {
            documentLink.href = documentUrl;
        }
    }
    
    // Show modal
    showModal(modalId);
}

function getWhatsAppMessage(modalId) {
    const messageTextarea = document.getElementById(modalId + '_message');
    return messageTextarea ? messageTextarea.value : '';
}

function setWhatsAppLoading(modalId, loading) {
    const sendBtn = document.getElementById(modalId + '_sendBtn');
    if (!sendBtn) return;
    
    if (loading) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    } else {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fab fa-whatsapp"></i> Send WhatsApp';
    }
}

// Initialize message preview on modal show
document.addEventListener('DOMContentLoaded', function() {
    const whatsappModals = document.querySelectorAll('[id$="whatsappModal"], [id*="whatsapp"]');
    
    whatsappModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const modalId = this.id;
            updateMessagePreview(modalId);
            
            // Focus on message textarea
            const messageTextarea = this.querySelector('.whatsapp-message');
            if (messageTextarea) {
                messageTextarea.focus();
            }
        });
    });
});
</script>

<style>
/* WhatsApp Preview Styling */
.whatsapp-chat-container {
    background: linear-gradient(to bottom, #dddbd1, #d2dbdc);
    padding: 20px;
    border-radius: 8px;
    position: relative;
    min-height: 100px;
}

.whatsapp-message-bubble {
    background: #dcf8c6;
    padding: 12px 16px 8px 16px;
    border-radius: 18px 18px 4px 18px;
    max-width: 80%;
    margin-left: auto;
    position: relative;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.whatsapp-message-bubble::before {
    content: '';
    position: absolute;
    bottom: 0;
    right: -8px;
    width: 0;
    height: 0;
    border-left: 8px solid #dcf8c6;
    border-bottom: 8px solid transparent;
}

.message-content {
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
    margin-bottom: 5px;
}

.message-time {
    font-size: 11px;
    color: #667781;
    text-align: right;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

/* Customer Info Styling */
.customer-info {
    border-left: 4px solid #25d366;
}

/* Document Preview Styling */
.document-preview {
    border-left: 4px solid #007bff;
}

.document-icon {
    width: 50px;
    text-align: center;
}

/* Message Editor Styling */
.whatsapp-message {
    font-family: system-ui, -apple-system, sans-serif;
    resize: vertical;
    min-height: 120px;
}

.whatsapp-message:focus {
    border-color: #25d366;
    box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
}

/* Modal Header */
.modal-header.bg-success {
    background-color: #25d366 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .whatsapp-message-bubble {
        max-width: 90%;
    }
    
    .customer-info .row > div {
        margin-bottom: 8px;
    }
}
</style>