@extends('layouts.app')

@section('title', 'Reference User List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Reference Users Management</h1>
                        <small class="text-muted">Manage reference user records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('reference-user-create'))
                            <a href="{{ route('reference_users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                            </a>
                        @endif
                        <a href="{{ route('reference_users.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('reference_users.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-end filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('reference_users.index') }}" class="btn btn-default filter_by_click">
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
                            @forelse($reference_users as $reference_user)
                                <tr>
                                    <td>{{ $reference_user->name }}</td>
                                    <td>{{ $reference_user->email }}</td>
                                    <td>{{ $reference_user->mobile_number }}</td>
                                    <td>
                                        @if ($reference_user->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($reference_user->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('reference-user-edit'))
                                                <a href="{{ route('reference_users.edit', ['reference_user' => $reference_user->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Reference User">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('reference-user-delete'))
                                                @if ($reference_user->status == 0)
                                                    <a href="{{ route('reference_users.status', ['reference_user_id' => $reference_user->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Reference User">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($reference_user->status == 1)
                                                    <a href="{{ route('reference_users.status', ['reference_user_id' => $reference_user->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Reference User">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('reference-user-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Reference User"
                                                    onclick="delete_conf_common('{{ $reference_user->id }}','ReferenceUser', 'Reference User', '{{ route('reference_users.index') }}');">
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
                    
                    <x-pagination-with-info :paginator="$reference_users" />
                </div>
            </div>
        </div>

    </div>
    {{-- @if (!$reference_users->isEmpty())
        @include('reference_users.delete-modal')
    @endif --}}

@endsection

@section('scripts')
    <script></script>
@endsection