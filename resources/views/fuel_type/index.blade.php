@extends('layouts.app')

@section('title', 'Fuel Type List')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Fuel Types Management"
                    subtitle="Manage all fuel type records"
                    addRoute="fuel_type.create"
                    addPermission="fuel-type-create"
                    exportRoute="fuel_type.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('fuel_type.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Fuel Types</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Fuel type name..." 
                                       value="{{ request('search') }}">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('fuel_type.index') }}" class="btn btn-secondary btn-sm">
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
                                <th width="20%">Name</th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fuel_type as $fuel_type_detail)
                                <tr>
                                    <td>{{ $fuel_type_detail->name }}</td>
                                    <td>
                                        @if ($fuel_type_detail->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($fuel_type_detail->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('fuel-type-delete'))
                                                <a href="{{ route('fuel_type.edit', ['fuel_type' => $fuel_type_detail->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Fuel Type">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('fuel-type-delete'))
                                                @if ($fuel_type_detail->status == 0)
                                                    <a href="{{ route('fuel_type.status', ['fuel_type_id' => $fuel_type_detail->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Fuel Type">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($fuel_type_detail->status == 1)
                                                    <a href="{{ route('fuel_type.status', ['fuel_type_id' => $fuel_type_detail->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Fuel Type">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('fuel-type-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Fuel Type"
                                                    onclick="delete_conf_common('{{ $fuel_type_detail->id }}','FuelType', 'Fuel Type', '{{ route('fuel_type.index') }}');">
                                                    <i class="fas fa-trash"></i>
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
                    
                    <x-pagination-with-info :paginator="$fuel_type" :request="$request" />
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script></script>
@endsection