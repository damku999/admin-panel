{{--
    Status Badge Component
    
    Usage:
    <x-buttons.status-badge 
        :status="$claim->status"
        :status-colors="{
            'open': 'primary',
            'in_progress': 'warning', 
            'closed': 'success',
            'rejected': 'danger'
        }"
        size="sm"
        :clickable="true"
        onclick="changeStatus('{{ $claim->id }}')">
    </x-buttons.status-badge>
--}}

@props([
    'status' => '',
    'statusColors' => [],
    'size' => 'sm', // sm, md, lg
    'variant' => '', // Override automatic color detection
    'clickable' => false,
    'onclick' => '',
    'title' => '',
    'icon' => '',
    'pulse' => false, // Add pulsing animation for active statuses
    'rounded' => true
])

@php
    // Default status color mappings
    $defaultColors = [
        // General statuses
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'draft' => 'secondary',
        'published' => 'success',
        'suspended' => 'warning',
        'deleted' => 'danger',
        'archived' => 'secondary',
        
        // Claim specific
        'open' => 'primary',
        'in_progress' => 'warning',
        'investigating' => 'info',
        'closed' => 'success',
        'settled' => 'success',
        'denied' => 'danger',
        'cancelled' => 'secondary',
        
        // Payment specific
        'paid' => 'success',
        'unpaid' => 'danger',
        'partial' => 'warning',
        'refunded' => 'info',
        'processing' => 'warning',
        
        // Insurance specific
        'active_policy' => 'success',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'lapsed' => 'warning',
        'renewed' => 'info',
        
        // Boolean-like
        'yes' => 'success',
        'no' => 'danger',
        'enabled' => 'success',
        'disabled' => 'secondary',
        'true' => 'success',
        'false' => 'secondary',
    ];
    
    $allColors = array_merge($defaultColors, $statusColors);
    
    // Determine badge variant
    $badgeVariant = $variant ?: ($allColors[strtolower($status)] ?? 'secondary');
    
    // Format status text
    $statusText = ucwords(str_replace(['_', '-'], ' ', $status));
    
    // Determine badge classes
    $badgeClasses = [
        'badge',
        "badge-{$size}",
        "bg-{$badgeVariant}",
        $clickable ? 'status-badge-clickable' : '',
        $pulse ? 'status-badge-pulse' : '',
        $rounded ? '' : 'rounded-0'
    ];
    
    $badgeClasses = array_filter($badgeClasses);
    
    // Auto-generate title if not provided
    $badgeTitle = $title ?: "Status: {$statusText}";
    if ($clickable && !$title) {
        $badgeTitle .= ' (Click to change)';
    }
    
    // Auto-generate icons based on status
    $statusIcon = $icon;
    if (!$icon) {
        $statusIcon = match(strtolower($status)) {
            'active', 'approved', 'success', 'paid', 'closed', 'settled' => 'fas fa-check-circle',
            'pending', 'processing', 'in_progress', 'investigating' => 'fas fa-clock',
            'rejected', 'denied', 'failed', 'unpaid', 'expired' => 'fas fa-times-circle',
            'cancelled', 'suspended', 'archived' => 'fas fa-pause-circle',
            'draft' => 'fas fa-edit',
            'published' => 'fas fa-eye',
            default => ''
        };
    }
@endphp

@if($clickable && $onclick)
    <button type="button" 
            class="btn p-0 border-0 bg-transparent {{ implode(' ', $badgeClasses) }}"
            onclick="{{ $onclick }}"
            title="{{ $badgeTitle }}"
            data-status="{{ $status }}">
        @if($statusIcon)
            <i class="{{ $statusIcon }} me-1"></i>
        @endif
        {{ $statusText }}
    </button>
@else
    <span class="{{ implode(' ', $badgeClasses) }}" 
          title="{{ $badgeTitle }}"
          data-status="{{ $status }}"
          @if($clickable && $onclick) style="cursor: pointer;" onclick="{{ $onclick }}" @endif>
        @if($statusIcon)
            <i class="{{ $statusIcon }} me-1"></i>
        @endif
        {{ $statusText }}
    </span>
@endif

<style>
/* Badge Size Variations */
.badge-sm {
    font-size: 0.75rem;
    padding: 0.35rem 0.6rem;
}

.badge-md {
    font-size: 0.875rem;
    padding: 0.5rem 0.8rem;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.6rem 1rem;
}

/* Clickable Status Badge */
.status-badge-clickable {
    transition: all 0.2s ease;
    cursor: pointer !important;
    text-decoration: none;
}

.status-badge-clickable:hover {
    opacity: 0.8;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.status-badge-clickable:active {
    transform: translateY(0);
}

/* Pulsing Animation */
.status-badge-pulse {
    animation: statusPulse 2s infinite;
}

@keyframes statusPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0.7);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(var(--bs-primary-rgb), 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0);
    }
}

.status-badge-pulse.bg-warning {
    --bs-primary-rgb: var(--bs-warning-rgb);
}

.status-badge-pulse.bg-danger {
    --bs-primary-rgb: var(--bs-danger-rgb);
}

.status-badge-pulse.bg-success {
    --bs-primary-rgb: var(--bs-success-rgb);
}

.status-badge-pulse.bg-info {
    --bs-primary-rgb: var(--bs-info-rgb);
}

/* Focus styles for accessibility */
.status-badge-clickable:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

/* Status-specific enhancements */
.badge.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-success-rgb), var(--bs-bg-opacity));
}

.badge.bg-warning {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-warning-rgb), var(--bs-bg-opacity));
    color: var(--bs-dark) !important;
}

.badge.bg-danger {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-danger-rgb), var(--bs-bg-opacity));
}

.badge.bg-info {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-info-rgb), var(--bs-bg-opacity));
}

.badge.bg-secondary {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-secondary-rgb), var(--bs-bg-opacity));
}

.badge.bg-primary {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity));
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.8rem;
    }
    
    .badge-md {
        font-size: 0.75rem;
        padding: 0.4rem 0.7rem;
    }
}
</style>

<script>
// Global status badge management
function changeStatus(itemId, currentStatus, availableStatuses, endpoint) {
    if (!availableStatuses || availableStatuses.length === 0) {
        show_notification('warning', 'No status options available');
        return;
    }
    
    // Create status selection modal or dropdown
    let statusOptions = availableStatuses.map(status => {
        const isSelected = status.value === currentStatus ? 'selected' : '';
        return `<option value="${status.value}" ${isSelected}>${status.label}</option>`;
    }).join('');
    
    const modalHtml = `
        <div class="modal fade" id="statusChangeModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <select id="newStatusSelect" class="form-select">
                            ${statusOptions}
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitStatusChange('${itemId}', '${endpoint}')">
                            Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page and show it
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('statusChangeModal'));
    modal.show();
    
    // Clean up modal when hidden
    document.getElementById('statusChangeModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function submitStatusChange(itemId, endpoint) {
    const newStatus = document.getElementById('newStatusSelect').value;
    const modal = bootstrap.Modal.getInstance(document.getElementById('statusChangeModal'));
    
    // Close modal
    modal.hide();
    
    // Submit AJAX request
    $.ajax({
        url: endpoint || `/update-status/${itemId}`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: newStatus
        },
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                show_notification('success', response.message || 'Status updated successfully');
                
                // Update the badge if still on page
                const badge = document.querySelector(`[data-status][onclick*="${itemId}"]`);
                if (badge) {
                    // Reload page or update badge dynamically
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                show_notification('error', response.message || 'Failed to update status');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to update status';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            show_notification('error', errorMessage);
        }
    });
}
</script>