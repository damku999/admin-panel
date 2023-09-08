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
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 ">
                            <label for="reportName" class="form-label"><span style="color:red;">*</span>Report Name</label>
                            <select class="form-control form-control-reprts @error('report_name') is-invalid @enderror"
                                id="reportName" name="report_name">
                                <option value="">Select Report Name</option>
                                @foreach (config('constants.REPORTS') as $reportName => $reportDescription)
                                    <option
                                        value="{{ $reportName }}"{{ old('report_name') === $reportName ? ' selected' : '' }}>
                                        {{ $reportDescription }}
                                    </option>
                                @endforeach
                            </select>
                            @error('report_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label for="reportName" class="form-label"><span style="color:red;">*</span>Creation
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
                            <label for="reportName" class="form-label">Customer : </label>
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
                        <div class="col-sm-3 col-md-2 mt-10 mb-sm-0 justify-content">
                            <label for="Select Columns" class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary mt-3 form-control" id="openModalPopUpColumn">
                                Select Columns
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0 fields-to-toggle insurance_detail">
                            <label for="reportName" class="form-label"><span style="color:red;">*</span>Issue Date</label>
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
                    <button type="submit" class="btn btn-success btn-reprts float-right mb-3 filter_by_click"><i
                            class="fas fa-search"></i> Search</button>
                    <a class="btn btn-primary float-right mr-3 mb-3 filter_by_click" href="{{ route('reports.index') }}">
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

@endsection

@section('stylesheets')
    <style>
        .hidden {
            display: none;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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

                // Date picker changes
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd', // Adjust the format as per your requirement
                    autoclose: true
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
            });
        });
    </script>

@endsection
