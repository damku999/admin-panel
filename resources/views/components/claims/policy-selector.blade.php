{{--
    Policy Selector Component for Claims

    @param string $mode - 'create' or 'edit'
    @param object $claim - Required for edit mode, should contain customerInsurance relationship
    @param string $selectedValue - For old values or default selection
--}}

@props(['mode' => 'create', 'claim' => null, 'selectedValue' => null])

@php
    $isEditMode = $mode === 'edit';
    $hasExistingPolicy = $isEditMode && $claim && $claim->customerInsurance;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold"><span class="text-danger">*</span> Select Policy/Insurance</label>
        <select class="form-control select2-policy @error('customer_insurance_id') is-invalid @enderror"
                name="customer_insurance_id" id="customer_insurance_id" required>

            @if($isEditMode && $hasExistingPolicy)
                {{-- Edit mode: Pre-populate with existing policy --}}
                <option value="{{ $claim->customerInsurance->id }}" selected
                        data-customer-name="{{ $claim->customer->name }}"
                        data-policy-no="{{ $claim->customerInsurance->policy_no }}"
                        data-registration-no="{{ $claim->customerInsurance->registration_no ?? '' }}"
                        data-insurance-company="{{ $claim->customerInsurance->insuranceCompany->name ?? '' }}"
                        data-policy-type="{{ $claim->customerInsurance->policyType->name ?? '' }}"
                        data-customer-email="{{ $claim->customer->email ?? '' }}"
                        data-customer-mobile="{{ $claim->customer->mobile_number ?? '' }}">
                    {{ $claim->customer->name }} - {{ $claim->customerInsurance->policy_no }}{{ $claim->customerInsurance->registration_no ? ' (' . $claim->customerInsurance->registration_no . ')' : '' }}
                </option>
            @elseif($selectedValue)
                {{-- Create mode with old values --}}
                <option value="{{ $selectedValue }}" selected>Loading...</option>
            @endif
        </select>

        @error('customer_insurance_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">Start typing to search policies. You can search by customer name, policy number, registration number, email or mobile number.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold"><span class="text-danger">*</span> Insurance Type</label>
        <select class="form-control @error('insurance_type') is-invalid @enderror"
                name="insurance_type" id="insurance_type" required>
            <option value="">Select Insurance Type</option>
            <option value="Health" {{ old('insurance_type', $isEditMode && $claim ? $claim->insurance_type : '') == 'Health' ? 'selected' : '' }}>Health Insurance</option>
            <option value="Vehicle" {{ old('insurance_type', $isEditMode && $claim ? $claim->insurance_type : '') == 'Vehicle' ? 'selected' : '' }}>Vehicle Insurance</option>
        </select>

        @error('insurance_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Selected Policy Details Display -->
<div id="policy-details" class="mt-3" style="{{ $hasExistingPolicy ? '' : 'display: none;' }}">
    <div class="alert alert-info">
        <h6 class="fw-bold mb-2">{{ $isEditMode ? 'Current' : 'Selected' }} Policy Details:</h6>
        <div id="policy-details-content">
            @if($hasExistingPolicy)
                <div class="row">
                    <div class="col-md-3"><strong>Customer:</strong> {{ $claim->customer->name }}</div>
                    <div class="col-md-3"><strong>Policy No:</strong> {{ $claim->customerInsurance->policy_no ?? 'N/A' }}</div>
                    <div class="col-md-3"><strong>Registration:</strong> {{ $claim->customerInsurance->registration_no ?? 'N/A' }}</div>
                    <div class="col-md-3"><strong>Company:</strong> {{ $claim->customerInsurance->insuranceCompany->name ?? 'N/A' }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

@once
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <style>
            .policy-option {
                padding: 8px 12px;
                border-bottom: 1px solid #eee;
            }

            .policy-option:last-child {
                border-bottom: none;
            }

            .policy-header {
                display: flex;
                justify-content: space-between;
                align-items-center;
                margin-bottom: 5px;
            }

            .policy-header strong {
                color: #2c3e50;
                font-size: 14px;
            }

            .policy-details small {
                color: #6c757d;
                font-size: 12px;
                line-height: 1.4;
            }

            .policy-details i {
                color: #007bff;
                margin-right: 4px;
            }

            .select2-container--bootstrap-5 .select2-dropdown {
                border: 1px solid #dee2e6;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                z-index: 9999 !important;
            }

            .select2-container--bootstrap-5 .select2-results__option {
                padding: 0;
            }

            .select2-container--bootstrap-5 .select2-results__option--highlighted {
                background-color: #f8f9fa;
            }

            .select2-container--bootstrap-5 .select2-selection--single {
                height: calc(2.25rem + 2px);
                border: 1px solid #ced4da;
            }

            /* Ensure dropdown appears above other elements */
            .select2-dropdown {
                z-index: 9999 !important;
            }

            /* Fix for container positioning */
            .select2-container {
                z-index: 1;
            }

            .select2-container--open {
                z-index: 9999;
            }
        </style>
    @endpush
@endonce

@once
    @push('scripts')
        <script>
            // Policy Selector Component JavaScript
            window.initializePolicySelector = function() {
                console.log('Initializing Policy Selector Component...');

                // Check if jQuery is loaded
                if (typeof $ === 'undefined') {
                    console.error('jQuery is not loaded!');
                    return;
                }

                // Check if Select2 is loaded
                if (typeof $.fn.select2 === 'undefined') {
                    console.error('Select2 is not loaded!');
                    return;
                }

                // Setup CSRF token for AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Check if there's an existing selection
                const hasInitialSelection = $('.select2-policy').val();
                const isEditMode = hasInitialSelection && hasInitialSelection !== '';

                console.log('Policy selector found elements:', $('.select2-policy').length);
                console.log('Has initial selection:', hasInitialSelection);
                console.log('Is edit mode:', isEditMode);

                // Initialize Select2 for policy search
                $('.select2-policy').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Type to search policies...',
                    allowClear: true,
                    minimumInputLength: 2,
                    width: '100%',
                    dropdownParent: $('body'),
                    dropdownAutoWidth: true,
                    closeOnSelect: true,
                    tags: false,
                    ajax: {
                        url: '{{ route("claims.searchPolicies") }}',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return { search: params.term };
                        },
                        processResults: function (data) {
                            if (!data || data.error || !data.results) {
                                return { results: [] };
                            }
                            return {
                                results: data.results.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.customer_name + ' - ' + item.policy_no,
                                        data: item
                                    };
                                })
                            };
                        }
                    },
                    templateResult: function(result) {
                        if (!result.id || !result.data) return result.text;

                        var data = result.data;
                        var html = '<div class="policy-option">' +
                            '<div class="policy-header">' +
                                '<strong>' + (data.customer_name || 'Unknown') + '</strong>' +
                                '<span class="badge bg-primary" style="font-size: 10px;">' + (data.policy_type || 'Policy') + '</span>' +
                            '</div>' +
                            '<div class="policy-details">' +
                                '<small><i class="fas fa-id-card"></i> ' + (data.policy_no || 'No Policy');

                        if (data.registration_no) html += ' | <i class="fas fa-car"></i> ' + data.registration_no;
                        html += ' | <i class="fas fa-building"></i> ' + (data.insurance_company || 'No Company') + '</small>';

                        if (data.customer_email || data.customer_mobile) {
                            html += '<small class="d-block mt-1">';
                            if (data.customer_email) html += '<i class="fas fa-envelope"></i> ' + data.customer_email;
                            if (data.customer_email && data.customer_mobile) html += ' | ';
                            if (data.customer_mobile) html += '<i class="fas fa-phone"></i> ' + data.customer_mobile;
                            html += '</small>';
                        }

                        return $(html + '</div></div>');
                    },
                    templateSelection: function(result) {
                        if (result.data) {
                            var text = (result.data.customer_name || 'Unknown') + ' - ' + (result.data.policy_no || 'No Policy');
                            if (result.data.registration_no) text += ' (' + result.data.registration_no + ')';
                            return text;
                        }

                        // Handle initial selected option with data attributes
                        if (result.element && result.element.dataset) {
                            var customerName = result.element.dataset.customerName || 'Unknown';
                            var policyNo = result.element.dataset.policyNo || 'No Policy';
                            var registrationNo = result.element.dataset.registrationNo || '';

                            return customerName + ' - ' + policyNo +
                                   (registrationNo ? ' (' + registrationNo + ')' : '');
                        }

                        return result.text || 'Select a policy...';
                    }
                });

                // Handle policy selection
                $('.select2-policy').on('select2:select', function (e) {
                    var data = e.params.data.data;

                    // Handle data attributes for initial selection (edit mode)
                    if (!data && e.params.data.element && e.params.data.element.dataset) {
                        var element = e.params.data.element;
                        data = {
                            customer_name: element.dataset.customerName,
                            policy_no: element.dataset.policyNo,
                            registration_no: element.dataset.registrationNo,
                            insurance_company: element.dataset.insuranceCompany,
                            suggested_insurance_type: element.dataset.policyType === 'Health' ? 'Health' : 'Vehicle'
                        };
                    }

                    var detailsHtml = '<div class="row">' +
                        '<div class="col-md-3"><strong>Customer:</strong> ' + (data.customer_name || 'N/A') + '</div>' +
                        '<div class="col-md-3"><strong>Policy:</strong> ' + (data.policy_no || 'N/A') + '</div>' +
                        '<div class="col-md-3"><strong>Registration:</strong> ' + (data.registration_no || 'N/A') + '</div>' +
                        '<div class="col-md-3"><strong>Company:</strong> ' + (data.insurance_company || 'N/A') + '</div>' +
                    '</div>';

                    $('#policy-details-content').html(detailsHtml);
                    $('#policy-details').show();

                    if (data.suggested_insurance_type) $('#insurance_type').val(data.suggested_insurance_type);
                });

                $('.select2-policy').on('select2:clear', function () {
                    $('#policy-details').hide();
                });

                // Form validation function
                window.validatePolicySelection = function() {
                    if (!$('#customer_insurance_id').val()) {
                        show_notification('error', 'Please select a policy/insurance first.');
                        $('#customer_insurance_id').focus();
                        return false;
                    }
                    return true;
                };
            };

            // Auto-initialize when document is ready with retry mechanism
            $(document).ready(function() {
                console.log('Document ready, checking for policy selector...');

                // Simple initialization first
                if ($('.select2-policy').length > 0) {
                    console.log('Found policy selector elements:', $('.select2-policy').length);

                    // Destroy existing select2 instances first
                    if ($('.select2-policy').hasClass('select2-hidden-accessible')) {
                        $('.select2-policy').select2('destroy');
                        console.log('Destroyed existing select2 instance');
                    }

                    // Wait a bit for all libraries to load
                    setTimeout(function() {
                        if (typeof window.initializePolicySelector === 'function') {
                            try {
                                window.initializePolicySelector();
                                console.log('Policy selector initialized successfully');
                            } catch (error) {
                                console.error('Failed to initialize policy selector:', error);
                                // Retry after a delay
                                setTimeout(function() {
                                    try {
                                        window.initializePolicySelector();
                                        console.log('Policy selector initialized on retry');
                                    } catch (retryError) {
                                        console.error('Policy selector failed on retry:', retryError);
                                    }
                                }, 1000);
                            }
                        }
                    }, 500);
                } else {
                    console.log('No policy selector elements found on this page');
                }
            });
        </script>
    @endpush
@endonce