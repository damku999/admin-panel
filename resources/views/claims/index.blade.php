@extends('layouts.app')

@section('title', 'Claims Management')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Claims List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div
                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Claims Management</h1>
                        <small class="text-muted">Manage insurance claims for all policies</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('claim-create'))
                            <a href="{{ route('claims.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">New Claim</span>
                            </a>
                        @endif
                        <x-buttons.export-button export-url="{{ route('claims.export') }}" :formats="['xlsx', 'csv', 'pdf']"
                            :show-dropdown="true" :with-filters="true" title="Export Claims Data">
                            Export Claims
                        </x-buttons.export-button>
                    </div>
                </div>
                <!-- Advanced Search and Filters -->
                <form action="{{ route('claims.index') }}" method="GET" role="search" class="claims-search-form">
                    <div class="row g-2 align-items-end">
                        <!-- Main Search Field -->
                        <div class="col-lg-4 col-md-6 mb-2">
                            <x-forms.search-field id="claimsSearch" name="search"
                                placeholder="Search by claim number, policy no, vehicle no, or customer name"
                                :value="request('search')" :with-button="true" button-text="Search" button-class="btn-primary"
                                button-icon="fas fa-search" :clear-button="true"
                                button-onclick="document.querySelector('.claims-search-form').submit()" />
                        </div>

                        <!-- Insurance Type Filter -->
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                            <label for="insurance_type" class="form-label text-sm fw-medium">Insurance Type</label>
                            <select name="insurance_type" id="insurance_type" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="Health" {{ request('insurance_type') == 'Health' ? 'selected' : '' }}>🏥
                                    Health</option>
                                <option value="Truck" {{ request('insurance_type') == 'Truck' ? 'selected' : '' }}>🚛 Truck
                                </option>
                            </select>
                        </div>

                        <!-- Claim Status Filter -->
                        <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                            <label for="claim_status" class="form-label text-sm fw-medium">Claim Status</label>
                            <select name="claim_status" id="claim_status" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Initiated" {{ request('claim_status') == 'Initiated' ? 'selected' : '' }}>⏳
                                    Initiated</option>
                                <option value="Documents Collected"
                                    {{ request('claim_status') == 'Documents Collected' ? 'selected' : '' }}>📄 Documents
                                    Collected</option>
                                <option value="Submitted to Insurance"
                                    {{ request('claim_status') == 'Submitted to Insurance' ? 'selected' : '' }}>📤 Submitted
                                    to Insurance</option>
                                <option value="Under Review"
                                    {{ request('claim_status') == 'Under Review' ? 'selected' : '' }}>🔍 Under Review
                                </option>
                                <option value="Approved" {{ request('claim_status') == 'Approved' ? 'selected' : '' }}>✅
                                    Approved</option>
                                <option value="Rejected" {{ request('claim_status') == 'Rejected' ? 'selected' : '' }}>❌
                                    Rejected</option>
                                <option value="Closed" {{ request('claim_status') == 'Closed' ? 'selected' : '' }}>🔒
                                    Closed</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-lg-2 col-md-12 mb-2">
                            <a href="{{ route('claims.index') }}"
                                class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-undo me-1"></i> Reset Filters
                            </a>
                        </div>

                        <!-- Quick Filter Badges -->
                        @if (request()->hasAny(['search', 'insurance_type', 'claim_status']))
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-1 mt-2 mb-1">
                                    <small class="text-muted me-2">Active filters:</small>
                                    @if (request('search'))
                                        <span class="badge bg-primary">
                                            <i class="fas fa-search me-1"></i>Search: {{ request('search') }}
                                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if (request('insurance_type'))
                                        <span class="badge bg-info">
                                            <i class="fas fa-filter me-1"></i>Type: {{ request('insurance_type') }}
                                            <a href="{{ request()->fullUrlWithQuery(['insurance_type' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if (request('claim_status'))
                                        <span class="badge bg-success">
                                            <i class="fas fa-flag me-1"></i>Status: {{ request('claim_status') }}
                                            <a href="{{ request()->fullUrlWithQuery(['claim_status' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
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
                    <table class="table table-hover table-bordered data-table" id="claimsDataTable" data-datatable="true"
                        data-page-length="25" data-server-side="false" data-responsive="true" data-order='[[5, "desc"]]'
                        data-column-defs='[
                               { "targets": [7], "orderable": false },
                               { "targets": [6], "type": "num-fmt" },
                               { "targets": [5], "type": "date-uk" }
                           ]'>
                        <thead class="table-dark">
                            <tr>
                                <th width="15%">Claim Number</th>
                                <th width="15%">Customer</th>
                                <th width="10%">Insurance Type</th>
                                <th width="15%">Policy/Vehicle No</th>
                                <th width="15%">Claim Status</th>
                                <th width="10%">Claim Date</th>
                                <th width="10%">Amount</th>
                                <th width="10%" class="no-sort">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($claims as $claim)
                                <tr data-claim-id="{{ $claim->id }}">
                                    <td data-sort="{{ $claim->insurance_claim_number ?? 'zzz' }}">
                                        @if ($claim->insurance_claim_number)
                                            <strong class="text-primary">{{ $claim->insurance_claim_number }}</strong>
                                        @else
                                            <span class="text-muted fst-italic">Not assigned</span>
                                        @endif
                                    </td>
                                    <td data-sort="{{ $claim->customer->name }}">
                                        <div class="customer-info">
                                            <strong class="d-block">{{ $claim->customer->name }}</strong>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-phone-alt me-1"></i>{{ $claim->customer->mobile_number }}
                                            </small>
                                        </div>
                                    </td>
                                    <td data-sort="{{ $claim->insurance_type }}">
                                        <x-buttons.status-badge :status="$claim->insurance_type" :status-colors="[
                                            'Health' => 'success',
                                            'Truck' => 'info',
                                        ]" />
                                    </td>
                                    <td data-sort="{{ $claim->policy_no ?? $claim->vehicle_number }}">
                                        <div class="policy-vehicle-info">
                                            @if ($claim->policy_no)
                                                <strong class="d-block text-dark">{{ $claim->policy_no }}</strong>
                                            @endif
                                            @if ($claim->vehicle_number)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-car me-1"></i>{{ $claim->vehicle_number }}
                                                </small>
                                            @endif
                                            @if (!$claim->policy_no && !$claim->vehicle_number)
                                                <span class="text-muted fst-italic">Not specified</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-sort="{{ $claim->claim_status }}">
                                        <x-buttons.status-badge :status="$claim->claim_status" :status-colors="[
                                            'Initiated' => 'warning',
                                            'Documents Collected' => 'info',
                                            'Submitted to Insurance' => 'primary',
                                            'Under Review' => 'secondary',
                                            'Approved' => 'success',
                                            'Rejected' => 'danger',
                                            'Closed' => 'dark',
                                        ]" />
                                    </td>
                                    <td data-sort="{{ $claim->incident_date?->format('Y-m-d') ?? '0000-00-00' }}">
                                        @if ($claim->incident_date)
                                            <span class="fw-medium">{{ $claim->incident_date->format('d/m/Y') }}</span>
                                            <small
                                                class="text-muted d-block">{{ $claim->incident_date->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Not specified</span>
                                        @endif
                                    </td>
                                    <td data-sort="{{ $claim->claim_amount ?? 0 }}">
                                        @if ($claim->claim_amount)
                                            <span
                                                class="fw-bold text-success">₹{{ number_format($claim->claim_amount, 0) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <x-tables.action-column>
                                        @if (auth()->user()->hasPermissionTo('claim-edit') && $claim->claim_status != 'Closed')
                                            <x-buttons.whatsapp-button
                                                href="{{ route('claims.intimateDocument', $claim) }}"
                                                title="Send Document List via WhatsApp" size="sm" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('claim-edit'))
                                            <x-buttons.action-button variant="primary" size="sm" icon="fas fa-edit"
                                                href="{{ route('claims.edit', $claim) }}" title="Edit Claim" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('claim-edit') &&
                                                !$claim->insurance_claim_number &&
                                                $claim->claim_status != 'Closed')
                                            <x-buttons.action-button variant="warning" size="sm" icon="fas fa-tag"
                                                onclick="showAssignClaimNumberModal({{ $claim->id }})"
                                                title="Assign Claim Number" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('claim-edit') && $claim->insurance_claim_number && $claim->claim_status != 'Closed')
                                            <x-buttons.whatsapp-button onclick="resendClaimNumber({{ $claim->id }})"
                                                title="Resend WhatsApp" size="sm" />
                                        @endif

                                        <x-buttons.action-button variant="info" size="sm" icon="fas fa-eye"
                                            href="{{ route('claims.show', $claim) }}" title="View Details" />

                                        @if (auth()->user()->hasPermissionTo('claim-edit') && $claim->claim_status != 'Closed')
                                            <x-buttons.action-button variant="secondary" size="sm"
                                                icon="fas fa-times-circle" onclick="closeClaimModal({{ $claim->id }})"
                                                title="Close Claim" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('claim-delete'))
                                            <x-buttons.action-button variant="danger" size="sm"
                                                icon="fas fa-trash-alt"
                                                onclick="delete_conf_common('{{ $claim->id }}','Claim','{{ $claim->insurance_claim_number ?: 'ID: ' . $claim->id }}', '{{ route('claims.index') }}')"
                                                title="Delete Claim" />
                                        @endif
                                    </x-tables.action-column>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No claims found. Create your first claim to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Showing {{ $claims->firstItem() ?? 0 }} to {{ $claims->lastItem() ?? 0 }}
                                of {{ $claims->total() }} claims
                            </small>
                        </div>
                        <div>
                            {{ $claims->appends(request()->all())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Close Claim Modal -->
        <x-modals.form-modal id="closeClaimModal" title="Close Claim" size="lg" :show-footer="true">

            <x-slot name="title">
                <i class="fas fa-times-circle text-warning"></i> Close Claim
            </x-slot>

            <x-slot name="body">
                <!-- Customer Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">Customer Name</label>
                            <p id="close-customer-name" class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">Mobile Number</label>
                            <p id="close-customer-mobile"
                                class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">Claim Reference</label>
                            <p id="close-claim-reference"
                                class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">Vehicle Number</label>
                            <p id="close-vehicle-number" class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Closure Reason -->
                <div class="form-group">
                    <label for="closure_reason" class="form-label font-weight-bold">Closure Reason</label>
                    <textarea name="closure_reason" id="closure_reason" class="form-control" rows="3"
                        placeholder="Please provide the reason for closing this claim..." oninput="updateClaimClosurePreview()"></textarea>
                    <small class="form-text text-muted">This reason will be included in the WhatsApp message to the
                        customer.</small>
                </div>

                <!-- WhatsApp Message Preview -->
                <div class="form-group">
                    <label class="form-label font-weight-bold">
                        <i class="fab fa-whatsapp text-success"></i> WhatsApp Message Preview
                    </label>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-line; min-height: 120px;">
                        <div id="whatsapp-preview-close">
                            <em class="text-muted">Enter closure reason to see message preview</em>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="closeClaimBtn" class="btn btn-danger" onclick="submitCloseClaim()">
                    <i class="fab fa-whatsapp"></i> Close Claim & Send WhatsApp
                </button>
            </x-slot>
        </x-modals.form-modal>

        <!-- Assign Claim Number Modal -->
        <x-modals.form-modal id="assignClaimNumberModal" title="Assign Claim Number & Send WhatsApp" size="lg"
            :show-footer="true">

            <x-slot name="title">
                <i class="fas fa-tag"></i> Assign Claim Number & Send WhatsApp
            </x-slot>

            <x-slot name="body">
                <!-- Customer Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong> <span id="modal-customer-name"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Mobile:</strong> <span id="modal-customer-mobile"></span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Insurance Type:</strong> <span id="modal-insurance-type"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Vehicle/Policy:</strong> <span id="modal-vehicle-policy"></span>
                    </div>
                </div>

                <hr>

                <!-- Claim Number Input -->
                <div class="form-group">
                    <label for="insurance_claim_number">Insurance Claim Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="insurance_claim_number" name="insurance_claim_number"
                        required placeholder="Enter claim number from insurance company"
                        oninput="updateClaimNumberPreview()">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Enter the claim number provided by the insurance company
                    </small>
                </div>

                <!-- WhatsApp Message Preview -->
                <div class="form-group">
                    <label>WhatsApp Message Preview:</label>
                    <div class="border p-3 bg-light rounded">
                        <small class="text-muted">Message that will be sent to customer:</small>
                        <div id="whatsapp-preview" class="mt-2" style="white-space: pre-line; font-family: monospace;">
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>What will happen:</strong>
                    <ol class="mb-0 mt-2">
                        <li>The claim number will be saved to the system</li>
                        <li>WhatsApp message will be sent automatically to the customer</li>
                        <li>You'll see confirmation of the action</li>
                    </ol>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="assignClaimBtn" class="btn btn-warning" onclick="submitAssignClaim()">
                    <i class="fas fa-tag"></i> <i class="fab fa-whatsapp"></i> Assign & Send WhatsApp
                </button>
            </x-slot>
        </x-modals.form-modal>

    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/js/modules/claims-common.js') }}"></script>
    <script>
        // Initialize DataTable with advanced features when document is ready
        $(document).ready(function() {
            // Check if DataTableManager is available
            if (window.CoreManager && CoreManager.has('datatables')) {
                const dataTableManager = CoreManager.get('datatables');

                // Initialize the claims table with advanced features
                dataTableManager.initializeTable('claimsDataTable', {
                    pageLength: 25,
                    responsive: true,
                    stateSave: true,
                    order: [
                        [5, 'desc']
                    ], // Sort by date column (newest first)
                    columnDefs: [{
                            targets: [7],
                            orderable: false
                        }, // Actions column not sortable
                        {
                            targets: [6],
                            type: 'num-fmt'
                        }, // Amount column numeric formatting
                        {
                            targets: [5],
                            type: 'date-uk'
                        } // Date column UK format
                    ],
                    language: {
                        search: "Search claims:",
                        lengthMenu: "Show _MENU_ claims per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ claims",
                        infoFiltered: "(filtered from _MAX_ total claims)",
                        emptyTable: "No claims found. Create your first claim to get started."
                    }
                });

                console.log('✅ Claims DataTable initialized with advanced features');
            } else {
                console.warn('⚠️ DataTableManager not available, falling back to basic table');
            }
        });
    </script>
    <script>
        // Override shared functions with page-specific data extraction
        let currentClaim = null;

        function closeClaimModal(claimId) {
            // Find the claim row
            const claimRow = $(`tr:has(a[onclick*="closeClaimModal(${claimId})"])`);

            // Extract claim data from the row
            const customerName = claimRow.find('td:nth-child(2) strong').text();
            const customerMobile = claimRow.find('td:nth-child(2) small').text().replace(/Mobile:\s*/, '');
            const claimReference = claimRow.find('td:nth-child(3)').text().trim() || `ID: ${claimId}`;
            const vehicleNumber = claimRow.find('td:nth-child(4)').text().trim() || 'N/A';

            // Store current claim data globally
            currentClaim = {
                id: claimId,
                customerName: customerName,
                customerMobile: customerMobile,
                claimReference: claimReference,
                vehicleNumber: vehicleNumber
            };

            // Populate modal fields
            document.getElementById('close-customer-name').textContent = customerName;
            document.getElementById('close-customer-mobile').textContent = customerMobile;
            document.getElementById('close-claim-reference').textContent = claimReference;
            document.getElementById('close-vehicle-number').textContent = vehicleNumber;

            // Clear form and preview
            document.getElementById('closure_reason').value = '';
            document.getElementById('whatsapp-preview-close').innerHTML =
                '<em class="text-muted">Enter closure reason to see message preview</em>';

            // Show modal with backdrop protection
            showModal('closeClaimModal', {
                closeOnBackdrop: false,
                closeOnEscape: false
            });
        }

        function updateClaimClosurePreview() {
            const closureReason = document.getElementById('closure_reason').value.trim();
            const previewDiv = document.getElementById('whatsapp-preview-close');

            if (!closureReason || !currentClaim) {
                previewDiv.innerHTML = '<em class="text-muted">Enter closure reason to see message preview</em>';
                return;
            }

            // Generate preview message using the same format as the backend
            const claimReference = currentClaim.claimReference;
            const vehicleText = currentClaim.vehicleNumber && currentClaim.vehicleNumber !== 'N/A' ?
                ` for vehicle number *${currentClaim.vehicleNumber}*` :
                '';

            const advisorName = '{{ \App\Services\AppSettingService::get('insurance_advisor_name', 'Parth Rawal') }}';
            const website = '{{ \App\Services\AppSettingService::get('business_website', 'https://parthrawal.in') }}';
            const tagline =
                '{{ \App\Services\AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.') }}';
            const contactPhone = '{{ \App\Services\AppSettingService::get('contact_phone', '+919727793123') }}';

            const message = `Dear *${currentClaim.customerName}*,

Your Claim *${claimReference}*${vehicleText} has been closed.

*Closure Reason:* ${closureReason}

If you have any questions regarding this claim closure, please feel free to contact us.

Best regards,
${advisorName}
${website}
Your Trusted Insurance Advisor
"${tagline}"
${contactPhone}`;

            previewDiv.textContent = message;
        }

        function submitCloseClaim() {
            const closureReason = document.getElementById('closure_reason').value.trim();

            if (!closureReason) {
                show_notification('error', 'Please provide a closure reason');
                return;
            }

            if (!currentClaim) {
                show_notification('error', 'Claim data not found. Please close the modal and try again.');
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('closeClaimBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Submit via AJAX
            $.ajax({
                url: `{{ route('claims.closeClaim', '') }}/${currentClaim.id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    closure_reason: closureReason,
                    send_whatsapp: true
                },
                success: function(response) {
                    if (response.success) {
                        show_notification('success', response.message);
                        hideModal('closeClaimModal');

                        // Reload page after short delay to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        show_notification('error', response.message || 'Failed to close claim');
                    }
                },
                error: function(xhr) {
                    console.error('Close claim error:', xhr);
                    const errorMessage = xhr.responseJSON?.message ||
                        'An error occurred while closing the claim';
                    show_notification('error', errorMessage);
                },
                complete: function() {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        function showAssignClaimNumberModal(claimId) {
            // Find the claim row
            const claimRow = $(`tr:has(a[onclick*="showAssignClaimNumberModal(${claimId})"])`);

            // Extract claim data from the row
            const customerName = claimRow.find('td:nth-child(2) strong').text();
            const customerMobile = claimRow.find('td:nth-child(2) small').text();
            const insuranceType = claimRow.find('td:nth-child(3) .badge').text();
            const vehiclePolicy = claimRow.find('td:nth-child(4) strong').text() ||
                claimRow.find('td:nth-child(4) small').text() || 'N/A';

            // Store current claim data
            currentClaim = {
                id: claimId,
                customerName: customerName,
                customerMobile: customerMobile,
                insuranceType: insuranceType,
                vehiclePolicy: vehiclePolicy,
                vehicleNumber: claimRow.find('td:nth-child(4) small').text() || null
            };

            // Populate modal
            $('#modal-customer-name').text(currentClaim.customerName);
            $('#modal-customer-mobile').text(currentClaim.customerMobile);
            $('#modal-insurance-type').text(currentClaim.insuranceType);
            $('#modal-vehicle-policy').text(currentClaim.vehiclePolicy);

            // Store current claim ID for AJAX submission
            window.currentClaimId = claimId;

            // Clear form
            document.getElementById('insurance_claim_number').value = '';
            document.getElementById('whatsapp-preview').innerHTML = '';

            // Show modal with backdrop protection
            showModal('assignClaimNumberModal', {
                closeOnBackdrop: false,
                closeOnEscape: false
            });
        }

        function updateClaimNumberPreview() {
            const claimNumber = document.getElementById('insurance_claim_number').value.trim();
            const previewDiv = document.getElementById('whatsapp-preview');

            if (claimNumber && currentClaim) {
                let message = `Dear *${currentClaim.customerName}*,\n\n`;
                message += `Your Claim Number *${claimNumber}* is generated`;

                if (currentClaim.vehicleNumber) {
                    message += ` against your vehicle number *${currentClaim.vehicleNumber}*`;
                }

                message += `. For further assistance kindly contact me.\n\n`;
                message += `Best regards,\n`;
                message += `{{ \App\Services\AppSettingService::get('insurance_advisor_name', 'Parth Rawal') }}\n`;
                message += `{{ \App\Services\AppSettingService::get('contact_phone', '+919727793123') }}`;

                previewDiv.innerHTML = message;
            } else {
                previewDiv.innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
            }
        }

        function resendClaimNumber(claimId) {
            showConfirmationModal({
                title: 'Resend WhatsApp Message',
                message: 'Are you sure you want to resend the claim number via WhatsApp?',
                confirmText: 'Yes, Resend',
                confirmClass: 'btn-success',
                onConfirm: function() {
                    window.location.href = `{{ url('/claims') }}/${claimId}/resend-claim-number`;
                }
            });
        }

        // submitAssignClaim function now in claims-common.js
    </script>
@endsection
