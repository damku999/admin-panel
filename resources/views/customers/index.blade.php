@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Customers Management"
                    subtitle="Manage all customer records"
                    addRoute="customers.create"
                    addPermission="customer-create"
                    exportRoute="customers.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('customers.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Customers</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name, email, mobile number..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type">Customer Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="Retail" {{ request('type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Corporate" {{ request('type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
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
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($customer->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
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
