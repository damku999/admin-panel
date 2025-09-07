@extends('layouts.app')

@section('title', 'Create New Claim')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus"></i> Create New Claim
            </h1>
            <a href="{{ route('claims.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Claims
            </a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Claim Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus"></i> New Claim Details
                </h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('claims.store') }}" id="claimForm">
                    @csrf

                    <!-- Primary Fields in Your Requested Order -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group position-relative">
                                <label for="policy_no"><span class="text-danger">*</span> Policy Number</label>
                                <input type="text" name="policy_no" id="policy_no"
                                    class="form-control @error('policy_no') is-invalid @enderror"
                                    value="{{ old('policy_no') }}"
                                    placeholder="Enter policy number" required
                                    autocomplete="off">
                                <div id="policy_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></div>
                                @error('policy_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group position-relative">
                                <label for="vehicle_number">Registration No.</label>
                                <input type="text" name="vehicle_number" id="vehicle_number"
                                    class="form-control @error('vehicle_number') is-invalid @enderror"
                                    value="{{ old('vehicle_number') }}" style="text-transform: uppercase"
                                    placeholder="e.g., GJ01AB1234"
                                    autocomplete="off">
                                <div id="vehicle_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></div>
                                @error('vehicle_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="incident_date"><span class="text-danger">*</span> Incident Date</label>
                                <input type="date" name="incident_date" id="incident_date"
                                    class="form-control @error('incident_date') is-invalid @enderror"
                                    value="{{ old('incident_date', date('Y-m-d')) }}" required>
                                @error('incident_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="insurance_type">Insurance Type <small class="text-muted">(Auto-detected)</small></label>
                                <select name="insurance_type" id="insurance_type"
                                    class="form-control @error('insurance_type') is-invalid @enderror" required>
                                    <option value="">Auto-detecting...</option>
                                    <option value="Health" {{ old('insurance_type') == 'Health' ? 'selected' : '' }}>Health Insurance</option>
                                    <option value="Truck" {{ old('insurance_type') == 'Truck' ? 'selected' : '' }}>Truck Insurance</option>
                                </select>
                                @error('insurance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Auto-populated Policy Details (Hidden fields for form submission) -->
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                    <input type="hidden" name="customer_insurance_id" id="customer_insurance_id" value="{{ old('customer_insurance_id') }}">
                    
                    <!-- Policy Details Display Section -->
                    <div class="row mt-3" id="policy_details_section" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-info">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-info-circle"></i> Policy Details <small>(Auto-populated)</small></h6>
                                </div>
                                <div class="card-body bg-light" style="font-size: 13px;">
                                    <div class="row">
                                        <div class="col-md-6">
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
                                        <div class="col-md-6">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="claim_amount">Claim Amount (₹)</label>
                                <input type="number" name="claim_amount" id="claim_amount"
                                    class="form-control @error('claim_amount') is-invalid @enderror"
                                    value="{{ old('claim_amount') }}" min="0" step="1"
                                    placeholder="0">
                                @error('claim_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="intimation_date">Intimation Date</label>
                                <input type="date" name="intimation_date" id="intimation_date"
                                    class="form-control @error('intimation_date') is-invalid @enderror"
                                    value="{{ old('intimation_date', date('Y-m-d')) }}">
                                @error('intimation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="claim_status">Claim Status</label>
                                <select name="claim_status" id="claim_status"
                                    class="form-control @error('claim_status') is-invalid @enderror">
                                    <option value="Initiated" {{ old('claim_status', 'Initiated') == 'Initiated' ? 'selected' : '' }}>Initiated</option>
                                </select>
                                @error('claim_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Type Specific Fields -->
                    <!-- Health Insurance Specific Fields -->
                    <div class="row" id="health_fields" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-success mb-4">
                                <div class="card-header bg-success text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-heartbeat"></i> Health Insurance Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="patient_name">Patient Name <span class="text-danger health-required">*</span></label>
                                                <input type="text" name="patient_name" id="patient_name"
                                                    class="form-control @error('patient_name') is-invalid @enderror"
                                                    value="{{ old('patient_name') }}">
                                                @error('patient_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="hospital_name">Hospital Name</label>
                                                <input type="text" name="hospital_name" id="hospital_name"
                                                    class="form-control @error('hospital_name') is-invalid @enderror"
                                                    value="{{ old('hospital_name') }}">
                                                @error('hospital_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="admission_date">Admission Date <span class="text-danger health-required">*</span></label>
                                                <input type="date" name="admission_date" id="admission_date"
                                                    class="form-control @error('admission_date') is-invalid @enderror"
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
                            <div class="card border-left-danger mb-4">
                                <div class="card-header bg-danger text-white py-2">
                                    <h6 class="m-0"><i class="fas fa-truck"></i> Truck Insurance Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="driver_name">Driver Name</label>
                                                <input type="text" name="driver_name" id="driver_name"
                                                    class="form-control @error('driver_name') is-invalid @enderror"
                                                    value="{{ old('driver_name') }}">
                                                @error('driver_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="accident_location">Accident Location</label>
                                                <input type="text" name="accident_location" id="accident_location"
                                                    class="form-control @error('accident_location') is-invalid @enderror"
                                                    value="{{ old('accident_location') }}">
                                                @error('accident_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="driver_contact_number">Driver Contact <span class="text-danger truck-required">*</span></label>
                                                <input type="text" name="driver_contact_number" id="driver_contact_number"
                                                    class="form-control @error('driver_contact_number') is-invalid @enderror"
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
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('claims.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Create Claim
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            
            // Auto-complete for Policy Number (after 3 characters)
            let policyTimeout;
            $('#policy_no').on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(policyTimeout);
                
                if (query.length >= 3) {
                    policyTimeout = setTimeout(() => {
                        searchPolicyNumbers(query);
                    }, 300);
                } else {
                    $('#policy_suggestions').hide().empty();
                    hidePolicyDetails();
                }
            });

            // Auto-complete for Registration Number (after 3 characters)
            let vehicleTimeout;
            $('#vehicle_number').on('input', function() {
                const query = $(this).val().trim().toUpperCase();
                $(this).val(query); // Keep uppercase
                clearTimeout(vehicleTimeout);
                
                if (query.length >= 3) {
                    vehicleTimeout = setTimeout(() => {
                        searchVehicleNumbers(query);
                    }, 300);
                } else {
                    $('#vehicle_suggestions').hide().empty();
                    hidePolicyDetails();
                }
            });

            // Search policy numbers for auto-complete
            function searchPolicyNumbers(query) {
                $.get('{{ route("claims.search-policies") }}?q=' + encodeURIComponent(query))
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            showPolicySuggestions(response.data);
                        } else {
                            $('#policy_suggestions').hide().empty();
                        }
                    })
                    .fail(function() {
                        $('#policy_suggestions').hide().empty();
                    });
            }

            // Search vehicle numbers for auto-complete
            function searchVehicleNumbers(query) {
                $.get('{{ route("claims.search-vehicles") }}?q=' + encodeURIComponent(query))
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            showVehicleSuggestions(response.data);
                        } else {
                            $('#vehicle_suggestions').hide().empty();
                        }
                    })
                    .fail(function() {
                        $('#vehicle_suggestions').hide().empty();
                    });
            }

            // Show policy suggestions dropdown
            function showPolicySuggestions(policies) {
                let html = '';
                policies.forEach(policy => {
                    html += `<div class="p-2 policy-suggestion" data-policy="${policy.policy_number}" style="cursor: pointer; border-bottom: 1px solid #eee;">`;
                    html += `<strong>${policy.policy_number}</strong><br>`;
                    html += `<small class="text-muted">${policy.customer_name} - ${policy.insurance_company}</small>`;
                    html += `</div>`;
                });
                $('#policy_suggestions').html(html).show();
            }

            // Show vehicle suggestions dropdown
            function showVehicleSuggestions(vehicles) {
                let html = '';
                vehicles.forEach(vehicle => {
                    html += `<div class="p-2 vehicle-suggestion" data-vehicle="${vehicle.vehicle_number}" style="cursor: pointer; border-bottom: 1px solid #eee;">`;
                    html += `<strong>${vehicle.vehicle_number}</strong><br>`;
                    html += `<small class="text-muted">${vehicle.customer_name} - ${vehicle.insurance_company}</small>`;
                    html += `</div>`;
                });
                $('#vehicle_suggestions').html(html).show();
            }

            // Handle policy suggestion selection
            $(document).on('click', '.policy-suggestion', function() {
                const policyNumber = $(this).data('policy');
                $('#policy_no').val(policyNumber);
                $('#policy_suggestions').hide().empty();
                lookupPolicyDetails('policy', policyNumber);
            });

            // Handle vehicle suggestion selection  
            $(document).on('click', '.vehicle-suggestion', function() {
                const vehicleNumber = $(this).data('vehicle');
                $('#vehicle_number').val(vehicleNumber.toUpperCase());
                $('#vehicle_suggestions').hide().empty();
                lookupPolicyDetails('vehicle', vehicleNumber);
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#policy_no, #policy_suggestions, #vehicle_number, #vehicle_suggestions').length) {
                    $('#policy_suggestions, #vehicle_suggestions').hide().empty();
                }
            });

            // Lookup policy details and populate fields
            function lookupPolicyDetails(type, value) {
                if (!value) return;

                $.get('{{ url("/claims/policy-lookup") }}/' + type + '/' + encodeURIComponent(value))
                    .done(function(response) {
                        if (response.success) {
                            populateFields(response.data);
                        } else {
                            hidePolicyDetails();
                            alert('No policy details found for ' + value);
                        }
                    })
                    .fail(function() {
                        hidePolicyDetails();
                        alert('Error looking up policy details');
                    });
            }

            // Populate all fields based on policy data
            function populateFields(data) {
                // Auto-detect insurance type
                let insuranceType = 'Health'; // default
                if (data.policy_type) {
                    if (data.policy_type.toLowerCase().includes('truck') || 
                        data.policy_type.toLowerCase().includes('motor') ||
                        data.policy_type.toLowerCase().includes('vehicle') ||
                        data.policy_type.toLowerCase().includes('commercial')) {
                        insuranceType = 'Truck';
                    }
                }
                $('#insurance_type').val(insuranceType);

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

                // Show/hide insurance type specific fields
                toggleInsuranceFields(insuranceType);
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
                $('#insurance_type').val('');
                $('#customer_id').val('');
                $('#customer_insurance_id').val('');
                toggleInsuranceFields('');
            }

            // Toggle insurance type specific fields
            function toggleInsuranceFields(type) {
                $('#health_fields, #truck_fields').hide();
                $('.health-required, .truck-required').text('');
                $('#health_fields input, #truck_fields input').prop('required', false);

                if (type === 'Health') {
                    $('#health_fields').show();
                    $('.health-required').text('*');
                    $('#patient_name, #admission_date').prop('required', true);
                } else if (type === 'Truck') {
                    $('#truck_fields').show();
                    $('.truck-required').text('*');
                    $('#driver_contact_number').prop('required', true);
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