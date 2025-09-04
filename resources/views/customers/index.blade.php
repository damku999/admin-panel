@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Customers</h1>
            <div class="row">
                @if (auth()->user()->hasPermissionTo('customer-create'))
                    <div class="col-md-6">
                        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                @endif
                <div class="col-md-6">
                    <a href="{{ route('customers.export') }}" class="btn btn-sm btn-success">
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
                <form action="{{ route('customers.index') }}" method="GET" role="search">
                    <div class="input-group">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}"
                            style="margin-right: 10px;">
                        <select name="type" class="form-control" onchange="this.form.submit()"
                            style="margin-right: 10px;">
                            <option value="">All Types</option>
                            <option value="Retail" {{ request('type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                            <option value="Corporate" {{ request('type') == 'Corporate' ? 'selected' : '' }}>Corporate
                            </option>
                        </select>

                        {{--  <input type="text" placeholder="From Date" name="from_date" id="from_date"
                            class="form-control datepicker" value="{{ request('from_date') }}" style="margin-right: 10px;">
                        <input type="text" placeholder="To Date" name="to_date" id="to_date"
                            class="form-control datepicker" value="{{ request('to_date') }}" style="margin-right: 10px;"> --}}

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default filter_by_click">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-default filter_by_click">
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
                                    <a
                                        href="{{ route('customers.index', ['sort_field' => 'name', 'sort_order' => $sortField == 'name' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}">
                                        Name
                                        @if ($sortField == 'name')
                                            @if ($sortOrder == 'asc')
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
                                    <a
                                        href="{{ route('customers.index', ['sort_field' => 'email', 'sort_order' => $sortField == 'email' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}">
                                        Email
                                        @if ($sortField == 'email')
                                            @if ($sortOrder == 'asc')
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
                                    <a
                                        href="{{ route('customers.index', ['sort_field' => 'mobile_number', 'sort_order' => $sortField == 'mobile_number' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}">
                                        Mobile
                                        @if ($sortField == 'mobile_number')
                                            @if ($sortOrder == 'asc')
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
                                    <a
                                        href="{{ route('customers.index', ['sort_field' => 'type', 'sort_order' => $sortField == 'type' && $sortOrder == 'asc' ? 'desc' : 'asc']) }}">
                                        Type
                                        @if ($sortField == 'type')
                                            @if ($sortOrder == 'asc')
                                                <i class="fas fa-sort-up"></i>
                                            @else
                                                <i class="fas fa-sort-down"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->mobile_number }}</td>
                                    <td>{{ $customer->type }}</td>
                                    <td>
                                        @if ($customer->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($customer->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        <!-- 1. WhatsApp (First Priority) -->
                                        @if (auth()->user()->hasPermissionTo('customer-edit'))
                                            <a href="{{ route('customers.resendOnBoardingWA', ['customer' => $customer->id]) }}"
                                                class="btn btn-success m-2" title="Send Onboarding via WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        @endif

                                        <!-- 2. Edit (Second Priority) -->
                                        @if (auth()->user()->hasPermissionTo('customer-edit'))
                                            <a href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                                class="btn btn-primary m-2" title="Edit Customer">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif

                                        <!-- 3. Disable/Enable (Third Priority) -->
                                        @if (auth()->user()->hasPermissionTo('customer-delete'))
                                            @if ($customer->status == 0)
                                                <a href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2" title="Enable Customer">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($customer->status == 1)
                                                <a href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2" title="Disable Customer">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif

                                        <!-- Delete (Last) -->
                                        @if (auth()->user()->hasPermissionTo('customer-delete'))
                                            <a class="btn btn-danger m-2" href="javascript:void(0);" title="Delete Customer"
                                                onclick="delete_conf_common('{{ $customer['id'] }}','Customer','Customer', '{{ route('customers.index') }}');">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $customers->appends($request)->links() }}
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
@endsection
