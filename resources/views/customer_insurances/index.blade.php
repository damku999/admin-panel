@extends('layouts.app')

@section('title', 'Customer Insurance List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Customer Insurances Management</h1>
                        <small class="text-muted">Manage all active insurance policies</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('customer-insurance-create'))
                            <a href="{{ route('customer_insurances.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <a href="{{ route('customer_insurances.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('customer_insurances.index') }}" method="GET" role="search">
                    <div class="input-group">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key mr-2" value="{{ request('search') }}">

                        <input type="text" placeholder="Exp Start Date" name="start_date"
                            class="form-control datepicker mr-2" value="{{ request('start_date') }}">

                        <input type="text" placeholder="Exp End Date" name="end_date"
                            class="form-control datepicker mr-2" value="{{ request('end_date') }}">

                        {{-- <select name="status" class="form-control" id="status">
                            <option value="all" {{ request('status', '1') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="1" {{ request('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status', '1') == '0' ? 'selected' : '' }}>In Active</option>
                        </select> --}}

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default filter_by_click">
                                <i class="fas fa-search"></i>
                            </button>

                            <a href="{{ route('customer_insurances.index') }}" class="btn btn-default filter_by_click">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                    <div class="input-group mt-2">

                    </div>
                    <input type="hidden" name="sort" value="{{ request('sort', 'updated_at') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="22%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'customer_name', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th width="22%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'policy_no', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th width="14%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'registration_no', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th width="10%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'start_date', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th width="10%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'expired_date', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="{{ $sort === 'expired_date' ? 'active' : '' }}">Expired Date
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
                                <th width="12%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'premium_types.name', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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

                                <th width="10%">
                                    <a href="{{ route('customer_insurances.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
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
                                <th>Action</th>
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
                                    <td>
                                        @php
                                            $expiredDate = \Carbon\Carbon::parse($customer_insurance->expired_date);
                                            $oneMonthBefore = $expiredDate->copy()->subMonth();
                                            $oneMonthAfter = $expiredDate->copy()->addMonth();
                                            $currentDate = \Carbon\Carbon::now();
                                        @endphp
                                        
                                        <div class="d-flex flex-nowrap" style="gap: 4px; justify-content: flex-start; align-items: center; overflow-x: auto;">
                                            <!-- 1. WhatsApp Send Document -->
                                            @if ($customer_insurance->policy_document_path)
                                                <a href="{{ route('customer_insurances.sendWADocument', ['customer_insurance' => $customer_insurance->id]) }}"
                                                    class="btn btn-success btn-sm" title="Send Document via WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- 2. WhatsApp Renewal Reminder -->
                                            @if (auth()->user()->hasPermissionTo('customer-insurance-edit') && $currentDate->between($oneMonthBefore, $oneMonthAfter) && $customer_insurance->is_renewed == 0)
                                                <a href="{{ route('customer_insurances.sendRenewalReminderWA', ['customer_insurance' => $customer_insurance->id]) }}"
                                                    class="btn btn-warning btn-sm" title="Send Renewal Reminder via WhatsApp" style="white-space: nowrap;">
                                                    <i class="fa fa-bell"></i><i class="fab fa-whatsapp" style="margin-left: 1px;"></i>
                                                </a>
                                            @endif

                                            <!-- 3. Edit -->
                                            @if (auth()->user()->hasPermissionTo('customer-insurance-edit'))
                                                <a href="{{ route('customer_insurances.edit', ['customer_insurance' => $customer_insurance->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Policy">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            <!-- 4. Download -->
                                            @if ($customer_insurance->policy_document_path)
                                                <a href="{{ asset('storage/' . $customer_insurance->policy_document_path) }}"
                                                    class="btn btn-info btn-sm" target="_blank" title="Download Policy Document">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif

                                            <!-- 5. Renew -->
                                            @if (auth()->user()->hasPermissionTo('customer-insurance-edit') && $currentDate->between($oneMonthBefore, $oneMonthAfter) && $customer_insurance->is_renewed == 0)
                                                <a href="{{ route('customer_insurances.renew', ['customer_insurance' => $customer_insurance->id]) }}"
                                                    class="btn btn-secondary btn-sm" title="Renew Policy">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            @endif

                                            <!-- 6. Enable/Disable -->
                                            @if (auth()->user()->hasPermissionTo('customer-insurance-delete'))
                                                @if ($customer_insurance->status == 0)
                                                    <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Policy">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('customer_insurances.status', ['customer_insurance_id' => $customer_insurance->id, 'status' => 0]) }}"
                                                        class="btn btn-danger btn-sm" title="Disable Policy">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            <!-- 7. Delete -->
                                            @if (auth()->user()->hasPermissionTo('customer-insurance-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Policy"
                                                    onclick="delete_conf_common('{{ $customer_insurance->id }}','CustomerInsurance', 'Customer Insurance', '{{ route('customer_insurances.index') }}');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $customer_insurances->appends($request)->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#customer_id').select2();

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
    <style>
        .icon-group {
            display: inline-flex !important;
            align-items: center !important;
        }

        .icon-group svg {
            margin-left: 10px !important;
            /* Adjust spacing as needed */
        }

        .icon-group svg:first-child {
            margin-left: 0 !important;
        }
    </style>
@endsection
