@extends('layouts.app')

@section('title', 'Premium Type List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Premium Types Management"
                    subtitle="Manage insurance premium types"
                    addRoute="premium_type.create"
                    addPermission="premium-type-create"
                    exportRoute="premium_type.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('premium_type.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Premium Types</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Premium type name..." 
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
                                <a href="{{ route('premium_type.index') }}" class="btn btn-secondary btn-sm">
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
                                <th width="15%">Is it for Vehicle</th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($premium_type as $premium_type_detail)
                                <tr>
                                    <td>{{ $premium_type_detail->name }}</td>
                                    <td>
                                        @if ($premium_type_detail->is_vehicle == 0)
                                            <span class="badge bg-danger text-white">No</span>
                                        @elseif ($premium_type_detail->is_vehicle == 1)
                                            <span class="badge bg-success text-white">Yes</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($premium_type_detail->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($premium_type_detail->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('premium-type-edit'))
                                                <a href="{{ route('premium_type.edit', ['premium_type' => $premium_type_detail->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Premium Type">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('premium-type-delete'))
                                                @if ($premium_type_detail->status == 0)
                                                    <a href="{{ route('premium_type.status', ['premium_type_id' => $premium_type_detail->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Premium Type">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($premium_type_detail->status == 1)
                                                    <a href="{{ route('premium_type.status', ['premium_type_id' => $premium_type_detail->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Premium Type">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('premium-type-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Premium Type"
                                                    onclick="delete_conf_common('{{ $premium_type_detail->id }}','PremiumType', 'Premium Type', '{{ route('premium_type.index') }}');">
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
                    
                    <x-pagination-with-info :paginator="$premium_type" />
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script></script>
@endsection