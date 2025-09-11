@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                title="Permissions Management"
                subtitle="Manage system permissions"
                addRoute="permissions.create"
                addPermission="permission-create"
            />
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
