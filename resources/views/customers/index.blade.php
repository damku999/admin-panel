@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Customers Management</h1>
                        <small class="text-muted">Manage all customer records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('customer-create'))
                            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <a href="{{ route('customers.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
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
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('customer-edit'))
                                                <a href="{{ route('customers.resendOnBoardingWA', ['customer' => $customer->id]) }}"
                                                    class="btn btn-success btn-sm" title="Send Onboarding via WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('customer-edit'))
                                                <a href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Customer">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('customer-delete'))
                                                @if ($customer->status == 0)
                                                    <a href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Customer">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($customer->status == 1)
                                                    <a href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Customer">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('customer-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Customer"
                                                    onclick="delete_conf_common('{{ $customer['id'] }}','Customer','Customer', '{{ route('customers.index') }}');">
                                                    <i class="fa fa-trash-alt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <x-pagination-with-info :paginator="$customers" :request="$request" />
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
@endsection
