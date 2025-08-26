@extends('layouts.customer')

@section('title', 'Customer Dashboard')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Family Insurance Dashboard</h1>
    </div>

    <!-- Compact Customer & Family Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow">
                <div class="card-body py-3">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-sm-0">
                            <div class="mr-3">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <div class="h5 mb-1 font-weight-bold text-gray-800">
                                    Welcome, {{ $customer->name }}
                                    @if ($isHead)
                                        <span class="badge badge-success ml-2 d-block d-sm-inline">Family Head</span>
                                    @else
                                        <span class="badge badge-info ml-2 d-block d-sm-inline">Family Member</span>
                                    @endif
                                </div>
                                <div class="text-gray-600">
                                    <small class="d-block d-sm-inline">
                                        <i class="fas fa-envelope mr-1"></i>{{ $customer->email }}
                                    </small>
                                    <small class="d-block d-sm-inline">
                                        <span class="d-none d-sm-inline"> | </span><i class="fas fa-phone mr-1"></i>{{ $customer->mobile_number }}
                                    </small>
                                    @if ($familyGroup)
                                        <small class="d-block d-sm-inline">
                                            <span class="d-none d-sm-inline"> | </span><i class="fas fa-home mr-1"></i>{{ $familyGroup->name }}
                                        </small>
                                        <small class="d-block d-sm-inline">
                                            <span class="d-none d-sm-inline"> | </span><i class="fas fa-users mr-1"></i>{{ $familyMembers->count() }} Members:
                                            @foreach ($familyMembers as $member)
                                                {{ $member->customer->name }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right text-sm-right text-center">
                            <div class="text-xs text-uppercase text-muted mb-1">Dashboard</div>
                            <div class="h6 mb-0 text-primary">{{ now()->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if ($expiringPolicies->count() > 0)
        <!-- Expiring Policies Alert -->
        <div class="card border-left-warning shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Policies Expiring Soon
                    ({{ $expiringPolicies->count() }})
                </h6>
                <a href="{{ route('customer.policies') }}" class="btn btn-warning btn-sm">View All</a>
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
                                            <span class="badge badge-info ml-1">You</span>
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
                                        <span class="badge badge-{{ $daysLeft <= 7 ? 'danger' : 'warning' }}">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    @if ($isHead)
                        Family Insurance Policies ({{ $familyPolicies->count() }} Total)
                    @else
                        Your Insurance Policies ({{ $familyPolicies->count() }} Total)
                    @endif
                </h6>
                <a href="{{ route('customer.policies') }}" class="btn btn-primary btn-sm">View All Policies</a>
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
                                            <span class="badge badge-info ml-1">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                    <td>{{ $policy->policyType->name ?? 'N/A' }}</td>
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
                                            <span class="badge badge-danger">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="badge badge-warning">Expiring Soon</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
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
                    <div class="col mr-2">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-calculator mr-2"></i>Recent Quotations ({{ $recentQuotations->count() }})
                </h6>
                <a href="{{ route('customer.quotations') }}" class="btn btn-success btn-sm">View All</a>
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
                                            <span class="badge badge-info ml-1">You</span>
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
                                            <span class="badge badge-success">Sent</span>
                                        @elseif($quotation->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span
                                                class="badge badge-secondary">{{ ucfirst($quotation->status ?? 'Draft') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $quotation->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customer.quotations.detail', $quotation->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($quotation->quotationCompanies->count() > 0)
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-6 mb-2">
                            <a href="{{ route('customer.change-password') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-key"></i> <span class="d-none d-sm-inline">Change Password</span><span class="d-sm-none">Password</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="{{ route('customer.policies') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-file-alt"></i> <span class="d-none d-sm-inline">View Policies</span><span class="d-sm-none">Policies</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="{{ route('customer.quotations') }}" class="btn btn-success btn-block">
                                <i class="fas fa-calculator"></i> <span class="d-none d-sm-inline">View Quotations</span><span class="d-sm-none">Quotations</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <a href="{{ route('customer.profile') }}" class="btn btn-info btn-block">
                                <i class="fas fa-user-edit"></i> <span class="d-none d-sm-inline">Profile</span><span class="d-sm-none">Profile</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-12 mb-2">
                            <a href="#helpModal" class="btn btn-secondary btn-block" data-toggle="modal">
                                <i class="fas fa-question-circle"></i> <span class="d-none d-sm-inline">Help & Support</span><span class="d-sm-none">Help</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help & Support Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">
                        <i class="fas fa-question-circle text-info mr-2"></i>
                        Help & Support
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle text-primary mr-1"></i> How to Use This Portal</h6>
                            <ul class="small">
                                <li><strong>Dashboard:</strong> View your family's insurance policies and member information
                                </li>
                                <li><strong>Family Policies:</strong>
                                    @if ($isHead)
                                        As family head, you can view all family members' policies
                                    @else
                                        You can view all policies in your family group
                                    @endif
                                </li>
                                <li><strong>Profile:</strong> View and check your personal information</li>
                                <li><strong>Security:</strong> Change your password regularly for security</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-headset text-success mr-1"></i> Contact Support</h6>
                            <p class="small">If you need assistance, please contact our support team:</p>
                            <div class="contact-info small">
                                <p><i class="fas fa-phone text-primary mr-2"></i><strong>Phone:</strong> <a
                                        href="tel:+919727793123">+91 97277 93123</a></p>
                                <p><i class="fas fa-envelope text-primary mr-2"></i><strong>Email:</strong>
                                    <a href="mailto:webmonks.in">darshan@webmonks.in</a>
                                </p>
                                <p><i class="fas fa-clock text-primary mr-2"></i><strong>Hours:</strong> Mon-Fri, 9:00 AM -
                                    6:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
