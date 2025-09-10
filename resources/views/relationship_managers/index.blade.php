@extends('layouts.app')

@section('title', 'Relationship Managers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Relationship Managers Management</h1>
                        <small class="text-muted">Manage relationship manager records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('relationship_manager-create'))
                            <a href="{{ route('relationship_managers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <a href="{{ route('relationship_managers.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('relationship_managers.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-end filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('relationship_managers.index') }}" class="btn btn-default filter_by_click">
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
                                <th width="25%">Email</th>
                                <th width="15%">Mobile</th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($relationship_managers as $relationship_manager)
                                <tr>
                                    <td>{{ $relationship_manager->name }}</td>
                                    <td>{{ $relationship_manager->email }}</td>
                                    <td>{{ $relationship_manager->mobile_number }}</td>
                                    <td>
                                        @if ($relationship_manager->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($relationship_manager->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('relationship_manager-edit'))
                                                <a href="{{ route('relationship_managers.edit', ['relationship_manager' => $relationship_manager->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Relationship Manager">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('relationship_manager-delete'))
                                                @if ($relationship_manager->status == 0)
                                                    <a href="{{ route('relationship_managers.status', ['relationship_manager_id' => $relationship_manager->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Relationship Manager">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($relationship_manager->status == 1)
                                                    <a href="{{ route('relationship_managers.status', ['relationship_manager_id' => $relationship_manager->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Relationship Manager">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('relationship_manager-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Relationship Manager"
                                                    onclick="delete_conf_common('{{ $relationship_manager['id'] }}','RelationshipManager','Relationship Manager', '{{ route('relationship_managers.index') }}');">
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
                    
                    <x-pagination-with-info :paginator="$relationship_managers" />
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script></script>
@endsection