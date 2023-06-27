@extends('layouts.app')

@section('title', 'Premium Type List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Premium Type</h1>
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('premium_type.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('premium_type.export') }}" class="btn btn-sm btn-success">
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
                <form action="{{ route('premium_type.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('premium_type.index') }}" class="btn btn-default filter_by_click">
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
                                <th width="15%">Is it for Vehicle</th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($premium_type as $premium_type_detail)
                                <tr>
                                    <td>{{ $premium_type_detail->name }}</td>
                                    <td>
                                        @if ($premium_type_detail->is_vehicle == 0)
                                            <span class="badge badge-danger">No</span>
                                        @elseif ($premium_type_detail->is_vehicle == 1)
                                            <span class="badge badge-success">Yes</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($premium_type_detail->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($premium_type_detail->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        @if ($premium_type_detail->status == 0)
                                            <a href="{{ route('premium_type.status', ['premium_type_id' => $premium_type_detail->id, 'status' => 1]) }}"
                                                class="btn btn-success m-2">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        @elseif ($premium_type_detail->status == 1)
                                            <a href="{{ route('premium_type.status', ['premium_type_id' => $premium_type_detail->id, 'status' => 0]) }}"
                                                class="btn btn-danger m-2">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('premium_type.edit', ['premium_type' => $premium_type_detail->id]) }}"
                                            class="btn btn-primary m-2">
                                            <i class="fa fa-pen"></i>
                                        </a>

                                        <a class="btn btn-danger m-2" href="javascript:void(0);"
                                            onclick="delete_conf_common('{{ $premium_type_detail->id }}','PremiumType', 'Premium Type', '{{ route('premium_type.index') }}');"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $premium_type->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script></script>
@endsection
