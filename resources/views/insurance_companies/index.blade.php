@extends('layouts.app')

@section('title', 'Insurance Companies List')

@section('content')
    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="Insurance Companies Management"
                    subtitle="Manage all insurance company records"
                    addRoute="insurance_companies.create"
                    addPermission="insurance_company-create"
                    exportRoute="insurance_companies.export"
            />
            <div class="card-body">
                <form method="GET" action="{{ route('insurance_companies.index') }}" id="search_form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Insurance Companies</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Company name, email, mobile..." 
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
                                <a href="{{ route('insurance_companies.index') }}" class="btn btn-secondary btn-sm">
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
                            @forelse($insurance_companies as $insurance_company)
                                <tr>
                                    <td>{{ $insurance_company->name }}</td>
                                    <td>{{ $insurance_company->email }}</td>
                                    <td>{{ $insurance_company->mobile_number }}</td>
                                    <td>
                                        @if ($insurance_company->status == 0)
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @elseif ($insurance_company->status == 1)
                                            <span class="badge bg-success text-white">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px; justify-content: flex-start; align-items: center;">
                                            @if (auth()->user()->hasPermissionTo('insurance_company-edit'))
                                                <a href="{{ route('insurance_companies.edit', ['insurance_company' => $insurance_company->id]) }}"
                                                    class="btn btn-primary btn-sm" title="Edit Insurance Company">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('insurance_company-delete'))
                                                @if ($insurance_company->status == 0)
                                                    <a href="{{ route('insurance_companies.status', ['insurance_company_id' => $insurance_company->id, 'status' => 1]) }}"
                                                        class="btn btn-success btn-sm" title="Enable Insurance Company">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @elseif ($insurance_company->status == 1)
                                                    <a href="{{ route('insurance_companies.status', ['insurance_company_id' => $insurance_company->id, 'status' => 0]) }}"
                                                        class="btn btn-warning btn-sm" title="Disable Insurance Company">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermissionTo('insurance_company-delete'))
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete Insurance Company"
                                                    onclick="delete_conf_common('{{ $insurance_company['id'] }}','InsuranceCompany','Insurance Company', '{{ route('insurance_companies.index') }}');">
                                                    <i class="fa fa-trash-alt"></i>
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
                    
                    <x-pagination-with-info :paginator="$insurance_companies" :request="$request" />
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script></script>
@endsection