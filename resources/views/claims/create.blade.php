@extends('layouts.app')

@section('title', 'Create New Claim')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Claim Form -->
        <div class="card shadow mb-1">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div>
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Create New Claim</h1>
                        <small class="text-muted">Register a new insurance claim</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <x-buttons.action-button 
                            variant="outline-secondary" 
                            size="sm" 
                            icon="fas fa-arrow-left"
                            href="{{ route('claims.index') }}"
                            title="Back to Claims List">
                            Back to Claims
                        </x-buttons.action-button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Enhanced Error Display -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            <h6 class="mb-0">Please fix the following errors:</h6>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('claims.store') }}" id="claimForm">
                    @csrf

                    <!-- Enhanced Form Layout using Reusable Components -->
                    <div class="row g-3 mb-4">
                        <!-- Insurance Type -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label for="insurance_type" class="form-label text-sm fw-bold">
                                <span class="text-danger">*</span> Insurance Type
                            </label>
                            <select name="insurance_type" id="insurance_type" required
                                    class="form-select form-select-sm select2-enable"
                                    data-placeholder="Select Insurance Type"
                                    onchange="updateSearchPlaceholder(this.value)">
                                <option value="">Select Insurance Type</option>
                                <option value="Health" {{ old('insurance_type') == 'Health' ? 'selected' : '' }}>
                                    🏥 Health Insurance
                                </option>
                                <option value="Truck" {{ old('insurance_type') == 'Truck' ? 'selected' : '' }}>
                                    🚛 Truck Insurance
                                </option>
                            </select>
                            @error('insurance_type')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Universal Search Field -->
                        <div class="col-lg-6 col-md-8 col-sm-6">
                            <div class="position-relative">
                                <label for="universal_search_input" id="search_label" class="form-label text-sm fw-bold">
                                    Search by Policy Number, Customer Name, or Mobile Number
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="universal_search_input"
                                           class="form-control"
                                           placeholder="Enter policy number, customer name, or mobile..."
                                           autocomplete="off">
                                    <button type="button" id="clearSearch" class="btn btn-outline-secondary" 
                                            style="display: none;" onclick="clearUniversalSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="universal_search_suggestions" 
                                     class="position-absolute w-100 bg-white border rounded shadow-sm" 
                                     style="top: 100%; z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                            </div>
                        </div>

                        <!-- Incident Date -->
                        <div class="col-lg-3 col-md-12">
                            <label for="incident_date" class="form-label text-sm fw-bold">Incident Date</label>
                            <input type="date" name="incident_date" id="incident_date"
                                   class="form-control form-control-sm"
                                   value="{{ old('incident_date', date('Y-m-d')) }}">
                            @error('incident_date')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Hidden fields for form submission -->
                    <input type="hidden" name="policy_no" id="policy_no" value="{{ old('policy_no') }}">
                    <input type="hidden" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number') }}">

                    <!-- Auto-populated Policy Details (Hidden fields for form submission) -->
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                    <input type="hidden" name="customer_insurance_id" id="customer_insurance_id" value="{{ old('customer_insurance_id') }}">
                    
                    <!-- Policy Details Display Section -->
                    <div class="row mt-3" id="policy_details_section" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-info">
                                <div class="card-header bg-info text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-info-circle"></i> Policy Details <small>(Auto-populated)</small></h6>
                                </div>
                                <div class="card-body bg-light" style="font-size: 13px;">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <div class="row">
                                                <div class="col-4"><strong>Customer:</strong></div>
                                                <div class="col-8" id="display_customer_name">-</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-4"><strong>Mobile:</strong></div>
                                                <div class="col-8" id="display_customer_mobile">-</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-4"><strong>Insurance:</strong></div>
                                                <div class="col-8" id="display_insurance_company">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <div class="row">
                                                <div class="col-4"><strong>Sum Insured:</strong></div>
                                                <div class="col-8" id="display_sum_insured">-</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-4"><strong>Policy Period:</strong></div>
                                                <div class="col-8" id="display_policy_period">-</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-4"><strong>Vehicle:</strong></div>
                                                <div class="col-8" id="display_vehicle_info">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Claim Fields -->
                    <div class="row mt-3">
                        <div class="col-md-4 col-sm-6 mb-1">
                            <div class="form-group">
                                <label for="claim_amount">Claim Amount (₹)</label>
                                <input type="number" name="claim_amount" id="claim_amount"
                                    class="form-control form-control-sm @error('claim_amount') is-invalid @enderror"
                                    value="{{ old('claim_amount') }}" min="0" step="1"
                                    placeholder="0">
                                @error('claim_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <div class="form-group">
                                <label for="intimation_date">Intimation Date</label>
                                <input type="date" name="intimation_date" id="intimation_date"
                                    class="form-control form-control-sm @error('intimation_date') is-invalid @enderror"
                                    value="{{ old('intimation_date', date('Y-m-d')) }}">
                                @error('intimation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <div class="form-group">
                                <label for="claim_status">Claim Status</label>
                                <select name="claim_status" id="claim_status"
                                    class="form-control form-control-sm @error('claim_status') is-invalid @enderror">
                                    <option value="Initiated" {{ old('claim_status', 'Initiated') == 'Initiated' ? 'selected' : '' }}>Initiated</option>
                                </select>
                                @error('claim_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Claim Number Field -->
                    <div class="row mt-3">
                        <div class="col-md-6 col-sm-12 mb-1">
                            <div class="form-group">
                                <label for="insurance_claim_number">Insurance Claim Number</label>
                                <input type="text" name="insurance_claim_number" id="insurance_claim_number"
                                    class="form-control form-control-sm @error('insurance_claim_number') is-invalid @enderror"
                                    value="{{ old('insurance_claim_number') }}"
                                    placeholder="Claim number from insurance company (optional)">
                                @error('insurance_claim_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> You can add this later if not available now
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Type Specific Fields -->
                    <!-- Health Insurance Specific Fields -->
                    <div class="row" id="health_fields" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-success mb-1">
                                <div class="card-header bg-success text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-heartbeat"></i> Health Insurance Details</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="patient_name">Patient Name</label>
                                                <input type="text" name="patient_name" id="patient_name"
                                                    class="form-control form-control-sm @error('patient_name') is-invalid @enderror"
                                                    value="{{ old('patient_name') }}">
                                                @error('patient_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="hospital_name">Hospital Name</label>
                                                <input type="text" name="hospital_name" id="hospital_name"
                                                    class="form-control form-control-sm @error('hospital_name') is-invalid @enderror"
                                                    value="{{ old('hospital_name') }}">
                                                @error('hospital_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="admission_date">Admission Date</label>
                                                <input type="date" name="admission_date" id="admission_date"
                                                    class="form-control form-control-sm @error('admission_date') is-invalid @enderror"
                                                    value="{{ old('admission_date') }}">
                                                @error('admission_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Truck Insurance Specific Fields -->
                    <div class="row" id="truck_fields" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-danger mb-1">
                                <div class="card-header bg-danger text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-truck"></i> Truck Insurance Details</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="driver_name">Driver Name</label>
                                                <input type="text" name="driver_name" id="driver_name"
                                                    class="form-control form-control-sm @error('driver_name') is-invalid @enderror"
                                                    value="{{ old('driver_name') }}">
                                                @error('driver_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="accident_location">Accident Location</label>
                                                <input type="text" name="accident_location" id="accident_location"
                                                    class="form-control form-control-sm @error('accident_location') is-invalid @enderror"
                                                    value="{{ old('accident_location') }}">
                                                @error('accident_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="driver_contact_number">Driver Contact</label>
                                                <input type="text" name="driver_contact_number" id="driver_contact_number"
                                                    class="form-control form-control-sm @error('driver_contact_number') is-invalid @enderror"
                                                    value="{{ old('driver_contact_number') }}">
                                                @error('driver_contact_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer py-3">
                <div class="d-flex flex-column flex-sm-row justify-content-end align-items-center gap-2">
                    <x-buttons.action-button 
                        variant="outline-secondary" 
                        size="sm" 
                        icon="fas fa-times"
                        href="{{ route('claims.index') }}"
                        title="Cancel and return to claims list">
                        Cancel
                    </x-buttons.action-button>
                    
                    <button type="submit" form="claimForm" 
                            class="btn btn-primary btn-sm d-flex align-items-center" 
                            id="submitBtn">
                        <i class="fas fa-save me-1"></i>
                        <span>Create Claim</span>
                        <div class="spinner-border spinner-border-sm ms-2 d-none" id="submitSpinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Ensure Insurance Type field visibility */
        #insurance_type {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Responsive layout for smaller screens */
        @media (max-width: 768px) {
            div[style*="display: flex"] {
                flex-direction: column !important;
                gap: 10px !important;
            }
            div[style*="flex: 0 0"] {
                flex: none !important;
                min-width: auto !important;
            }
        }
        
        /* Make sure the search field is properly styled */
        #universal_search_field {
            min-height: 70px;
        }
        
        #universal_search_suggestions {
            background: white;
            border: 1px solid #ddd;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
    <script>
        $(document).ready(function() {
            
            // Force Insurance Type field visibility immediately on page load
            $('#insurance_type').show().css({
                'display': 'block',
                'visibility': 'visible',
                'opacity': '1',
                'position': 'relative',
                'z-index': '10'
            });
            
            // Debugging - Check if elements exist and their visibility
            console.log('Insurance Type field found:', $('#insurance_type').length);
            console.log('Insurance Type field visible:', $('#insurance_type').is(':visible'));
            console.log('Insurance Type field CSS display:', $('#insurance_type').css('display'));
            console.log('Insurance Type field CSS visibility:', $('#insurance_type').css('visibility'));
            console.log('Insurance Type field CSS opacity:', $('#insurance_type').css('opacity'));
            console.log('Universal search field found:', $('#universal_search_field').length);
            console.log('Universal search field visible:', $('#universal_search_field').is(':visible'));
            
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Insurance Type Change Handler - Update Search Field Label
            $('#insurance_type').on('change', function() {
                const insuranceType = $(this).val();
                
                // Clear previous search and hide suggestions
                $('#universal_search_suggestions').hide().empty();
                $('#universal_search_input').val('');
                hidePolicyDetails();
                
                // Update label and placeholder based on insurance type selection
                if (insuranceType === 'Health') {
                    $('#search_label').text('Search by Policy Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter policy number, customer name, or mobile...');
                } else if (insuranceType === 'Truck') {
                    $('#search_label').text('Search by Registration Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter registration number, customer name, or mobile...');
                } else {
                    // When no insurance type is selected, show generic label
                    $('#search_label').text('Search by Policy Number, Registration Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter search term...');
                }
            });

            // Universal Search - One API for ALL fields
            let universalSearchTimeout;
            $('#universal_search_input').on('input', function() {
                let query = $(this).val().trim();
                const insuranceType = $('#insurance_type').val();
                
                // Auto-uppercase for truck insurance searches
                if (insuranceType === 'Truck' && /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/i.test(query)) {
                    query = query.toUpperCase();
                    $(this).val(query);
                }
                
                clearTimeout(universalSearchTimeout);
                
                if (query.length >= 3) {
                    universalSearchTimeout = setTimeout(() => {
                        performUniversalSearch(query);
                    }, 300);
                } else {
                    $('#universal_search_suggestions').hide().empty();
                    hidePolicyDetails();
                }
            });

            // Universal Search Function - Single API Call
            function performUniversalSearch(query) {
                $.get('{{ route("claims.search") }}?q=' + encodeURIComponent(query))
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            showUniversalSearchSuggestions(response.data);
                        } else {
                            $('#universal_search_suggestions').hide().empty();
                        }
                    })
                    .fail(function() {
                        $('#universal_search_suggestions').hide().empty();
                    });
            }

            // Show Universal Search Suggestions
            function showUniversalSearchSuggestions(results) {
                let html = '';
                
                results.forEach(result => {
                    // Show all results regardless of insurance type selection

                    html += `<div class="p-2 universal-suggestion" 
                                data-policy="${result.policy_number}" 
                                data-vehicle="${result.vehicle_number || ''}"
                                data-customer-id="${result.customer_id}"
                                data-policy-id="${result.policy_id}"
                                data-insurance-type="${result.insurance_type}"
                                style="cursor: pointer; border-bottom: 1px solid #eee;">`;
                    
                    // Show what field matched
                    if (result.matched_field === 'policy') {
                        html += `<strong class="text-primary">Policy: ${result.policy_number}</strong><br>`;
                    } else if (result.matched_field === 'vehicle' && result.vehicle_number) {
                        html += `<strong class="text-success">Vehicle: ${result.vehicle_number}</strong><br>`;
                    } else if (result.matched_field === 'customer_name') {
                        html += `<strong class="text-info">Customer: ${result.customer_name}</strong><br>`;
                    } else if (result.matched_field === 'mobile') {
                        html += `<strong class="text-warning">Mobile: ${result.customer_mobile}</strong><br>`;
                    }
                    
                    // Additional info
                    if (result.matched_field !== 'policy' && result.policy_number) {
                        html += `<small>Policy: ${result.policy_number}</small><br>`;
                    }
                    if (result.matched_field !== 'vehicle' && result.vehicle_number) {
                        html += `<small>Vehicle: ${result.vehicle_number}</small><br>`;
                    }
                    if (result.matched_field !== 'customer_name') {
                        html += `<small class="text-muted">${result.customer_name} - ${result.customer_mobile}</small><br>`;
                    }
                    
                    html += `<small class="text-info">${result.insurance_company} (${result.insurance_type})</small>`;
                    html += `</div>`;
                });
                
                $('#universal_search_suggestions').html(html).show();
            }

            // Handle Universal Search Selection
            $(document).on('click', '.universal-suggestion', function() {
                const policyNumber = $(this).data('policy');
                const vehicleNumber = $(this).data('vehicle') || '';
                const customerId = $(this).data('customer-id');
                const policyId = $(this).data('policy-id');
                const insuranceType = $(this).data('insurance-type');
                
                // Hide suggestions
                $('#universal_search_suggestions').hide().empty();
                
                // Set form values
                $('#universal_search_input').val(policyNumber || vehicleNumber);
                $('#policy_no').val(policyNumber);
                $('#vehicle_number').val(vehicleNumber);
                $('#customer_id').val(customerId);
                $('#customer_insurance_id').val(policyId);
                
                // Insurance Type is independent - user must select manually
                
                // Lookup full details
                lookupPolicyDetails('policy', policyNumber);
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#universal_search_input, #universal_search_suggestions').length) {
                    $('#universal_search_suggestions').hide().empty();
                }
            });

            // Lookup policy details and populate fields
            function lookupPolicyDetails(type, value) {
                if (!value) return;

                $.get('{{ route("claims.lookup", ":type") }}'.replace(':type', type) + '?value=' + encodeURIComponent(value))
                    .done(function(response) {
                        if (response.success) {
                            populateFields(response.data);
                        } else {
                            hidePolicyDetails();
                        }
                    })
                    .fail(function() {
                        hidePolicyDetails();
                    });
            }

            // Populate all fields based on policy data
            function populateFields(data) {
                // Insurance Type remains independent - user must select manually

                // Populate hidden fields
                $('#customer_id').val(data.customer_id || '');
                $('#customer_insurance_id').val(data.policy_id || '');

                // Cross-populate fields
                if (!$('#policy_no').val() && data.policy_number) {
                    $('#policy_no').val(data.policy_number);
                }
                if (!$('#vehicle_number').val() && data.vehicle_number) {
                    $('#vehicle_number').val(data.vehicle_number.toUpperCase());
                }

                // Show policy details section
                showPolicyDetails(data);

                // Show/hide insurance type specific fields based on user's selection
                const userSelectedInsuranceType = $('#insurance_type').val();
                toggleInsuranceFields(userSelectedInsuranceType);
            }

            // Show policy details in display section
            function showPolicyDetails(data) {
                $('#display_customer_name').text(data.customer_name || '-');
                $('#display_customer_mobile').text(data.customer_mobile || '-');
                $('#display_insurance_company').text(data.insurance_company || '-');
                $('#display_sum_insured').text('₹' + (data.sum_insured || 0));
                
                let policyPeriod = '';
                if (data.policy_start_date && data.policy_end_date) {
                    policyPeriod = formatDate(data.policy_start_date) + ' to ' + formatDate(data.policy_end_date);
                }
                $('#display_policy_period').text(policyPeriod || '-');
                
                let vehicleInfo = '';
                if (data.vehicle_make || data.vehicle_model || data.vehicle_year) {
                    vehicleInfo = [data.vehicle_make, data.vehicle_model, data.vehicle_year ? '(' + data.vehicle_year + ')' : ''].filter(Boolean).join(' ');
                }
                $('#display_vehicle_info').text(vehicleInfo || '-');

                $('#policy_details_section').show();
            }

            function hidePolicyDetails() {
                $('#policy_details_section').hide();
                // Insurance Type remains unchanged - user-controlled
                $('#customer_id').val('');
                $('#customer_insurance_id').val('');
                // Keep current insurance type selection for field toggles
                const currentInsuranceType = $('#insurance_type').val();
                toggleInsuranceFields(currentInsuranceType);
            }

            // Toggle insurance type specific fields
            function toggleInsuranceFields(type) {
                $('#health_fields, #truck_fields').hide();
                $('.health-required, .truck-required').text('');
                $('#health_fields input, #truck_fields input').prop('required', false);

                if (type === 'Health') {
                    $('#health_fields').show();
                    // No required fields - all optional as per user request
                } else if (type === 'Truck') {
                    $('#truck_fields').show();
                    // No required fields - all optional as per user request
                }
            }

            // Manual insurance type change
            $('#insurance_type').on('change', function() {
                toggleInsuranceFields($(this).val());
            });

            // Helper function to format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB');
            }

            // Form submission
            $('#claimForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            });

        });
    </script>
@endsection