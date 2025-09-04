@extends('layouts.customer')

@section('title', 'Customer Dashboard')

@section('content')
    <!-- Compact Welcome -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="compact-welcome-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Welcome, {{ $customer->name }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            @if ($isHead)
                                <span class="badge bg-success">Family Head</span>
                            @else
                                <span class="badge bg-info">Family Member</span>
                            @endif
                            @if ($familyGroup)
                                <small class="text-muted">{{ $familyGroup->name }} • {{ $familyMembers->count() }}
                                    members</small>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">{{ now()->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($expiringPolicies->count() > 0)
        <!-- Expiring Policies Alert -->
        <div class="card border-0 shadow-sm mb-4 fade-in" style="border-left: 4px solid var(--warning-color) !important;">
            <div
                class="card-header bg-warning bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="m-0 fw-bold text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Policies Expiring Soon
                    <span class="badge bg-warning text-dark ms-2">{{ $expiringPolicies->count() }}</span>
                </h5>
                <a href="{{ route('customer.policies') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-eye me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                <p class="text-warning mb-3">These policies will expire within the next 30 days:</p>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Policy No</th>
                                <th>Policy Holder</th>
                                <th>Insurance Company</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expiringPolicies as $policy)
                                <tr>
                                    <td>
                                        <strong>{{ $policy->policy_no ?? 'N/A' }}</strong>
                                        @if ($policy->registration_no)
                                            <br><small class="text-muted">{{ $policy->registration_no }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $policy->customer->name }}
                                        @if ($policy->customer_id === $customer->id)
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $daysLeft = now()->diffInDays(
                                                \Carbon\Carbon::parse($policy->expired_date),
                                                false,
                                            );
                                        @endphp
                                        <span class="badge bg-{{ $daysLeft <= 7 ? 'danger' : 'warning' }}">
                                            {{ $daysLeft }} days
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($familyPolicies->count() > 0)
        <!-- Family Insurance Policies -->
        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="m-0 fw-bold text-white">
                    <i class="fas fa-shield-alt me-2"></i>
                    @if ($isHead)
                        Family Insurance Policies
                        <span class="badge bg-light text-primary ms-2">{{ $familyPolicies->count() }} Total</span>
                    @else
                        Your Insurance Policies
                        <span class="badge bg-light text-primary ms-2">{{ $familyPolicies->count() }} Total</span>
                    @endif
                </h5>
                <a href="{{ route('customer.policies') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye me-1"></i>View All Policies
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Policy No</th>
                                <th>Policy Holder</th>
                                <th>Insurance Company</th>
                                <th>Policy Type</th>
                                <th>Premium Type</th>
                                <th>Premium Amount</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($familyPolicies as $policy)
                                <tr class="{{ $policy->customer_id === $customer->id ? 'table-light' : '' }}">
                                    <td>
                                        <strong>{{ $policy->policy_no ?? 'N/A' }}</strong>
                                        @if ($policy->registration_no)
                                            <br><small class="text-muted">{{ $policy->registration_no }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $policy->customer->name }}
                                        @if ($policy->customer_id === $customer->id)
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                    <td>{{ $policy->policyType->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($policy->premiumType)
                                            <span class="badge bg-primary">{{ $policy->premiumType->name }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($policy->final_premium_with_gst)
                                            ₹{{ number_format($policy->final_premium_with_gst, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($policy->expired_date)
                                            {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                            @php
                                                $expiry = \Carbon\Carbon::parse($policy->expired_date);
                                                $daysLeft = $expiry->diffInDays(now(), false);
                                            @endphp
                                            @if ($daysLeft > 0)
                                                <br><small class="text-danger">Expired {{ $daysLeft }} days ago</small>
                                            @elseif($daysLeft > -30)
                                                <br><small class="text-warning">Expires in {{ abs($daysLeft) }}
                                                    days</small>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isExpired = $policy->expired_date
                                                ? \Carbon\Carbon::parse($policy->expired_date)->isPast()
                                                : false;
                                            $isExpiringSoon = false;
                                            if ($policy->expired_date && !$isExpired) {
                                                $daysLeft = now()->diffInDays(
                                                    \Carbon\Carbon::parse($policy->expired_date),
                                                    false,
                                                );
                                                $isExpiringSoon = $daysLeft <= 30;
                                            }
                                        @endphp
                                        @if ($isExpired)
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="badge bg-warning">Expiring Soon</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- No Policies -->
        <div class="card border-left-warning shadow mb-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">No Insurance Policies</div>
                        <div class="text-gray-800">
                            @if ($customer->hasFamily())
                                No insurance policies found for your family.
                            @else
                                You don't have any insurance policies or are not part of a family group.
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($recentQuotations->count() > 0)
        <!-- Recent Quotations -->
        <div class="card border-0 shadow-sm mb-4 fade-in">
            <div class="card-header bg-success py-3 d-flex align-items-center justify-content-between">
                <h5 class="m-0 fw-bold text-white">
                    <i class="fas fa-calculator me-2"></i>Recent Quotations
                    <span class="badge bg-light text-success ms-2">{{ $recentQuotations->count() }}</span>
                </h5>
                <a href="{{ route('customer.quotations') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Quote Ref</th>
                                <th>Vehicle/Policy Holder</th>
                                <th>Vehicle Details</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentQuotations as $quotation)
                                <tr>
                                    <td>
                                        <strong>{{ $quotation->getQuoteReference() }}</strong>
                                    </td>
                                    <td>
                                        {{ $quotation->customer->name }}
                                        @if ($quotation->customer_id === $customer->id)
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($quotation->vehicle_number)
                                            <strong>{{ $quotation->vehicle_number }}</strong><br>
                                            <small class="text-muted">{{ $quotation->make_model_variant }}</small>
                                        @else
                                            <small class="text-muted">General Insurance</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($quotation->status == 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($quotation->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span
                                                class="badge bg-secondary">{{ ucfirst($quotation->status ?? 'Draft') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $quotation->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customer.quotations.detail', $quotation->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if ($quotation->quotationCompanies->count() > 0)
                                                <a href="{{ route('customer.quotations.download', $quotation->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif


@endsection
