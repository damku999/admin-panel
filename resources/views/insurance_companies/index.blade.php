@extends('layouts.app')

@section('title', 'Insurance Companies List')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Insurance Companies</h1>
            <div class="row">
                @if (auth()->user()->hasPermissionTo('insurance_company-create'))
                    <div class="col-md-6">
                        <a href="{{ route('insurance_companies.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                @endif
                <div class="col-md-6">
                    <a href="{{ route('insurance_companies.export') }}" class="btn btn-sm btn-success">
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
                <form action="{{ route('insurance_companies.index') }}" method="GET" role="search">
                    <div class="input-group-append">
                        <input type="text" placeholder="Search" name="search"
                            class="form-control float-right filter_by_key" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-default filter_by_click">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('insurance_companies.index') }}" class="btn btn-default filter_by_click">
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
                            @forelse($insurance_companies as $insurance_company)
                                <tr>
                                    <td>{{ $insurance_company->name }}</td>
                                    <td>{{ $insurance_company->email }}</td>
                                    <td>{{ $insurance_company->mobile_number }}</td>
                                    <td>
                                        @if ($insurance_company->status == 0)
                                            <span class="badge badge-danger">Inactive</span>
                                        @elseif ($insurance_company->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td style="display: flex">
                                        @if (auth()->user()->hasPermissionTo('insurance_company-delete'))
                                            @if ($insurance_company->status == 0)
                                                <a href="{{ route('insurance_companies.status', ['insurance_company_id' => $insurance_company->id, 'status' => 1]) }}"
                                                    class="btn btn-success m-2">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @elseif ($insurance_company->status == 1)
                                                <a href="{{ route('insurance_companies.status', ['insurance_company_id' => $insurance_company->id, 'status' => 0]) }}"
                                                    class="btn btn-danger m-2">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('insurance_company-edit'))
                                            <a href="{{ route('insurance_companies.edit', ['insurance_company' => $insurance_company->id]) }}"
                                                class="btn btn-primary m-2">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasPermissionTo('insurance_company-delete'))
                                            <a class="btn btn-danger m-2" href="javascript:void(0);"
                                                onclick="delete_conf_common('{{ $insurance_company['id'] }}','InsuranceCompany','Insurance Company', '{{ route('insurance_companies.index') }}');"><i
                                                    class="fa fa-trash-alt "></i></a>
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

                    {{ $insurance_companies->links() }}
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script></script>
@endsection
