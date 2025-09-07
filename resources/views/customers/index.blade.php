@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div
                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Customers Management</h1>
                        <small class="text-muted">Manage all customer records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('customer-create'))
                            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <x-buttons.export-button export-url="{{ route('customers.export') }}" :formats="['xlsx', 'csv']"
                            :show-dropdown="true" :with-filters="true" title="Export Customers">
                            Export Customers
                        </x-buttons.export-button>
                    </div>
                </div>
                <!-- Enhanced Search and Filters -->
                <form action="{{ route('customers.index') }}" method="GET" role="search" class="customers-search-form">
                    <div class="row g-2 align-items-end">
                        <!-- Main Search Field -->
                        <div class="col-lg-6 col-md-8 col-sm-12 mb-2">
                            <x-forms.search-field id="customersSearch" name="search"
                                placeholder="Search by name, email, mobile number, or address" :value="request('search')"
                                :with-button="true" button-text="Search" button-class="btn-primary"
                                button-icon="fas fa-search" :clear-button="true"
                                button-onclick="document.querySelector('.customers-search-form').submit()" />
                        </div>

                        <!-- Customer Type Filter -->
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <label for="type" class="form-label text-sm fw-medium">Customer Type</label>
                            <select name="type" id="type" class="form-select form-select-sm"
                                data-placeholder="All Types" onchange="this.form.submit()">
                                <option value="" {{ request('type') == '' ? 'selected' : '' }}>All Types</option>
                                <option value="Retail" {{ request('type') == 'Retail' ? 'selected' : '' }}>
                                    👤 Retail
                                </option>
                                <option value="Corporate" {{ request('type') == 'Corporate' ? 'selected' : '' }}>
                                    🏢 Corporate
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <label for="status" class="form-label text-sm fw-medium">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm"
                                data-placeholder="All Status" onchange="this.form.submit()">
                                <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                    ✅ Active
                                </option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                    ❌ Inactive
                                </option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-lg-2 col-md-12 mb-2">
                            <a href="{{ route('customers.index') }}"
                                class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-undo me-1"></i> Reset Filters
                            </a>
                        </div>

                        <!-- Quick Filter Badges -->
                        @if (request()->hasAny(['search', 'type', 'status']))
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-1 mt-2 mb-1">
                                    <small class="text-muted me-2">Active filters:</small>
                                    @if (request('search'))
                                        <span class="badge bg-primary">
                                            <i class="fas fa-search me-1"></i>Search: {{ request('search') }}
                                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if (request('type'))
                                        <span class="badge bg-info">
                                            <i class="fas fa-filter me-1"></i>Type: {{ request('type') }}
                                            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                    @if (request('status'))
                                        <span class="badge bg-success">
                                            <i
                                                class="fas fa-toggle-{{ request('status') == '1' ? 'on' : 'off' }} me-1"></i>Status:
                                            {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
                                                class="text-white ms-1" title="Remove filter">&times;</a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card-body">
                <!-- Enhanced Table with DataTableManager Integration -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered data-table" id="customersDataTable" data-datatable="true"
                        data-page-length="25" data-server-side="false" data-responsive="true" data-order='[[0, "asc"]]'
                        data-column-defs='[
                               { "targets": [5], "orderable": false }
                           ]'>
                        <thead class="table-dark">
                            <tr>
                                <th width="20%" class="sortable" data-sort="name">
                                    <span class="d-flex align-items-center">
                                        Customer Name
                                        <i class="fas fa-sort ms-1 text-muted sort-icon"></i>
                                    </span>
                                </th>
                                <th width="25%" class="sortable" data-sort="email">
                                    <span class="d-flex align-items-center">
                                        Email
                                        <i class="fas fa-sort ms-1 text-muted sort-icon"></i>
                                    </span>
                                </th>
                                <th width="15%" class="sortable" data-sort="mobile_number">
                                    <span class="d-flex align-items-center">
                                        Mobile
                                        <i class="fas fa-sort ms-1 text-muted sort-icon"></i>
                                    </span>
                                </th>
                                <th width="10%" class="sortable" data-sort="type">
                                    <span class="d-flex align-items-center">
                                        Type
                                        <i class="fas fa-sort ms-1 text-muted sort-icon"></i>
                                    </span>
                                </th>
                                <th width="10%" class="sortable" data-sort="status">
                                    <span class="d-flex align-items-center">
                                        Status
                                        <i class="fas fa-sort ms-1 text-muted sort-icon"></i>
                                    </span>
                                </th>
                                <th width="20%" class="no-sort">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($customers as $customer)
                                <tr data-customer-id="{{ $customer->id }}">
                                    <td data-sort="{{ $customer->name }}">
                                        <div class="customer-info">
                                            <strong class="d-block">{{ $customer->name }}</strong>
                                            @if ($customer->address)
                                                <small class="text-muted d-block">
                                                    <i
                                                        class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($customer->address, 50) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-sort="{{ $customer->email }}">
                                        <div class="email-info">
                                            @if ($customer->email)
                                                <span class="d-block">{{ $customer->email }}</span>
                                                @if ($customer->email_verified_at)
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Verified
                                                    </small>
                                                @else
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Unverified
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic">No email</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-sort="{{ $customer->mobile_number }}">
                                        <div class="mobile-info">
                                            <strong class="d-block">
                                                <i
                                                    class="fas fa-phone-alt me-1 text-primary"></i>{{ $customer->mobile_number }}
                                            </strong>
                                            @if ($customer->whatsapp_number && $customer->whatsapp_number !== $customer->mobile_number)
                                                <small class="text-success d-block">
                                                    <i class="fab fa-whatsapp me-1"></i>{{ $customer->whatsapp_number }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-sort="{{ $customer->type }}">
                                        <x-buttons.status-badge :status="$customer->type" :status-colors="[
                                            'Retail' => 'info',
                                            'Corporate' => 'primary',
                                        ]" />
                                    </td>
                                    <td data-sort="{{ $customer->status }}">
                                        <x-buttons.status-badge :status="$customer->status == 1 ? 'Active' : 'Inactive'" :status-colors="[
                                            'Active' => 'success',
                                            'Inactive' => 'danger',
                                        ]" />
                                    </td>
                                    <x-tables.action-column>
                                        @if (auth()->user()->hasPermissionTo('customer-edit'))
                                            <x-buttons.whatsapp-button
                                                href="{{ route('customers.resendOnBoardingWA', ['customer' => $customer->id]) }}"
                                                title="Send Onboarding via WhatsApp" size="sm" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('customer-edit'))
                                            <x-buttons.action-button variant="primary" size="sm" icon="fas fa-edit"
                                                href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                                title="Edit Customer" />
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('customer-delete'))
                                            @if ($customer->status == 0)
                                                <x-buttons.action-button variant="success" size="sm"
                                                    icon="fas fa-check"
                                                    href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 1]) }}"
                                                    title="Enable Customer" />
                                            @elseif ($customer->status == 1)
                                                <x-buttons.action-button variant="warning" size="sm"
                                                    icon="fas fa-ban"
                                                    href="{{ route('customers.status', ['customer_id' => $customer->id, 'status' => 0]) }}"
                                                    title="Disable Customer" />
                                            @endif
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('customer-delete'))
                                            <x-buttons.action-button variant="danger" size="sm"
                                                icon="fas fa-trash-alt"
                                                onclick="delete_conf_common('{{ $customer->id }}','Customer','{{ $customer->name }}', '{{ route('customers.index') }}')"
                                                title="Delete Customer" />
                                        @endif
                                    </x-tables.action-column>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-users me-2"></i>
                                        No customers found. Add your first customer to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Enhanced Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }}
                                of {{ $customers->total() }} customers
                            </small>
                        </div>
                        <div>
                            {{ $customers->appends($request)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
    <!-- Customers module JavaScript -->
    <script src="{{ asset('admin/js/modules/customers-common.js') }}"></script>
    
    <!-- Pass current sort state to JavaScript -->
    <script>
        // Set current sort state for JavaScript
        window.currentSort = {
            column: @json(request('sort', 'name')),
            direction: @json(request('direction', 'asc'))
        };
    </script>

@endsection
