@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                title="Roles Management"
                subtitle="Manage user roles and permissions"
                addRoute="roles.create"
                addPermission="role-create"
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
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('role-edit'))
                                                <a href="{{ route('roles.edit', ['role' => $role->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Role">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif
                                            @if (auth()->user()->hasPermissionTo('role-delete'))
                                                <form method="POST" action="{{ route('roles.destroy', ['role' => $role->id]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit" title="Delete Role">
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
                    
                    <x-pagination-with-info :paginator="$roles" />
                </div>
            </div>
        </div>

    </div>


@endsection
