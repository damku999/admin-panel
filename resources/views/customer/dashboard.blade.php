@extends('layouts.customer')

@section('title', 'Customer Dashboard')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Family Insurance Dashboard</h1>
    </div>

    <!-- Customer Info Row -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Welcome</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customer->name }}</div>
                            <div class="text-gray-600">
                                <small>
                                    Email: {{ $customer->email }} | 
                                    Mobile: {{ $customer->mobile_number }} |
                                    @if($isHead)
                                        <span class="badge badge-success">Family Head</span>
                                    @else
                                        <span class="badge badge-info">Family Member</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($familyGroup)
    <!-- Family Info Row -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Family Group</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $familyGroup->name }}</div>
                            <div class="text-gray-600">
                                <small>Head: {{ $familyGroup->familyHead->name }}</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Family Members</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $familyMembers->count() }} Members</div>
                            <div class="text-gray-600">
                                <small>
                                    @foreach($familyMembers as $member)
                                        {{ $member->customer->name }}@if(!$loop->last), @endif
                                    @endforeach
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Members List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Family Members</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Relationship</th>
                            <th>Date of Birth</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($familyMembers as $member)
                        <tr class="{{ $member->customer->id === $customer->id ? 'table-primary' : '' }}">
                            <td>
                                {{ $member->customer->name }}
                                @if($member->is_head)
                                    <span class="badge badge-success ml-1">Head</span>
                                @endif
                                @if($member->customer->id === $customer->id)
                                    <span class="badge badge-info ml-1">You</span>
                                @endif
                            </td>
                            <td>{{ $member->customer->email }}</td>
                            <td>{{ $member->customer->mobile_number ?? 'N/A' }}</td>
                            <td>{{ $member->relationship ?? 'N/A' }}</td>
                            <td>{{ $member->customer->date_of_birth ? $member->customer->date_of_birth->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @if($member->customer->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
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
    <!-- No Family Group -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">No Family Group</div>
                            <div class="text-gray-800">You are not currently assigned to any family group.</div>
                            <div class="text-gray-600">
                                <small>Contact support to be added to a family group.</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($expiringPolicies->count() > 0)
    <!-- Expiring Policies Alert -->
    <div class="card border-left-warning shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>Policies Expiring Soon ({{ $expiringPolicies->count() }})
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
                        @foreach($expiringPolicies as $policy)
                        <tr>
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
                            <td>{{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}</td>
                            <td>
                                @php
                                    $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                @endphp
                                <span class="badge badge-{{ $daysLeft <= 7 ? 'danger' : 'warning' }}">
                                    {{ $daysLeft }} days
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-info btn-sm">
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

    @if($familyPolicies->count() > 0)
    <!-- Family Insurance Policies -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                @if($isHead)
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
                        @foreach($familyPolicies as $policy)
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
                            <td>{{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @if($policy->expired_date)
                                    {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                    @php
                                        $expiry = \Carbon\Carbon::parse($policy->expired_date);
                                        $daysLeft = $expiry->diffInDays(now(), false);
                                    @endphp
                                    @if($daysLeft > 0)
                                        <br><small class="text-danger">Expired {{ $daysLeft }} days ago</small>
                                    @elseif($daysLeft > -30)
                                        <br><small class="text-warning">Expires in {{ abs($daysLeft) }} days</small>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @php
                                    $isExpired = $policy->expired_date ? \Carbon\Carbon::parse($policy->expired_date)->isPast() : false;
                                    $isExpiringSoon = false;
                                    if ($policy->expired_date && !$isExpired) {
                                        $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                        $isExpiringSoon = $daysLeft <= 30;
                                    }
                                @endphp
                                @if($isExpired)
                                    <span class="badge badge-danger">Expired</span>
                                @elseif($isExpiringSoon)
                                    <span class="badge badge-warning">Expiring Soon</span>
                                @else
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-info btn-sm">
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
                        @if($customer->hasFamily())
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('customer.change-password') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('customer.policies') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-file-alt"></i> View Insurance Policies
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('customer.profile') }}" class="btn btn-info btn-block">
                                <i class="fas fa-user-edit"></i> View Profile
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#helpModal" class="btn btn-secondary btn-block" data-toggle="modal">
                                <i class="fas fa-question-circle"></i> Help & Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help & Support Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
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
                                <li><strong>Dashboard:</strong> View your family's insurance policies and member information</li>
                                <li><strong>Family Policies:</strong> 
                                    @if($isHead)
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
                                <p><i class="fas fa-phone text-primary mr-2"></i><strong>Phone:</strong> +91 XXX-XXX-XXXX</p>
                                <p><i class="fas fa-envelope text-primary mr-2"></i><strong>Email:</strong> support@company.com</p>
                                <p><i class="fas fa-clock text-primary mr-2"></i><strong>Hours:</strong> Mon-Fri, 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="fas fa-exclamation-triangle text-warning mr-1"></i> Important Notes</h6>
                            <div class="alert alert-info small">
                                <ul class="mb-0">
                                    <li><strong>Read-Only Access:</strong> You can view insurance policies but cannot make changes</li>
                                    <li><strong>Policy Updates:</strong> Contact your insurance agent or admin for policy modifications</li>
                                    <li><strong>Email Verification:</strong> Please verify your email address for account security</li>
                                    <li><strong>Password Security:</strong> Use a strong password and change it periodically</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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