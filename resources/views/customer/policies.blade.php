@extends('layouts.customer')

@section('title', 'My Policies')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if($isHead)
                Family Insurance Policies
            @else
                Your Insurance Policies
            @endif
        </h1>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Policy Status Tabs -->
    <ul class="nav nav-tabs mb-4" id="policyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="true">
                <i class="fas fa-shield-alt text-success"></i>
                Active Policies ({{ $activePolicies->count() }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="expired-tab" data-toggle="tab" href="#expired" role="tab" aria-controls="expired" aria-selected="false">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                Expired Policies ({{ $expiredPolicies->count() }})
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="policyTabContent">
        <!-- Active Policies Tab -->
        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
            @if($activePolicies->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Active Insurance Policies</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Policy Details</th>
                                    <th>Policy Holder</th>
                                    <th>Insurance Company</th>
                                    <th>Policy Type</th>
                                    <th>Premium Amount</th>
                                    <th>Validity Period</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activePolicies as $policy)
                                <tr class="{{ $policy->customer_id === $customer->id ? 'table-light' : '' }}">
                                    <td>
                                        <strong>{{ $policy->policy_no ?? 'N/A' }}</strong>
                                        @if($policy->registration_no)
                                            <br><small class="text-muted">{{ $policy->registration_no }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $policy->customer->name }}
                                        @if($policy->customer_id === $customer->id)
                                            <span class="badge badge-info ml-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                    <td>{{ $policy->policyType->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($policy->final_premium_with_gst)
                                            ₹{{ number_format($policy->final_premium_with_gst, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <strong>Start:</strong> {{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}<br>
                                        <strong>Expiry:</strong> 
                                        @if($policy->expired_date)
                                            {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                            @php
                                                $expiry = \Carbon\Carbon::parse($policy->expired_date);
                                                $daysLeft = now()->diffInDays($expiry, false);
                                            @endphp
                                            @if($daysLeft <= 30 && $daysLeft > 0)
                                                <br><small class="text-warning"><strong>Expires in {{ $daysLeft }} days</strong></small>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                        @if($policy->expired_date)
                                            @php
                                                $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                            @endphp
                                            @if($daysLeft <= 30 && $daysLeft > 0)
                                                <br><span class="badge badge-warning mt-1">Expiring Soon</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($policy->policy_document_path)
                                        <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-success btn-sm mt-1">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h5>No Active Policies</h5>
                        <p class="text-muted">You don't have any active insurance policies at the moment.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Expired Policies Tab -->
        <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
            @if($expiredPolicies->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Expired Insurance Policies</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Policy Details</th>
                                    <th>Policy Holder</th>
                                    <th>Insurance Company</th>
                                    <th>Policy Type</th>
                                    <th>Premium Amount</th>
                                    <th>Validity Period</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiredPolicies as $policy)
                                <tr class="{{ $policy->customer_id === $customer->id ? 'table-light' : '' }}">
                                    <td>
                                        <strong>{{ $policy->policy_no ?? 'N/A' }}</strong>
                                        @if($policy->registration_no)
                                            <br><small class="text-muted">{{ $policy->registration_no }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $policy->customer->name }}
                                        @if($policy->customer_id === $customer->id)
                                            <span class="badge badge-info ml-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                    <td>{{ $policy->policyType->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($policy->final_premium_with_gst)
                                            ₹{{ number_format($policy->final_premium_with_gst, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <strong>Start:</strong> {{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}<br>
                                        <strong>Expiry:</strong> 
                                        @if($policy->expired_date)
                                            {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                            @php
                                                $daysExpired = \Carbon\Carbon::parse($policy->expired_date)->diffInDays(now());
                                            @endphp
                                            <br><small class="text-danger"><strong>Expired {{ $daysExpired }} days ago</strong></small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">Expired</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($policy->policy_document_path)
                                        <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-success btn-sm mt-1">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-left-danger shadow">
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5>No Expired Policies</h5>
                        <p class="text-muted">You don't have any expired insurance policies.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection