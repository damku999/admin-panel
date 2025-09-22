@extends('layouts.app')

@section('title', 'Users List')

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTables Card -->
    <div class="card shadow mb-4">
        <!-- Header with Components -->
        <x-list-header 
            title="Users Management"
            subtitle="Manage all system users"
            addRoute="users.create"
            addPermission="user-create"
            exportRoute="users.export"
        />

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="18%">Name</th>
                            <th width="22%">Email</th>
                            <th width="14%">Mobile</th>
                            <th width="14%">Role</th>
                            <th width="12%">Status</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile_number }}</td>
                                <td>{{ $user->roles ? $user->roles->pluck('name')->first() : 'N/A' }}</td>
                                <td>
                                    @if ($user->status == 0)
                                        <span class="badge bg-danger text-white">Inactive</span>
                                    @elseif ($user->status == 1)
                                        <span class="badge bg-success text-white">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                        @if (auth()->user()->hasPermissionTo('user-edit'))
                                            <a href="{{ route('users.edit', ['user' => $user->id]) }}"
                                                class="btn btn-primary btn-sm" title="Edit User">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('user-delete'))
                                            @if ($user->status == 0)
                                                <a href="{{ route('users.status', ['user_id' => $user->id, 'status' => 1]) }}"
                                                    class="btn btn-success btn-sm" title="Enable User">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($user->status == 1)
                                                <a href="{{ route('users.status', ['user_id' => $user->id, 'status' => 0]) }}"
                                                    class="btn btn-warning btn-sm" title="Disable User">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection