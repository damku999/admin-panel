@extends('layouts.customer')

@section('title', 'My Claims')

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- Page Header -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">My Claims</h6>
                    <small class="text-muted">View and track your insurance claims</small>
                </div>
                <div>
                    @if($claims->count() > 0)
                        <span class="badge badge-info badge-pill">{{ $claims->total() }} Total Claims</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($claims->count() > 0)
        <!-- Claims List -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Claim Number</th>
                                <th>Insurance Type</th>
                                <th>Customer</th>
                                <th>Policy Number</th>
                                <th>Vehicle/Registration</th>
                                <th>Current Status</th>
                                <th>Incident Date</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claims as $claim)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $claim->claim_number }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $claim->insurance_type == 'Health' ? 'success' : 'info' }}">
                                            <i class="fas fa-{{ $claim->insurance_type == 'Health' ? 'heartbeat' : 'car' }} me-1"></i>
                                            {{ $claim->insurance_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $claim->customer->name }}</strong>
                                            @if($claim->customer_id !== auth('customer')->id())
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-users me-1"></i>Family Member
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-monospace">{{ $claim->customerInsurance->policy_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        {{ $claim->customerInsurance->registration_no ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($claim->currentStage)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $claim->currentStage->stage_name }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">No Stage</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $claim->incident_date ? $claim->incident_date->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $claim->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.claims.detail', $claim) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Claim Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($claims->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $claims->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- No Claims State -->
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No Claims Found</h4>
                <p class="text-muted mb-4">
                    You don't have any insurance claims yet.
                    @if(auth('customer')->user()->isFamilyHead())
                        This includes claims for all family members.
                    @endif
                </p>
                <div class="mt-4">
                    <a href="{{ route('customer.policies') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-contract me-2"></i>View My Policies
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Claims Overview -->
    @if($claims->count() > 0)
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                    Total Claims
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-white">
                                    {{ $claims->total() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-white-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                    Health Claims
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-white">
                                    {{ $claims->where('insurance_type', 'Health')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-heartbeat fa-2x text-white-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-1">
                                    Vehicle Claims
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-white">
                                    {{ $claims->where('insurance_type', 'Vehicle')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-car fa-2x text-white-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fc;
        border-top: none;
        font-weight: 600;
        font-size: 0.85rem;
        color: #5a5c69;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    .font-monospace {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }

    .text-white-25 {
        color: rgba(255, 255, 255, 0.25) !important;
    }

    .text-white-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            white-space: nowrap;
        }
    }
</style>
@endpush