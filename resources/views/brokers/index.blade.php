@extends('layouts.app')

@section('title', 'Brokers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                    <div class="mb-2 mb-md-0">
                        <h1 class="h4 mb-0 text-primary font-weight-bold">Brokers Management</h1>
                        <small class="text-muted">Manage all broker records</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('brokers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add New</span>
                        </a>
                        <a href="{{ route('brokers.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> <span class="d-none d-sm-inline">Export</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('brokers.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-end filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('brokers.index') }}" class="btn btn-default filter_by_click">
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
                            @forelse($brokers as $broker)
                                <tr>
                                    <td>{{ $broker->name }}</td>
                                    <td>{{ $broker->email }}</td>
                                    <td>{{ $broker->mobile_number }}</td>
                                    <td>
                                        @if ($broker->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($broker->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('broker-edit'))
                                                <a href="{{ route('brokers.edit', ['broker' => $broker->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Broker">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('broker-delete'))
                                                @if ($broker->status == 0)
                                                    <a href="{{ route('brokers.status', ['broker_id' => $broker->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Broker">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($broker->status == 1)
                                                    <a href="{{ route('brokers.status', ['broker_id' => $broker->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Broker">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('broker-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Broker"
                                                    onclick="delete_conf_common('{{ $broker->id }}','Broker', 'Broker', '{{ route('brokers.index') }}');">
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
                    
                    <x-pagination-with-info :paginator="$brokers" :request="$request" />
                </div>
            </div>
        </div>

    </div>
    {{-- @if (!$brokers->isEmpty())
        @include('brokers.delete-modal')
    @endif --}}

@endsection

@section('scripts')
    <script></script>
@endsection