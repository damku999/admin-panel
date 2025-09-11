@extends('layouts.app')

@section('title', 'Reports List')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Reports Management"
                    subtitle="Generate and export system reports"
            />
            <form action="{{ route('reports.index') }}" method="GET" role="search">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        {{-- <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="Select Columns" class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary form-control" id="openModalPopUpColumn">
                                Select Columns
                            </button>
                        </div> --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="reportName" class="form-label"><span style="color:red;">*</span>Report
                                Name:</label>
                            <select class="form-control form-control-reprts @error('report_name') is-invalid @enderror"
                                id="reportName" name="report_name">
                                <option value="">Select Report Name</option>
                                @foreach (config('constants.REPORTS') as $reportName => $reportDescription)
                                    <option
                                        value="{{ $reportName }}"{{ request('report_name') === $reportName ? ' selected' : '' }}>
                                        {{ $reportDescription }}
                                    </option>
                                @endforeach
                            </select>
                            @error('report_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle due_policy_detail">
                            <label class="form-label"><span style="color:red;">*</span>Report Period</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="due_start_date" class="form-label text-muted small">From Month:</label>
                                    <input type="text" placeholder="Select month" name="due_start_date"
                                        class="form-control form-control-sm datepicker_month" value="{{ request('due_start_date') }}"
                                        autocomplete="off" readonly id="due_start_date">
                                </div>
                                <div class="col-6">
                                    <label for="due_end_date" class="form-label text-muted small">To Month:</label>
                                    <input type="text" placeholder="Select month" name="due_end_date"
                                        class="form-control form-control-sm datepicker_month" value="{{ request('due_end_date') }}"
                                        autocomplete="off" readonly id="due_end_date">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="daterange" class="form-label"><span style="color:red;">*</span>Creation
                                Date</label>
                            <div class="d-flex">
                                <input type="text" placeholder="Start Date" name="record_creation_start_date"
                                    class="form-control datepicker" value="{{ request('record_creation_start_date') }}"
                                    style="margin-right: 10px;">
                                <input type="text" placeholder="End Date" name="record_creation_end_date"
                                    class="form-control datepicker" value="{{ request('record_creation_end_date') }}"
                                    style="margin-right: 10px;">
                            </div>
                        </div> --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="customer_id" class="form-label">Customer : </label>
                            <select name="customer_id" id="customer_id" class=" w-100">
                                <option selected="selected" disabled="disabled">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        data-mobile="{{ $item->mobile_number }}"
                                        {{ request('customer_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                        @if ($item->mobile_number)
                                            - {{ $item->mobile_number }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="branch_id" class="form-label">Branches : </label>
                            <select name="branch_id" class="form-control" id="branch_id">
                                <option selected="selected" disabled="disabled">Select Branches</option>
                                @foreach ($branches as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('branch_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="broker_id" class="form-label">Brokers : </label>
                            <select name="broker_id" class="form-control" id="broker_id">
                                <option selected="selected" disabled="disabled">Select Brokers</option>
                                @foreach ($brokers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('broker_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="relationship_manager_id" class="form-label">RM : </label>
                            <select name="relationship_manager_id" class="form-control" id="relationship_manager_id">
                                <option selected="selected" disabled="disabled">Select RM</option>
                                @foreach ($relationship_managers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('relationship_manager_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="insurance_company_id" class="form-label">Insurance Company : </label>
                            <select name="insurance_company_id" class="form-control" id="insurance_company_id">
                                <option selected="selected" disabled="disabled">Select Insurance Company</option>
                                @foreach ($insurance_companies as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('insurance_company_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="policy_type_id" class="form-label">Policy Type : </label>
                            <select name="policy_type_id" class="form-control" id="policy_type_id">
                                <option selected="selected" disabled="disabled">Select Policy Type</option>
                                @foreach ($policy_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('policy_type_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="fuel_type_id" class="form-label">Fuel Type : </label>
                            <select name="fuel_type_id" class="form-control" id="fuel_type_id">
                                <option selected="selected" disabled="disabled">Select Fuel Type</option>
                                @foreach ($fuel_type as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('fuel_type_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="premium_type_id" class="form-label">Premium Type:</label>
                            <select name="premium_type_id[]" class="form-control" id="premium_type_id"
                                multiple="multiple">
                                <option value="" disabled>Select Premium Type</option>
                                @foreach ($premium_types as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ in_array($item->id, request('premium_type_id', [])) ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="reference_by" class="form-label">Reference By : </label>
                            <select name="reference_by" class="form-control" id="reference_by">
                                <option selected="selected" disabled="disabled">Select Reference By</option>
                                @foreach ($reference_by_user as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('reference_by') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle insurance_detail cross_selling">
                            <label for="issueDate" class="form-label"><span style="color:red;">*</span>Issue Date</label>
                            <div class="d-flex">
                                <input type="text" placeholder="Start Date" name="issue_start_date"
                                    class="form-control datepicker" value="{{ request('issue_start_date') }}"
                                    style="margin-right: 10px;">
                                <input type="text" placeholder="End Date" name="issue_end_date"
                                    class="form-control datepicker" value="{{ request('issue_end_date') }}"
                                    style="margin-right: 10px;">
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle policy_detail ">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {{-- <button type="submit" name="search"
                        class="btn btn-success btn-reprts float-right mb-3 filter_by_click"><i class="fas fa-search"></i>
                        Search</button> --}}
                    <button type="submit" name="download" onclick="setDownloadAction(this)"
                        class="btn btn-primary btn-reprts float-right  mr-3 mb-3 filter_by_click"><i
                            class="fas fa-download"></i> Download</button>
                    <button type="submit" name="view" value="1" onclick="return validateReportForm()"
                        class="btn btn-primary btn-reprts float-right  mr-3 mb-3"><i
                            class="fa-solid fa-eye"></i> View</button>
                    <a class="btn btn-warning float-right mr-3 mb-3 filter_by_click" href="{{ route('reports.index') }}">
                        <i class="fas fa-redo"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="columnSelectionForm" action="{{ route('reports.save.selected.columns') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select and Reorder Columns</h5>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <button type="button" class="btn btn-secondary float-right ml-2"
                                onclick="hideModal('modalPopUpColumn')">Close</button>
                            <button type="button" class="btn btn-primary float-right ml-2 saveColumns">Save
                                changes</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <table class="table table-responsive table-bordered table-striped draggable-table">
                            <tbody id="sortableTableBody" class="table-hover">

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('modalPopUpColumn')">Close</button>
                        <button type="button" class="btn btn-primary saveColumns">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Debug info --}}
    @if(request()->has('view') && config('app.debug'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Report Name: {{ request('report_name') }}<br>
            Issue Start Date: {{ request('issue_start_date') }}<br>
            Issue End Date: {{ request('issue_end_date') }}<br>
            <strong>Has View Parameter:</strong> {{ request()->has('view') ? 'YES' : 'NO' }}<br>
            <strong>Has Download Parameter:</strong> {{ request()->has('download') ? 'YES' : 'NO' }}<br>
            <strong>Report Name:</strong> {{ request()->get('report_name', 'Not set') }}<br>
            Results Count: {{ isset($customerInsurances) ? (is_countable($customerInsurances) ? count($customerInsurances) : 'Not countable') : 'Not set' }}<br>
            Results Type: {{ isset($customerInsurances) ? gettype($customerInsurances) : 'Not set' }}
        </div>
    @endif
    
    @if (!empty($customerInsurances))
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Report</h6>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Policy No</th>
                                    <th>Broker Name</th>
                                    <th>RM Name</th>
                                    <th>Premium Type</th>
                                    <th>Insurance Company Name</th>
                                    <th>Commission %</th>
                                    <th>My Commission Amount</th>
                                    <th>Transfer Commission</th>
                                    <th>Actual Earnings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCommissionAmount = 0;
                                    $totalTransferCommission = 0;
                                    $totalActualEarnings = 0;
                                @endphp
                                @foreach ($customerInsurances as $customerInsurance)
                                    <tr>
                                        <td>{{ $customerInsurance->customer->name }}</td>
                                        <td>{{ $customerInsurance->policy_no }}</td>
                                        <td>{{ $customerInsurance->broker->name }}</td>
                                        <td>{{ $customerInsurance->relationshipManager->name }}</td>
                                        <td>{{ $customerInsurance->premiumType->name }}</td>
                                        <td>{{ $customerInsurance->insuranceCompany->name }}</td>
                                        <td>{{ $customerInsurance->my_commission_percentage }}</td>
                                        <td>{{ $customerInsurance->my_commission_amount }}</td>
                                        <td>{{ $customerInsurance->transfer_commission_amount }}</td>
                                        <td>{{ $customerInsurance->actual_earnings }}</td>
                                    </tr>
                                    @php
                                        $totalCommissionAmount += $customerInsurance->my_commission_amount;
                                        $totalTransferCommission += $customerInsurance->transfer_commission_amount;
                                        $totalActualEarnings += $customerInsurance->actual_earnings;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="7" class="text-right">Total:</td>
                                    <td>{{ $totalCommissionAmount }}</td>
                                    <td>{{ $totalTransferCommission }}</td>
                                    <td>{{ $totalActualEarnings }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (!empty($crossSelling) && !empty($premium_types))
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cross Selling Report</h6>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Total Premium Collected (Last Year)</th>
                                    <th>Actual Earnings (Last Year)</th>
                                    @foreach ($premiumTypes as $premiumType)
                                        <th>{{ $premiumType->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crossSelling as $customerData)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer_insurances.index', ['customer_id' => $customerData['id']]) }}"
                                                target="_blank">
                                                {{ $customerData['customer_name'] }}
                                            </a>
                                        </td>

                                        <td>{{ number_format($customerData['total_premium_last_year'], 2) }}</td>
                                        <td>{{ number_format($customerData['actual_earnings_last_year'], 2) }}</td>
                                        <!-- New Total Premium Column -->
                                        @foreach ($premiumTypes as $premiumType)
                                            <td>{{ $customerData['premium_totals'][$premiumType->name]['has_premium'] }}
                                                @if (!empty($customerData['premium_totals'][$premiumType->name]['amount']))
                                                    /
                                                    <i class="fa fas fa-rupee-sign"></i>
                                                    {{ $customerData['premium_totals'][$premiumType->name]['amount'] }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('stylesheets')
    <style>
        .hidden {
            display: none;
        }
    </style>

@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* var openModalPopUpColumn = document.getElementById("openModalPopUpColumn");

            openModalPopUpColumn.addEventListener("click", function() {
                var reportName = document.querySelector("#reportName").value;
                if (reportName !== '') {
                    axios.get(
                            "{{ route('reports.load.selected.columns', ['report_name' => ':reportName']) }}"
                            .replace(':reportName', reportName))
                        .then(function(response) {
                            $('#myModal').modal('show');
                            var responseData = response.data;
                            document.querySelector('#sortableTableBody').innerHTML = responseData;
                            toastr.success('Data fetched successfully!', 'Success');
                        })
                        .catch(function(error) {
                            toastr.error('An error occurred while fetching data.', 'Error');
                        });
                } else
                    toastr.error('Select Report first.', 'Error');
            }); */
            const reportNameSelect = document.getElementById("reportName");
            const fieldsToToggle = document.querySelectorAll(".fields-to-toggle");

            function toggleFieldsVisibility(selectedReportValue) {
                fieldsToToggle.forEach(function(field) {
                    if (field.classList.contains(selectedReportValue)) {
                        field.classList.remove("hidden");
                    } else {
                        field.classList.add("hidden");
                    }
                });
            }

            // Check the selected report value on page load
            const initialSelectedReportValue = reportNameSelect.value;
            toggleFieldsVisibility(initialSelectedReportValue);

            reportNameSelect.addEventListener("change", function() {
                const selectedReportValue = this.value;
                toggleFieldsVisibility(selectedReportValue);
            });

            const saveButton = document.querySelector(".saveColumns");

            saveButton.addEventListener("click", function() {
                const reportName = document.querySelector("#reportName").value;
                const sortedRows = Array.from(sortableTableBody.children);
                const sortedIds = sortedRows.map(row => row.getAttribute('data-id'));
                const selectedColumns = [];
                document.querySelectorAll(".form-check-input:checked").forEach(function(checkbox) {
                    selectedColumns.push(checkbox.name);
                });

                $.ajax({
                    url: "{{ route('reports.save.selected.columns') }}",
                    method: 'POST',
                    data: {
                        sorted_column_ids: sortedIds,
                        _token: '{{ csrf_token() }}',
                        report_name: reportName,
                        selected_columns: selectedColumns,
                    },
                    success: function(response) {
                        $('#myModal').modal('hide');

                        toastr.success('Form submitted successfully!', 'Success');
                    },
                    error: function(error) {
                        toastr.error(error, 'Error');
                    }
                });
            });

            $(document).ready(function() {
                // Initialize Select2
                // Initialize Select2 for Customer dropdown with enhanced search
                $('#customer_id').select2({
                    placeholder: 'Search and select customer...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    escapeMarkup: function(markup) {
                        return markup; // Allow HTML in results
                    },
                    templateResult: function(option) {
                        if (!option.id || option.loading) {
                            return option.text;
                        }
                        
                        // Get mobile number from data attribute if available  
                        const $option = $(option.element);
                        const mobile = $option.data('mobile');
                        const customerName = option.text.split(' - ')[0];
                        
                        if (mobile) {
                            return '<div style="padding: 5px;"><strong>' + customerName + '</strong><br><small class="text-muted" style="color: #6c757d;">📱 ' + mobile + '</small></div>';
                        }
                        
                        return '<div style="padding: 5px;">' + customerName + '</div>';
                    },
                    templateSelection: function(option) {
                        if (!option.id) {
                            return option.text;
                        }
                        
                        // Show just the customer name in the selection
                        const customerName = option.text.split(' - ')[0];
                        return customerName;
                    }
                });
                // Get selected values from the request
                const selectedValues = {!! json_encode(request('premium_type_id', [])) !!};

                // If no values are selected in the request, select all options except empty values
                if (selectedValues.length === 0) {
                    $('#premium_type_id option').each(function() {
                        const value = $(this).val();
                        if (value) { // Avoid selecting empty options
                            $(this).prop('selected', true);
                        }
                    });
                }

                // Trigger change event to update Select2
                $('#premium_type_id').trigger('change');

                // Initialize Issue Date pickers with Flatpickr API
                const issueStartPicker = flatpickr('input[name="issue_start_date"]', {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            // Set minimum date for end date picker
                            issueEndPicker.set('minDate', selectedDates[0]);
                        }
                    }
                });
                
                const issueEndPicker = flatpickr('input[name="issue_end_date"]', {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            // Set maximum date for start date picker
                            issueStartPicker.set('maxDate', selectedDates[0]);
                        }
                    }
                });


                // Month picker dependencies are now handled globally in app.blade.php
            });
        });

        // Validation function for report form
        function validateReportForm() {
            const reportName = document.getElementById('reportName').value;
            
            if (!reportName) {
                toastr.error('Please select a Report Name first.', 'Validation Error');
                return false;
            }
            
            // For due_policy_detail report, check if due date range is provided
            if (reportName === 'due_policy_detail') {
                const dueStartDate = document.getElementById('due_start_date').value;
                const dueEndDate = document.getElementById('due_end_date').value;
                
                if (!dueStartDate && !dueEndDate) {
                    toastr.error('Please provide Due Date range for Due Policy Details report.', 'Validation Error');
                    return false;
                }
            }
            
            // For insurance_detail and cross_selling, check if issue date is provided
            if (reportName === 'insurance_detail' || reportName === 'cross_selling') {
                const issueStartDate = document.querySelector('input[name="issue_start_date"]').value;
                const issueEndDate = document.querySelector('input[name="issue_end_date"]').value;
                
                if (!issueStartDate && !issueEndDate) {
                    toastr.error('Please provide Issue Date range for this report type.', 'Validation Error');
                    return false;
                }
            }
            
            return true;
        }
        
        function setDownloadAction(button) {
            // Change form action to export route when download is clicked
            const form = button.closest('form');
            form.action = '{{ route("reports.export") }}';
            
            if (validateReportForm()) {
                // Show processing state
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                button.disabled = true;
                
                // Better approach: Reset when page becomes visible again (after download)
                const resetButton = () => {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                };
                
                // Multiple reset triggers
                setTimeout(resetButton, 5000); // Fallback after 5 seconds
                
                // Reset when user returns to page (after download)
                document.addEventListener('visibilitychange', function onVisibilityChange() {
                    if (!document.hidden) {
                        resetButton();
                        document.removeEventListener('visibilitychange', onVisibilityChange);
                    }
                });
                
                // Reset on window focus (when user returns to page)
                window.addEventListener('focus', function onFocus() {
                    setTimeout(() => {
                        resetButton();
                        window.removeEventListener('focus', onFocus);
                    }, 1000);
                });
                
                return true;
            }
            return false;
        }
    </script>

@endsection
