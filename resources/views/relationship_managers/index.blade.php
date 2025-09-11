@extends('layouts.app')

@section('title', 'Relationship Managers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                title="Relationship Managers Management"
                subtitle="Manage relationship manager records"
                addRoute="relationship_managers.create"
                addPermission="relationship_manager-create"
                exportRoute="relationship_managers.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('relationship_managers.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Relationship Managers</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name, email, mobile..." 
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
                                <a href="{{ route('relationship_managers.index') }}" class="btn btn-secondary btn-sm">
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