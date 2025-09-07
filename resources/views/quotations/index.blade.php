@extends('layouts.app')

@section('title', 'Quotations List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Quotations Management</h1>
                        <small class="text-muted">Manage insurance quotations and quotes</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @can('quotation-create')
                            <x-buttons.action-button 
                                variant="primary" 
                                size="sm" 
                                icon="fas fa-plus"
                                href="{{ route('quotations.create') }}"
                                title="Create New Quotation">
                                <span class="d-none d-sm-inline">Create New</span>
                            </x-buttons.action-button>
                        @endcan
                        <x-buttons.export-button 
                            export-url="{{ route('quotations.export') }}"
                            :formats="['xlsx', 'csv']"
                            :show-dropdown="true"
                            :with-filters="true"
                            title="Export Quotations">
                            Export Quotations
                        </x-buttons.export-button>
                    </div>
                </div>
                <!-- Enhanced Search and Filters -->
                <form action="{{ route('quotations.index') }}" method="GET" role="search" class="quotations-search-form">
                    <div class="row g-2 align-items-end">
                        <!-- Main Search Field -->
                        <div class="col-lg-5 col-md-8 col-sm-12 mb-2">
                            <x-forms.search-field 
                                id="quotationsSearch"
                                name="search"
                                placeholder="Search by customer name, mobile, vehicle number, or reference"
                                :value="request('search')"
                                :with-button="true"
                                button-text="Search"
                                button-class="btn-primary"
                                button-icon="fas fa-search"
                                :clear-button="true"
                                button-onclick="document.querySelector('.quotations-search-form').submit()" />
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <label for="status" class="form-label text-sm fw-medium">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm select2-enable" 
                                    data-placeholder="All Status" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>
                                    📝 Draft
                                </option>
                                <option value="Generated" {{ request('status') == 'Generated' ? 'selected' : '' }}>
                                    ⚙️ Generated
                                </option>
                                <option value="Sent" {{ request('status') == 'Sent' ? 'selected' : '' }}>
                                    📤 Sent
                                </option>
                                <option value="Accepted" {{ request('status') == 'Accepted' ? 'selected' : '' }}>
                                    ✅ Accepted
                                </option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                                    ❌ Rejected
                                </option>
                            </select>
                        </div>
                        
                        <!-- Policy Type Filter -->
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <label for="policy_type" class="form-label text-sm fw-medium">Policy Type</label>
                            <select name="policy_type" id="policy_type" class="form-select form-select-sm select2-enable" 
                                    data-placeholder="All Types" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="Comprehensive" {{ request('policy_type') == 'Comprehensive' ? 'selected' : '' }}>
                                    🛡️ Comprehensive
                                </option>
                                <option value="Third Party" {{ request('policy_type') == 'Third Party' ? 'selected' : '' }}>
                                    🔹 Third Party
                                </option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-lg-3 col-md-12 mb-2">
                            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-undo me-1"></i> Reset Filters
                            </a>
                        </div>
                        
                        <!-- Quick Filter Badges -->
                        @if(request()->hasAny(['search', 'status', 'policy_type']))
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-1 mt-2 mb-1">
                                    <small class="text-muted me-2">Active filters:</small>
                                    @if(request('search'))
                                        <span class="badge bg-primary">
                                            <i class="fas fa-search me-1"></i>Search: {{ request('search') }}
                                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if(request('status'))
                                        <span class="badge bg-info">
                                            <i class="fas fa-filter me-1"></i>Status: {{ request('status') }}
                                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if(request('policy_type'))
                                        <span class="badge bg-success">
                                            <i class="fas fa-shield-alt me-1"></i>Type: {{ request('policy_type') }}
                                            <a href="{{ request()->fullUrlWithQuery(['policy_type' => null]) }}" class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body">
                <!-- Enhanced Table with DataTableManager Integration -->
                <div class="table-responsive">

                    <table class="table table-hover table-bordered data-table" id="quotationsDataTable" 
                           data-datatable="true"
                           data-page-length="25"
                           data-server-side="false"
                           data-responsive="true"
                           data-order='[[6, "desc"]]'
                           data-column-defs='[
                               { "targets": [7], "orderable": false }
                           ]'>
                        <thead class="table-dark">
                                <tr>
                                    <th>Quote Ref</th>
                                    <th>Customer</th>
                                    <th>Vehicle Details</th>
                                    <th>Policy Type</th>
                                    <th>Best Quote</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $quotation)
                                    <tr>
                                        <td>
                                            <strong>{{ $quotation->getQuoteReference() }}</strong>
                                        </td>
                                        <td data-sort="{{ $quotation->customer->name }}">
                                            <div class="customer-info">
                                                <strong class="d-block">{{ $quotation->customer->name }}</strong>
                                                @if($quotation->customer->mobile_number)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-phone me-1"></i>{{ $quotation->customer->mobile_number }}
                                                    </small>
                                                @endif
                                                @if($quotation->customer->email)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-envelope me-1"></i>{{ Str::limit($quotation->customer->email, 25) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-sort="{{ $quotation->make_model_variant }}">
                                            <div class="vehicle-info">
                                                <strong class="d-block">{{ $quotation->make_model_variant }}</strong>
                                                <small class="text-muted d-block">
                                                    <span class="badge bg-light text-dark me-1">{{ $quotation->vehicle_number ?? 'To be registered' }}</span>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-gas-pump me-1"></i>{{ $quotation->fuel_type }} |
                                                    <i class="fas fa-rupee-sign me-1"></i>IDV: {{ number_format($quotation->total_idv) }}
                                                </small>
                                            </div>
                                        </td>
                                        <td data-sort="{{ $quotation->policy_type }}">
                                            <x-buttons.status-badge 
                                                :status="$quotation->policy_type"
                                                :status-colors="[
                                                    'Comprehensive' => 'primary',
                                                    'Third Party' => 'info'
                                                ]" />
                                            <small class="d-block text-muted mt-1">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ $quotation->policy_tenure_years }} Year(s)
                                            </small>
                                        </td>
                                        <td>
                                            @if($quotation->bestQuote())
                                                <strong class="text-success">{{ $quotation->bestQuote()->getFormattedPremium() }}</strong><br>
                                                <small class="text-muted">{{ $quotation->bestQuote()->insuranceCompany->name }}</small>
                                            @else
                                                <span class="text-muted">Not generated</span>
                                            @endif
                                        </td>
                                        <td data-sort="{{ $quotation->status }}">
                                            <x-buttons.status-badge 
                                                :status="$quotation->status"
                                                :status-colors="[
                                                    'Draft' => 'secondary',
                                                    'Generated' => 'info',
                                                    'Sent' => 'warning',
                                                    'Accepted' => 'success',
                                                    'Rejected' => 'danger'
                                                ]" />
                                            @if($quotation->sent_at)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-paper-plane me-1"></i>Sent: {{ $quotation->sent_at->format('M d, H:i') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $quotation->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $quotation->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <x-tables.action-column>
                                                <!-- WhatsApp Button -->
                                                @if($quotation->quotationCompanies->count() > 0)
                                                    @can('quotation-send-whatsapp')
                                                        <x-buttons.whatsapp-button 
                                                            onclick="{{ $quotation->status === 'Sent' ? 'showResendWhatsAppModal(' . $quotation->id . ')' : 'showSendWhatsAppModal(' . $quotation->id . ')' }}"
                                                            title="{{ $quotation->status === 'Sent' ? 'Resend via WhatsApp' : 'Send via WhatsApp' }}" 
                                                            :variant="$quotation->status === 'Sent' ? 'warning' : 'success'"
                                                            size="sm" />
                                                    @endcan
                                                @endif

                                                <!-- Edit Button -->
                                                @can('quotation-edit')
                                                    <x-buttons.action-button 
                                                        variant="primary" 
                                                        size="sm" 
                                                        icon="fas fa-edit"
                                                        href="{{ route('quotations.edit', $quotation) }}"
                                                        title="Edit Quotation" />
                                                @endcan
                                                
                                                <!-- Download Button -->
                                                @if($quotation->quotationCompanies->count() > 0)
                                                    @can('quotation-download-pdf')
                                                        <x-buttons.action-button 
                                                            variant="info" 
                                                            size="sm" 
                                                            icon="fas fa-download"
                                                            href="{{ route('quotations.download-pdf', $quotation) }}"
                                                            title="Download PDF" />
                                                    @endcan
                                                @endif

                                                <!-- View Details -->
                                                @can('quotation-edit')
                                                    <x-buttons.action-button 
                                                        variant="secondary" 
                                                        size="sm" 
                                                        icon="fas fa-eye"
                                                        href="{{ route('quotations.show', $quotation) }}"
                                                        title="View Details" />
                                                @endcan

                                                <!-- Generate Quotes -->
                                                @if($quotation->quotationCompanies->count() == 0)
                                                    @can('quotation-generate')
                                                        <form method="POST" action="{{ route('quotations.generate-quotes', $quotation) }}" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <x-buttons.action-button 
                                                                variant="warning" 
                                                                size="sm" 
                                                                icon="fas fa-cog"
                                                                type="submit"
                                                                title="Generate Quotes" />
                                                        </form>
                                                    @endcan
                                                @endif
                                                
                                                <!-- Delete Button -->
                                                @can('quotation-delete')
                                                    <x-buttons.action-button 
                                                        variant="danger" 
                                                        size="sm" 
                                                        icon="fas fa-trash"
                                                        onclick="showDeleteQuotationModal({{ $quotation->id }})"
                                                        title="Delete Quotation" />
                                                @endcan
                                            </x-tables.action-column>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-file-alt me-2"></i>
                                            No quotations found. Create your first insurance quotation to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Showing {{ $quotations->firstItem() ?? 0 }} to {{ $quotations->lastItem() ?? 0 }} 
                                of {{ $quotations->total() }} quotations
                            </small>
                        </div>
                        <div>
                            {{ $quotations->appends($request ?? request())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for each quotation -->
@foreach($quotations as $quotation)
    @if($quotation->quotationCompanies->count() > 0)
        <!-- Send WhatsApp Modal -->
        <div class="modal fade" id="sendWhatsAppModal{{ $quotation->id }}" tabindex="-1" role="dialog" aria-labelledby="sendWhatsAppModalLabel{{ $quotation->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h6 class="modal-title" id="sendWhatsAppModalLabel{{ $quotation->id }}">
                            <i class="fab fa-whatsapp"></i> Send Quotation via WhatsApp
                        </h6>
                        <button type="button" class="close text-white" onclick="hideWhatsAppModal('sendWhatsAppModal{{ $quotation->id }}')" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fab fa-whatsapp fa-2x text-success"></i>
                        </div>
                        <p class="text-center">Send quotation with PDF attachment to:</p>
                        <div class="alert alert-info">
                            <strong>{{ $quotation->getQuoteReference() }}</strong><br>
                            <strong>Customer:</strong> {{ $quotation->customer->name }}<br>
                            <strong>WhatsApp:</strong> {{ $quotation->whatsapp_number ?? $quotation->customer->mobile_number }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="hideWhatsAppModal('sendWhatsAppModal{{ $quotation->id }}')">Cancel</button>
                        <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> Send Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resend WhatsApp Modal -->
        <div class="modal fade" id="resendWhatsAppModal{{ $quotation->id }}" tabindex="-1" role="dialog" aria-labelledby="resendWhatsAppModalLabel{{ $quotation->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h6 class="modal-title" id="resendWhatsAppModalLabel{{ $quotation->id }}">
                            <i class="fab fa-whatsapp"></i> Resend Quotation via WhatsApp
                        </h6>
                        <button type="button" class="close" onclick="hideWhatsAppModal('resendWhatsAppModal{{ $quotation->id }}')" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fab fa-whatsapp fa-2x text-warning"></i>
                        </div>
                        <p class="text-center">Resend quotation with updated PDF attachment to:</p>
                        <div class="alert alert-warning">
                            <strong>{{ $quotation->getQuoteReference() }}</strong><br>
                            <strong>Customer:</strong> {{ $quotation->customer->name }}<br>
                            <strong>WhatsApp:</strong> {{ $quotation->whatsapp_number ?? $quotation->customer->mobile_number }}<br>
                            <strong>Last Sent:</strong> {{ $quotation->sent_at ? $quotation->sent_at->format('d M Y, H:i') : 'Not available' }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="hideWhatsAppModal('resendWhatsAppModal{{ $quotation->id }}')">Cancel</button>
                        <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fab fa-whatsapp"></i> Resend Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Quotation Modal -->
    <div class="modal fade" id="deleteQuotationModal{{ $quotation->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteQuotationModalLabel{{ $quotation->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title" id="deleteQuotationModalLabel{{ $quotation->id }}">
                        <i class="fas fa-trash"></i> Delete Quotation
                    </h6>
                    <button type="button" class="close text-white" onclick="hideDeleteModal('deleteQuotationModal{{ $quotation->id }}')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                    <p class="text-center"><strong>Are you sure you want to delete this quotation?</strong></p>
                    <div class="alert alert-danger">
                        <strong>{{ $quotation->getQuoteReference() }}</strong><br>
                        <strong>Customer:</strong> {{ $quotation->customer->name }}<br>
                        <strong>Vehicle:</strong> {{ $quotation->make_model_variant }}
                        @if($quotation->quotationCompanies->count() > 0)
                            <br><strong>Company Quotes:</strong> {{ $quotation->quotationCompanies->count() }} will also be deleted
                        @endif
                    </div>
                    <p class="text-warning small"><strong>Warning:</strong> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteModal('deleteQuotationModal{{ $quotation->id }}')">Cancel</button>
                    <form method="POST" action="{{ route('quotations.delete', $quotation) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Modal functions are now centralized in layouts/app.blade.php --}}

@endsection

@section('scripts')
    <!-- Quotations module JavaScript -->
    <script src="{{ asset('admin/js/modules/quotations-common.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTableManager for quotations table
            if (window.CoreManager && CoreManager.has('datatables')) {
                const dataTableManager = CoreManager.get('datatables');
                
                // Initialize the quotations table with advanced features
                dataTableManager.initializeTable('quotationsDataTable', {
                    pageLength: 25,
                    responsive: true,
                    stateSave: true,
                    order: [[6, 'desc']], // Sort by created date (newest first)
                    columnDefs: [
                        { targets: [7], orderable: false }, // Actions column not sortable
                        { targets: [0], type: 'string' }, // Quote ref column text sorting
                        { targets: [1], type: 'string' }, // Customer column text sorting
                        { targets: [6], type: 'date' } // Created date column date sorting
                    ],
                    language: {
                        search: "Search quotations:",
                        lengthMenu: "Show _MENU_ quotations per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ quotations",
                        infoFiltered: "(filtered from _MAX_ total quotations)",
                        emptyTable: "No quotations found. Create your first insurance quotation to get started."
                    }
                });
                
                console.log('✅ Quotations DataTable initialized with advanced features');
            } else {
                console.warn('⚠️ DataTableManager not available, falling back to basic table');
            }
            
            // Initialize Select2 for better dropdowns
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-enable').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    minimumResultsForSearch: -1 // Disable search for small lists
                });
            }
            
            // Enhanced confirmation for WhatsApp send operations
            $('button[onclick*="showSendWhatsAppModal"], button[onclick*="showResendWhatsAppModal"]').on('click', function(e) {
                const onclick = $(this).attr('onclick');
                const quotationId = onclick.match(/\d+/)[0];
                const isResend = onclick.includes('Resend');
                const customerName = $(this).closest('tr').find('.customer-info strong').text();
                
                if (window.CoreManager && CoreManager.has('modals')) {
                    const modalManager = CoreManager.get('modals');
                    modalManager.confirm({
                        title: `Confirm WhatsApp ${isResend ? 'Resend' : 'Send'}`,
                        message: `Are you sure you want to ${isResend ? 'resend' : 'send'} the quotation via WhatsApp to "${customerName}"?`,
                        confirmText: `Yes, ${isResend ? 'Resend' : 'Send'}`,
                        confirmClass: isResend ? 'btn-warning' : 'btn-success',
                        onConfirm: function() {
                            // Show loading state
                            if (window.CoreManager && CoreManager.has('notifications')) {
                                const notificationManager = CoreManager.get('notifications');
                                notificationManager.loading(`${isResend ? 'Resending' : 'Sending'} quotation via WhatsApp...`);
                            }
                            // Execute original onclick
                            eval(onclick);
                        }
                    });
                    
                    // Prevent original onclick execution
                    e.preventDefault();
                    return false;
                }
            });
            
            // Enhanced confirmation for delete operations
            $('button[onclick*="showDeleteQuotationModal"]').on('click', function(e) {
                const onclick = $(this).attr('onclick');
                const quotationId = onclick.match(/\d+/)[0];
                const customerName = $(this).closest('tr').find('.customer-info strong').text();
                const quoteRef = $(this).closest('tr').find('td:first strong').text();
                
                if (window.CoreManager && CoreManager.has('modals')) {
                    const modalManager = CoreManager.get('modals');
                    modalManager.confirm({
                        title: 'Confirm Quotation Deletion',
                        message: `Are you sure you want to delete quotation "${quoteRef}" for customer "${customerName}"? This action cannot be undone.`,
                        confirmText: 'Yes, Delete',
                        confirmClass: 'btn-danger',
                        onConfirm: function() {
                            // Show loading state
                            if (window.CoreManager && CoreManager.has('notifications')) {
                                const notificationManager = CoreManager.get('notifications');
                                notificationManager.loading('Deleting quotation...');
                            }
                            // Execute original onclick
                            eval(onclick);
                        }
                    });
                    
                    // Prevent original onclick execution
                    e.preventDefault();
                    return false;
                }
            });
        });
        
        // Global functions for modal operations (maintain backward compatibility)
        function showSendWhatsAppModal(quotationId) {
            showModal('sendWhatsAppModal' + quotationId);
        }
        
        function showResendWhatsAppModal(quotationId) {
            showModal('resendWhatsAppModal' + quotationId);
        }
        
        function showDeleteQuotationModal(quotationId) {
            showModal('deleteQuotationModal' + quotationId);
        }
        
        function hideWhatsAppModal(modalId) {
            hideModal(modalId);
        }
    </script>
@endsection