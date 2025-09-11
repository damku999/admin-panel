<x-list-page
    title="Fuel Types Management"
    subtitle="Manage all fuel types"
    addRoute="fuel_type.create"
    exportRoute="fuel_type.export"
    searchRoute="fuel_type.index"
    :searchValue="request('search')"
>
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th width="10%">Sr. No.</th>
                    <th width="30%">Name</th>
                    <th width="35%">Description</th>
                    <th width="15%">Status</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fuel_types as $key => $fuel_type)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $fuel_type->name }}</td>
                        <td>{{ $fuel_type->description ?? 'N/A' }}</td>
                        <td>
                            @if ($fuel_type->status == 0)
                                <span class="badge bg-danger text-white">Inactive</span>
                            @elseif ($fuel_type->status == 1)
                                <span class="badge bg-success text-white">Active</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex" style="gap: 6px;">
                                @if (auth()->user()->hasPermissionTo('fuel-type-edit'))
                                    <a href="{{ route('fuel_type.edit', ['fuel_type' => $fuel_type->id]) }}"
                                        class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                @endif

                                @if (auth()->user()->hasPermissionTo('fuel-type-delete'))
                                    @if ($fuel_type->status == 0)
                                        <a href="{{ route('fuel_type.status', ['fuel_type_id' => $fuel_type->id, 'status' => 1]) }}"
                                            class="btn btn-success btn-sm" title="Enable">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    @elseif ($fuel_type->status == 1)
                                        <a href="{{ route('fuel_type.status', ['fuel_type_id' => $fuel_type->id, 'status' => 0]) }}"
                                            class="btn btn-warning btn-sm" title="Disable">
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
    
    @if(method_exists($fuel_types, 'links'))
        {{ $fuel_types->links() }}
    @endif
</x-list-page>