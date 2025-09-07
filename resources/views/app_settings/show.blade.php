@extends('layouts.app')

@section('title', 'View App Setting')

@section('content')
<div class="container-fluid">

    {{-- Alert Messages --}}
    @include('common.alert')

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                        <div class="mb-2 mb-md-0">
                            <h1 class="h4 mb-0 text-primary font-weight-bold">Setting Details</h1>
                            <small class="text-muted">Configuration setting: <code>{{ $appSetting->key }}</code></small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('app-settings.edit', $appSetting) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
                            </a>
                            <a href="{{ route('app-settings.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Back</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Setting Key</label>
                                <div class="form-control-plaintext"><code>{{ $appSetting->key }}</code></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-secondary">{{ ucfirst($appSetting->category) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Value</label>
                                <div class="form-control-plaintext border rounded p-3 bg-light">
                                    @if($appSetting->is_encrypted)
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted fst-italic">
                                                <i class="fas fa-lock"></i> *** Encrypted Value (Hidden for Security) ***
                                            </span>
                                            <button class="btn btn-sm btn-outline-info view-encrypted-btn" 
                                                    data-id="{{ $appSetting->id }}" 
                                                    data-key="{{ $appSetting->key }}"
                                                    title="View encrypted value">
                                                <i class="fas fa-eye"></i> View Value
                                            </button>
                                        </div>
                                    @else
                                        @if($appSetting->type === 'json')
                                            <pre class="mb-0"><code>{{ json_encode($appSetting->value, JSON_PRETTY_PRINT) }}</code></pre>
                                        @else
                                            {{ $appSetting->value ?: '(empty)' }}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Type</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $appSetting->type }}</span>
                                    @if($appSetting->is_encrypted)
                                        <span class="badge bg-warning ms-1">
                                            <i class="fas fa-lock"></i> Encrypted
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($appSetting->description)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div class="form-control-plaintext border rounded p-3 bg-light">
                            {{ $appSetting->description }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <div class="form-control-plaintext">
                                    @if($appSetting->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <div class="form-control-plaintext">
                                    {{ $appSetting->updated_at->format('Y-m-d H:i:s') }}
                                    <small class="text-muted d-block">{{ $appSetting->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <div class="form-control-plaintext">
                                    {{ $appSetting->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Setting ID</label>
                                <div class="form-control-plaintext">
                                    <code>#{{ $appSetting->id }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready - jQuery loaded');
    console.log('Found', $('.view-encrypted-btn').length, 'view encrypted buttons');
    
    // View encrypted value functionality (using event delegation)
    $(document).on('click', '.view-encrypted-btn', function() {
        console.log('View encrypted button clicked');
        const btn = $(this);
        const settingId = btn.data('id');
        const settingKey = btn.data('key');
        const icon = btn.find('i');
        
        console.log('Setting ID:', settingId, 'Setting Key:', settingKey);
        
        // Show loading state
        icon.removeClass('fa-eye').addClass('fa-spinner fa-spin');
        btn.prop('disabled', true);
        
        $.ajax({
            url: `{{ url('app-settings') }}/${settingId}/view-encrypted`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    // Create modal HTML with proper Bootstrap 4 structure
                    const modal = `
                        <div class="modal fade" id="encryptedValueModal" tabindex="-1" role="dialog" aria-labelledby="encryptedValueModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="encryptedValueModalLabel">
                                            <i class="fas fa-lock text-warning"></i>
                                            Encrypted Value: <code>${settingKey}</code>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="hideModal('encryptedValueModal')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Security Notice:</strong> This value is encrypted in the database. Handle with care.
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Decrypted Value:</label>
                                            <textarea class="form-control" rows="3" readonly style="font-family: monospace;">${response.value}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="hideModal('encryptedValueModal')">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove existing modal if any
                    $('#encryptedValueModal').remove();
                    
                    // Add modal to body
                    $('body').append(modal);
                    
                    // Show modal using our modal system
                    showModal('encryptedValueModal');
                    
                    // Clean up modal when hidden
                    $('#encryptedValueModal').on('hidden.bs.modal', function() {
                        $(this).remove();
                    });
                    
                    // Add click event for backdrop close
                    $('#encryptedValueModal').on('click', function(e) {
                        if (e.target === this) {
                            $(this).modal('hide');
                        }
                    });
                } else {
                    alert(response.message || 'Failed to retrieve encrypted value');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr, status, error);
                console.log('Response Text:', xhr.responseText);
                alert('Error retrieving encrypted value: ' + (xhr.responseJSON?.message || xhr.responseText || error));
            },
            complete: function() {
                // Reset button state
                icon.removeClass('fa-spinner fa-spin').addClass('fa-eye');
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection