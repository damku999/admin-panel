@extends('layouts.app')

@section('title', 'Add-on Covers List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Add-on Covers</h1>
            <div class="row">
                @if (auth()->user()->hasPermissionTo('addon-cover-create'))
                    <div class="col-md-6">
                        <a href="{{ route('addon-covers.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                @endif
                <div class="col-md-6">
                    <a href="{{ route('addon-covers.export') }}" class="btn btn-sm btn-success">
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
                <form action="{{ route('addon-covers.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('addon-covers.index') }}" class="btn btn-default filter_by_click">
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
                                <th width="18%">Name</th>
                                <th width="27%">Description</th>
                                <th width="10%">Order</th>
                                <th width="15%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($addon_covers as $addon_cover)
                                <tr>
                                    <td>{{ $addon_cover->name }}</td>
                                    <td>{{ Str::limit($addon_cover->description, 50) }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $addon_cover->order_no }}</span>
                                    </td>
                                    <td>
                                        @if ($addon_cover->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($addon_cover->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        @if (auth()->user()->hasPermissionTo('addon-cover-delete'))
                                            @if ($addon_cover->status == 0)
                                                <a href="{{ route('addon-covers.status', ['addon_cover_id' => $addon_cover->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($addon_cover->status == 1)
                                                <a href="{{ route('addon-covers.status', ['addon_cover_id' => $addon_cover->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('addon-cover-edit'))
                                            <a href="{{ route('addon-covers.edit', ['addon_cover' => $addon_cover->id]) }}"
                                                class="btn btn-primary m-2">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermissionTo('addon-cover-delete'))
                                            <form action="{{ route('addon-covers.delete', $addon_cover->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger m-2" 
                                                    onclick="if(confirm('Are you sure you want to delete this Add-on Cover?')) { this.closest('form').submit(); }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Record Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $addon_covers->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script></script>
@endsection