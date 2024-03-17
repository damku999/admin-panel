@extends('layouts.app')

@section('title', 'Customer Insurance List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Customer Insurance</h1>
            <div class="row">
                @if (auth()->user()->hasPermissionTo('customer-insurance-create'))
                    <div class="col-md-6">
                        <a href="{{ route('customer_insurances.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                @endif
                <div class="col-md-6">
                    <a href="{{ route('customer_insurances.export') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-check"></i> Export To Excel
                    </a>
                </div>

            </div>

        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                {{-- <h6 class="m-0 font-weight-bold text-primary"></h6> --}}
                <form action="{{ route('customer_insurances.index') }}" method="GET" role="search">
                    <div class="input-group">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}"
                            style="margin-right: 10px;">
                        <!-- New filter for expiring date range -->
                        <input type="text" placeholder="Exp Start Date" name="start_date" class="form-control datepicker"
                            value="{{ request('start_date') }}" style="margin-right: 10px;">
                        <input type="text" placeholder="Exp End Date" name="end_date" class="form-control datepicker"
                            value="{{ request('end_date') }}" style="margin-right: 10px;">
                        <select name="customer_id" class="form-control" id="customer_id">
                            <option selected="selected" disabled="disabled">Select Customer</option>
                            @foreach ($customers as $item)
                                <option id="{{ $item->id }}" value="{{ $item->id }}"
                                    {{ request('customer_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default filter_by_click">
                                <i class="fas fa-search"></i>
                            </button>

                            <a href="{{ route('customer_insurances.index') }}" class="btn btn-default filter_by_click">
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
                                <th width="20%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'customer_name', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'customer_name' ? 'active' : '' }}">Customer Name
                                        @if ($sort === 'customer_name')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="25%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'policy_no', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'policy_no' ? 'active' : '' }}">POLICY NO.
                                        @if ($sort === 'policy_no')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'registration_no', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'registration_no' ? 'active' : '' }}">Registration NO.
                                        @if ($sort === 'registration_no')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'start_date', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'start_date' ? 'active' : '' }}">Start Date
                                        @if ($sort === 'start_date')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'expired_date', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'expired_date' ? 'active' : '' }}"> Expired Date
                                        @if ($sort === 'expired_date')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'premium_types.name', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'premium_types.name' ? 'active' : '' }}">Premium Type
                                        @if ($sort === 'premium_types.name')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>

                                <th width="15%">
                                    <a href="{{ route('customer_insurances.index', ['sort' => 'status', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
                                        class="{{ $sort === 'status' ? 'active' : '' }}">Status
                                        @if ($sort === 'status')
                                            @if ($direction === 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($customer_insurances as $customer_insurance)
                                <tr>
                                    <td>{{ $customer_insurance->customer_name }}</td>
                                    <td>{{ $customer_insurance->policy_no }}</td>
                                    <td>{{ $customer_insurance->registration_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($customer_insurance->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($customer_insurance->expired_date)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $customer_insurance->policy_type_name }}</td>
                                    <td>
                                        @if ($customer_insurance->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($customer_insurance->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        @if (auth()->user()->hasPermissionTo('customer-insurance-delete'))
                                            @if ($customer_insurance->status == 0)
                                                <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($customer_insurance->status == 1)
                                                <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('customer-insurance-edit'))
                                            <a href="{{ route('customer_insurances.edit', ['customer_insurance' => $customer_insurance->id]) }}"
                                                class="btn btn-primary m-2">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($customer_insurance->policy_document_path)
                                            <a href="{{ asset('storage/' . $customer_insurance->policy_document_path) }}"
                                                class="btn btn-info m-2" target="__blank"><i
                                                    class="fa fa-download"></i></a>
                                        @endif

                                        {{-- <a class="btn btn-danger m-2" href="javascript:void(0);"
                                            onclick="delete_conf_common('{{ $customer_insurance->id }}','CustomerInsurance', 'Customer Insurance', '{{ route('customer_insurances.index') }}');"><i
                                                class="fas fa-trash"></i></a> --}}
                                        {{-- <a class="btn btn-danger m-2" href="javascript:void(0);" onclick="delete_conf_common('{{ $customer_insurance->id }}','CustomerInsurance', 'CustomerInsurance', '{{ route('customer_insurances.index') }}');"><i class="fas fa-trash"></i></a> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $customer_insurances->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('#customer_id').select2();
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd', // Adjust the format as per your requirement
                autoclose: true
            });

            // Optional: Add logic to ensure the start date is before or equal to the end date
            $('.datepicker[name="start_date"]').on('changeDate', function(selected) {
                var endDate = $('.datepicker[name="end_date"]');
                endDate.datepicker('setStartDate', selected.date);
                if (selected.date > endDate.datepicker('getDate')) {
                    endDate.datepicker('setDate', selected.date);
                }
            });

            // Optional: Add logic to ensure the end date is after or equal to the start date
            $('.datepicker[name="end_date"]').on('changeDate', function(selected) {
                var startDate = $('.datepicker[name="start_date"]');
                startDate.datepicker('setEndDate', selected.date);
                if (selected.date < startDate.datepicker('getDate')) {
                    startDate.datepicker('setDate', selected.date);
                }
            });
        });
    </script>
@endsection
@section('stylesheets')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
