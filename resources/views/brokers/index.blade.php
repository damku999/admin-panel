@extends('layouts.app')

@section('title', 'Brokers List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Brokers</h1>
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('brokers.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('brokers.export') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-check"></i> Export To Excel
                    </a>
                </div>

            </div>

        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                {{-- <h6 class="m-0 font-weight-bold text-primary"></h6> --}}
                <form action="{{ route('brokers.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}">
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
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($broker->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        @if (auth()->user()->hasPermissionTo('broker-delete'))
                                            @if ($broker->status == 0)
                                                <a href="{{ route('brokers.status', ['broker_id' => $broker->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($broker->status == 1)
                                                <a href="{{ route('brokers.status', ['broker_id' => $broker->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('broker-edit'))
                                            <a href="{{ route('brokers.edit', ['broker' => $broker->id]) }}"
                                                class="btn btn-primary m-2">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('broker-delete'))
                                            <a class="btn btn-danger m-2" href="javascript:void(0);"
                                                onclick="delete_conf_common('{{ $broker->id }}','Broker', 'Broker', '{{ route('brokers.index') }}');"><i
                                                    class="fas fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $brokers->links() }}
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
