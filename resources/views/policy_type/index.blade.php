@extends('layouts.app')

@section('title', 'Policy Type List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Policy Types Management</h1>
                        <small class="text-muted">Manage all policy type records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('policy-type-create'))
                            <a href="{{ route('policy_type.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <a href="{{ route('policy_type.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('policy_type.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('policy_type.index') }}" class="btn btn-default filter_by_click">
                            <i class="fas fa-redo"></i>
                        </a>
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
                            @forelse($policy_type as $policy_type_detail)
                                <tr>
                                    <td>{{ $policy_type_detail->name }}</td>
                                    <td>
                                        @if ($policy_type_detail->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($policy_type_detail->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('policy-type-edit'))
                                                <a href="{{ route('policy_type.edit', ['policy_type' => $policy_type_detail->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Policy Type">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('policy-type-delete'))
                                                @if ($policy_type_detail->status == 0)
                                                    <a href="{{ route('policy_type.status', ['policy_type_id' => $policy_type_detail->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Policy Type">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($policy_type_detail->status == 1)
                                                    <a href="{{ route('policy_type.status', ['policy_type_id' => $policy_type_detail->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Policy Type">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('policy-type-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Policy Type"
                                                    onclick="delete_conf_common('{{ $policy_type_detail->id }}','PolicyType', 'Policy Type', '{{ route('policy_type.index') }}');">
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
                    
                    <x-pagination-with-info :paginator="$policy_type" />
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script></script>
@endsection
