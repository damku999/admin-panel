@extends('layouts.app')

@section('title', 'Family Groups Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Family Groups Management</h1>
        <div class="d-sm-flex">
            <a href="{{ route('family_groups.export') }}" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-download fa-sm text-white-50"></i> Export CSV
            </a>
            <a href="{{ route('family_groups.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Create Family Group
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('family_groups.index') }}" id="search_form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search Family Groups</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Family name, head name, or email..." 
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
                            <a href="{{ route('family_groups.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Family Groups Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Family Groups ({{ $familyGroups->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            @if($familyGroups->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Family Name</th>
                            <th>Family Head</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($familyGroups as $familyGroup)
                        <tr>
                            <td>{{ $familyGroup->id }}</td>
                            <td>
                                <strong>{{ $familyGroup->name }}</strong>
                            </td>
                            <td>
                                @if($familyGroup->familyHead)
                                    <div>
                                        <strong>{{ $familyGroup->familyHead->name }}</strong><br>
                                        <small class="text-muted">{{ $familyGroup->familyHead->email }}</small>
                                    </div>
                                @else
                                    <span class="text-danger">No Head Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $familyGroup->familyMembers->count() }} Members</span>
                                @if($familyGroup->familyMembers->count() > 0)
                                    <div class="mt-1">
                                        @foreach($familyGroup->familyMembers->take(3) as $member)
                                            <small class="text-muted d-block">
                                                {{ $member->customer->name }}
                                                @if($member->is_head)
                                                    <span class="badge badge-success badge-sm">Head</span>
                                                @endif
                                            </small>
                                        @endforeach
                                        @if($familyGroup->familyMembers->count() > 3)
                                            <small class="text-muted">... and {{ $familyGroup->familyMembers->count() - 3 }} more</small>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($familyGroup->status)
                                    <a href="{{ route('family_groups.status', [$familyGroup->id, 0]) }}" 
                                       class="badge badge-success">Active</a>
                                @else
                                    <a href="{{ route('family_groups.status', [$familyGroup->id, 1]) }}" 
                                       class="badge badge-danger">Inactive</a>
                                @endif
                            </td>
                            <td>
                                <small>{{ $familyGroup->created_at->format('d M Y') }}<br>
                                {{ $familyGroup->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('family_groups.show', $familyGroup) }}" 
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('family_groups.edit', $familyGroup) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="javascript:void(0);" title="Delete"
                                       onclick="delete_conf_common('{{ $familyGroup->id }}','FamilyGroup', 'Family Group: {{ $familyGroup->name }}', '{{ route('family_groups.index') }}');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $familyGroups->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">No Family Groups Found</h5>
                <p class="text-gray-500">Start by creating your first family group.</p>
                <a href="{{ route('family_groups.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Family Group
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection