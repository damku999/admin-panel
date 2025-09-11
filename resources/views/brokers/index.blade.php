@extends('layouts.app')

@section('title', 'Brokers List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Brokers Management"
                    subtitle="Manage all broker records"
                    addRoute="brokers.create"
                    exportRoute="brokers.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('brokers.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Brokers</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name, email, mobile number..." 
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
                                <a href="{{ route('brokers.index') }}" class="btn btn-secondary btn-sm">
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