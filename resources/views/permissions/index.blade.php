@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Permissions Management</h1>
                        <small class="text-muted">Manage system permissions</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('permission-create'))
                            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="40%">Name</th>
                                <th width="40%">Guard Name</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->guard_name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('permission-edit'))
                                                <a href="{{ route('permissions.edit', ['permission' => $permission->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Permission">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif
                                            @if (auth()->user()->hasPermissionTo('permission-delete'))
                                                <form method="POST" action="{{ route('permissions.destroy', ['permission' => $permission->id]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit" title="Delete Permission">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <x-pagination-with-info :paginator="$permissions" />
                </div>
            </div>
        </div>

    </div>


@endsection
