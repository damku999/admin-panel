@extends('layouts.app')

@section('title', 'Reports List')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                Genrate All reports
                {{-- <input type="text" placeholder="Search" name="search" class="form-control float-right filter_by_key" value="{{ request('search') }}"> --}}
            </div>
            <form action="{{ route('reports.index') }}" method="GET" role="search">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="Select Columns" class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary form-control" id="openModalPopUpColumn">
                                Select Columns
                            </button>
                        </div>
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
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
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="customer_id" class="form-label">Customer : </label>
                            <select name="customer_id" id="customer_id" class=" w-100">
                                <option selected="selected" disabled="disabled">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('customer_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
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

                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="premium_type_id" class="form-label">Fuel Type : </label>
                            <select name="premium_type_id" class="form-control" id="premium_type_id">
                                <option selected="selected" disabled="disabled">Select Fuel Type</option>
                                @foreach ($premium_types as $item)
                                    <option id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ request('premium_type_id') == $item->id ? 'selected' : '' }}>
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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle insurance_detail">
                            <label for="issueDate" class="form-label"><span style="color:red;">*</span>Issue
                                Date</label>
                            <div class="d-flex">
                                <input type="text" placeholder="Start Date" name="issue_start_date"
                                    class="form-control datepicker" value="{{ request('issue_start_date') }}"
                                    style="margin-right: 10px;">
                                <input type="text" placeholder="End Date" name="issue_end_date"
                                    class="form-control datepicker" value="{{ request('issue_end_date') }}"
                                    style="margin-right: 10px;">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle due_policy_detail">
                            <label for="policyDueDate" class="form-label"><span style="color:red;">*</span>Month &
                                Year</label>
                            <div class="d-flex">
                                <input type="text" placeholder="Start Date" name="due_start_date"
                                    class="form-control datepicker_month" value="{{ request('due_start_date') }}"
                                    style="margin-right: 10px;" autocomplete="off" readonly id="due_start_date">
                                <input type="text" placeholder="End Date" name="due_end_date"
                                    class="form-control datepicker_month" value="{{ request('due_end_date') }}"
                                    style="margin-right: 10px;" autocomplete="off" readonly id="due_end_date">
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
                    <button type="submit" name="download"
                        class="btn btn-primary btn-reprts float-right  mr-3 mb-3 filter_by_click"><i
                            class="fas fa-download"></i> Download</button>
                    <button type="submit" name="view"
                        class="btn btn-primary btn-reprts float-right  mr-3 mb-3 filter_by_click"><i
                            class="fas fa-view"></i> View</button>
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
                                data-dismiss="modal">Close</button>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary saveColumns">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var openModalPopUpColumn = document.getElementById("openModalPopUpColumn");

            openModalPopUpColumn.addEventListener("click", function() {
                var reportName = document.querySelector("#reportName").value;
                if (reportName !== '') {
                    axios.get(
                            "{{ route('reports.load.selected.columns', ['report_name' => ':reportName']) }}"
                            .replace(':reportName', reportName))
                        .then(function(response) {
                            console.log(response);
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
            });
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
                        console.log(response);
                        $('#myModal').modal('hide');

                        toastr.success('Form submitted successfully!', 'Success');
                    },
                    error: function(error) {
                        toastr.error(error, 'Error');
                    }
                });
            });

            $(document).ready(function() {
                $('#customer_id').select2({
                    dropdownAutoWidth: true
                });

                $('.datepicker[name="issue_start_date"]').on('changeDate', function(selected) {
                    var endDate = $('.datepicker[name="issue_end_date"]');
                    endDate.datepicker('setStartDate', selected.date);
                    if (selected.date > endDate.datepicker('getDate')) {
                        endDate.datepicker('setDate', selected.date);
                    }
                });
                $('.datepicker[name="issue_end_date"]').on('changeDate', function(selected) {
                    var startDate = $('.datepicker[name="issue_start_date"]');
                    startDate.datepicker('setEndDate', selected.date);
                    if (selected.date < startDate.datepicker('getDate')) {
                        startDate.datepicker('setDate', selected.date);
                    }
                });

                $('.datepicker[name="record_creation_start_date"]').on('changeDate', function(selected) {
                    var endDate = $('.datepicker[name="record_creation_end_date"]');
                    endDate.datepicker('setStartDate', selected.date);
                    if (selected.date > endDate.datepicker('getDate')) {
                        endDate.datepicker('setDate', selected.date);
                    }
                });
                $('.datepicker[name="record_creation_end_date"]').on('changeDate', function(selected) {
                    var startDate = $('.datepicker[name="record_creation_start_date"]');
                    startDate.datepicker('setEndDate', selected.date);
                    if (selected.date < startDate.datepicker('getDate')) {
                        startDate.datepicker('setDate', selected.date);
                    }
                });

                // Month picker changes
                $('.datepicker_month').datepicker({
                    format: 'yyyy-mm', // Adjust the format as per your requirement
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true,
                    onChangeMonthYear: function(year, month, inst) {
                        var selectedDate = new Date(year, month - 1, 1);
                        console.log(this);
                        var selectedDate = $(this).datepicker('getDate');
                        if ($(this).attr('id') == 'due_start_date') {
                            $('#due_end_date').datepicker('option', 'minDate', selectedDate);
                        } else {
                            $('#due_start_date').datepicker('option', 'maxDate', selectedDate);
                        }
                    }
                });
            });
        });
    </script>

@endsection
