@extends('layouts.app')

@section('title', 'Reference User List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Reference Users Management"
                    subtitle="Manage reference user records"
                    addRoute="reference_users.create"
                    addPermission="reference-user-create"
                    exportRoute="reference_users.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('reference_users.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Reference Users</label>
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
                                <a href="{{ route('reference_users.index') }}" class="btn btn-secondary btn-sm">
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