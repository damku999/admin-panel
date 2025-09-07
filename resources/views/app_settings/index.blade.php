@extends('layouts.app')

@section('title', 'App Settings')

@push('css')
    <style>
        .setting-value {
            max-width: 200px;
            word-break: break-word;
        }
        .encrypted-value {
            font-style: italic;
            color: #6c757d;
        }
        .category-badge {
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                <div class="mb-2 mb-md-0">
                    <h1 class="h4 mb-0 text-primary font-weight-bold">App Settings Management</h1>
                    <small class="text-muted">Manage application configuration settings</small>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('app-settings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add Setting</span>
                    </a>
                    <a href="javascript:void(0)" class="btn btn-warning btn-sm" 
                       onclick="clearSettingsCache()">
                        <i class="fas fa-sync-alt"></i> <span class="d-none d-sm-inline">Clear Cache</span>
                    </a>
                    <x-buttons.export-button 
                        export-url="{{ route('app-settings.export') }}"
                        :formats="['xlsx', 'csv']"
                        :show-dropdown="true"
                        :with-filters="true"
                        title="Export App Settings">
                        Export Settings
                    </x-buttons.export-button>
                </div>
            </div>
            
            <!-- Category Filter -->
            <form method="GET" action="{{ route('app-settings.index') }}" role="search">
                <div class="input-group">
                    <select name="category" class="form-control" onchange="this.form.submit()" style="margin-right: 10px;">
                        <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <a href="{{ route('app-settings.index') }}" class="btn btn-default">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                    <tr>
                                        <td class="fw-bold">{{ $setting->key }}</td>
                                        <td class="setting-value">
                                            @if($setting->is_encrypted)
                                                <span class="encrypted-value">*** Encrypted ***</span>
                                                <button class="btn btn-sm btn-outline-info mr-2 view-encrypted-btn" 
                                                        data-id="{{ $setting->id }}" 
                                                        data-key="{{ $setting->key }}"
                                                        title="View encrypted value">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @else
                                                @if($setting->type === 'json')
                                                    <small class="text-muted">JSON Data</small>
                                                @else
                                                    {{ Str::limit($setting->value, 50) }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $setting->type }}</span>
                                            @if($setting->is_encrypted)
                                                <span class="badge badge-warning ml-1">Encrypted</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary category-badge">{{ $setting->category }}</span>
                                        </td>
                                        <td class="small text-muted">{{ Str::limit($setting->description, 100) }}</td>
                                        <td>
                                            <button class="btn btn-sm toggle-status {{ $setting->is_active ? 'btn-success' : 'btn-danger' }}" 
                                                    data-id="{{ $setting->id }}"
                                                    data-active="{{ $setting->is_active }}">
                                                {{ $setting->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('app-settings.show', $setting) }}" 
                                                   class="btn btn-outline-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('app-settings.edit', $setting) }}" 
                                                   class="btn btn-outline-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('app-settings.destroy', $setting) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return deleteAppSetting(event, '{{ $setting->key }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No settings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
            </div>

            <!-- Pagination -->
            @if($settings->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $settings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready - jQuery loaded');
    console.log('Found', $('.view-encrypted-btn').length, 'view encrypted buttons');
    
    // Toggle status functionality
    $('.toggle-status').click(function() {
        const btn = $(this);
        const settingId = btn.data('id');
        const isActive = btn.data('active');
        
        $.ajax({
            url: `/app-settings/${settingId}/toggle`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (response.is_active) {
                        btn.removeClass('btn-danger').addClass('btn-success').text('Active');
                        btn.data('active', true);
                    } else {
                        btn.removeClass('btn-success').addClass('btn-danger').text('Inactive');
                        btn.data('active', false);
                    }
                    
                    // Show toast or alert
                    if (typeof showNotification === 'function') {
                        showNotification('success', response.message);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('Error updating setting status');
            }
        });
    });

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

function clearSettingsCache() {
    showConfirmationModal({
        title: 'Clear Settings Cache',
        message: 'Are you sure you want to clear settings cache? This will reload all settings from the database.',
        confirmText: 'Yes, Clear Cache',
        confirmClass: 'btn-warning',
        onConfirm: function() {
            window.location.href = "{{ route('app-settings.clear-cache') }}";
        }
    });
}

function deleteAppSetting(event, settingKey) {
    event.preventDefault();
    showConfirmationModal({
        title: 'Delete Setting',
        message: `Are you sure you want to delete the setting "${settingKey}"? This action cannot be undone.`,
        confirmText: 'Yes, Delete',
        confirmClass: 'btn-danger',
        onConfirm: function() {
            event.target.submit();
        }
    });
    return false;
}
</script>
@endsection