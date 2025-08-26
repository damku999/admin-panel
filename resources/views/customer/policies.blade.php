@extends('layouts.customer')

@section('title', 'My Policies')

@section('content')
    <div class="container-fluid">
        <!-- Compact Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">
                @if($isHead)
                    Family Policies
                @else
                    My Policies
                @endif
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-success">{{ $activePolicies->count() }} Active</span>
                <span class="badge bg-danger">{{ $expiredPolicies->count() }} Expired</span>
            </div>
        </div>

        <!-- Policy Status Tabs -->
        <ul class="nav nav-pills nav-justified mb-4" id="policyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="pill" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">
                    <i class="fas fa-shield-check me-2"></i>
                    <span class="fw-bold">Active Policies</span>
                    <span class="badge bg-success ms-2">{{ $activePolicies->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="expired-tab" data-bs-toggle="pill" data-bs-target="#expired" type="button" role="tab" aria-controls="expired" aria-selected="false">
                    <i class="fas fa-clock me-2"></i>
                    <span class="fw-bold">Expired Policies</span>
                    <span class="badge bg-danger ms-2">{{ $expiredPolicies->count() }}</span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="policyTabContent">
            <!-- Active Policies Tab -->
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                @if($activePolicies->count() > 0)
                    @foreach($activePolicies as $policy)
                        <div class="card mb-4 fade-in">
                            <div class="card-header bg-success d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-shield-check me-2"></i>
                                        {{ $policy->policy_no ?? 'Policy #' . $policy->id }}
                                    </h5>
                                    @if($policy->registration_no)
                                        <small class="text-white-50">Vehicle: {{ $policy->registration_no }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-success fw-bold">ACTIVE</span>
                                    @if($policy->expired_date)
                                        @php
                                            $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                        @endphp
                                        @if($daysLeft <= 30 && $daysLeft > 0)
                                            <br><span class="badge bg-warning text-dark mt-1">{{ $daysLeft }} days left</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-user-circle me-2"></i>Policy Holder
                                        </h6>
                                        <p class="mb-2">
                                            <strong>{{ $policy->customer->name }}</strong>
                                            @if($policy->customer_id === $customer->id)
                                                <span class="badge bg-info ms-2">You</span>
                                            @endif
                                        </p>
                                        
                                        <h6 class="text-brand mb-3 mt-4">
                                            <i class="fas fa-building me-2"></i>Insurance Details
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Company:</strong> {{ $policy->insuranceCompany->name ?? 'N/A' }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Policy Type:</strong> {{ $policy->policyType->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-rupee-sign me-2"></i>Premium Information
                                        </h6>
                                        <div class="bg-light p-3 rounded mb-3">
                                            <h4 class="text-success mb-0">
                                                @if($policy->final_premium_with_gst)
                                                    ₹{{ number_format($policy->final_premium_with_gst, 2) }}
                                                @else
                                                    Amount not available
                                                @endif
                                            </h4>
                                            <small class="text-muted">Total Premium (incl. GST)</small>
                                        </div>
                                        
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>Coverage Period
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Start Date:</strong> {{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Expiry Date:</strong> 
                                            @if($policy->expired_date)
                                                {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                                @php
                                                    $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                                @endphp
                                                @if($daysLeft <= 30 && $daysLeft > 0)
                                                    <span class="text-warning fw-bold ms-2">
                                                        <i class="fas fa-exclamation-triangle"></i> {{ $daysLeft }} days remaining
                                                    </span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-3 mt-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-shield-check text-success"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-success">Policy Active</strong>
                                                    <br><small class="text-muted">Your coverage is currently active and valid</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                                @if($policy->policy_document_path)
                                                    <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card fade-in">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="fas fa-shield-alt fa-3x text-success"></i>
                                </div>
                            </div>
                            <h4 class="text-success mb-3">No Active Policies Found</h4>
                            <p class="text-muted mb-4">You don't have any active insurance policies at the moment.</p>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Expired Policies Tab -->
            <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
                @if($expiredPolicies->count() > 0)
                    @foreach($expiredPolicies as $policy)
                        <div class="card mb-4 fade-in">
                            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        {{ $policy->policy_no ?? 'Policy #' . $policy->id }}
                                    </h5>
                                    @if($policy->registration_no)
                                        <small class="text-white-50">Vehicle: {{ $policy->registration_no }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-danger fw-bold">EXPIRED</span>
                                    @if($policy->expired_date)
                                        @php
                                            $daysExpired = \Carbon\Carbon::parse($policy->expired_date)->diffInDays(now());
                                        @endphp
                                        <br><span class="badge bg-warning text-dark mt-1">{{ $daysExpired }} days ago</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-user-circle me-2"></i>Policy Holder
                                        </h6>
                                        <p class="mb-2">
                                            <strong>{{ $policy->customer->name }}</strong>
                                            @if($policy->customer_id === $customer->id)
                                                <span class="badge bg-info ms-2">You</span>
                                            @endif
                                        </p>
                                        
                                        <h6 class="text-brand mb-3 mt-4">
                                            <i class="fas fa-building me-2"></i>Insurance Details
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Company:</strong> {{ $policy->insuranceCompany->name ?? 'N/A' }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Policy Type:</strong> {{ $policy->policyType->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-rupee-sign me-2"></i>Premium Information
                                        </h6>
                                        <div class="bg-light p-3 rounded mb-3">
                                            <h4 class="text-muted mb-0">
                                                @if($policy->final_premium_with_gst)
                                                    ₹{{ number_format($policy->final_premium_with_gst, 2) }}
                                                @else
                                                    Amount not available
                                                @endif
                                            </h4>
                                            <small class="text-muted">Total Premium (incl. GST)</small>
                                        </div>
                                        
                                        <h6 class="text-brand mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>Coverage Period
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Start Date:</strong> {{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Expiry Date:</strong> 
                                            @if($policy->expired_date)
                                                {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}
                                                @php
                                                    $daysExpired = \Carbon\Carbon::parse($policy->expired_date)->diffInDays(now());
                                                @endphp
                                                <span class="text-danger fw-bold ms-2">
                                                    <i class="fas fa-exclamation-triangle"></i> Expired {{ $daysExpired }} days ago
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-3 mt-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-danger">Policy Expired</strong>
                                                    <br><small class="text-muted">This policy is no longer active - consider renewal</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                                @if($policy->policy_document_path)
                                                    <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card fade-in">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="fas fa-check-circle fa-3x text-success"></i>
                                </div>
                            </div>
                            <h4 class="text-success mb-3">Great News!</h4>
                            <p class="text-muted mb-4">You don't have any expired insurance policies. All your policies are up to date!</p>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection