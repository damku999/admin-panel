@extends('layouts.customer')

@section('title', 'Policy Details')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Policy Details</h1>
        <div>
            <a href="{{ route('customer.policies') }}" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Back to Policies
            </a>
            @if($policy->policy_document_path)
            <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Download Document
            </a>
            @endif
        </div>
    </div>

    <!-- Policy Status Alert -->
    @if($isExpired)
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Policy Expired:</strong> This policy expired {{ abs($daysUntilExpiry) }} days ago on {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}.
    </div>
    @elseif($isExpiringSoon)
    <div class="alert alert-warning">
        <i class="fas fa-clock mr-2"></i>
        <strong>Expiring Soon:</strong> This policy will expire in {{ $daysUntilExpiry }} days on {{ \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') }}.
    </div>
    @endif

    <!-- Policy Information -->
    <div class="row">
        <!-- Main Policy Details -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-contract mr-2"></i>Policy Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Policy Number</strong></label>
                                <p class="form-control-static h5 text-primary">{{ $policy->policy_no ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Registration Number</strong></label>
                                <p class="form-control-static">{{ $policy->registration_no ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Policy Holder</strong></label>
                                <p class="form-control-static">
                                    {{ $policy->customer->name }}
                                    @if($policy->customer_id === $customer->id)
                                        <span class="badge badge-info ml-1">You</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Insurance Company</strong></label>
                                <p class="form-control-static">{{ $policy->insuranceCompany->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Policy Type</strong></label>
                                <p class="form-control-static">
                                    <span class="badge badge-info">{{ $policy->policyType->name ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Premium Type</strong></label>
                                <p class="form-control-static">
                                    <span class="badge badge-secondary">{{ $policy->premiumType->name ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Vehicle/Asset Information -->
                    @if($policy->vehicle_make || $policy->vehicle_model || $policy->vehicle_variant)
                    <h6 class="font-weight-bold text-dark mb-3">
                        <i class="fas fa-car mr-2"></i>Vehicle Information
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Make</strong></label>
                                <p class="form-control-static">{{ $policy->vehicle_make ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Model</strong></label>
                                <p class="form-control-static">{{ $policy->vehicle_model ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Variant</strong></label>
                                <p class="form-control-static">{{ $policy->vehicle_variant ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Manufacturing Year</strong></label>
                                <p class="form-control-static">{{ $policy->manufacturing_year ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Engine CC</strong></label>
                                <p class="form-control-static">{{ $policy->engine_cc ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @endif

                    <!-- Premium Information -->
                    <h6 class="font-weight-bold text-dark mb-3">
                        <i class="fas fa-money-bill-wave mr-2"></i>Premium Details
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Base Premium</strong></label>
                                <p class="form-control-static">
                                    @if($policy->base_premium)
                                        ₹{{ number_format($policy->base_premium, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>GST Amount</strong></label>
                                <p class="form-control-static">
                                    @if($policy->gst_amount)
                                        ₹{{ number_format($policy->gst_amount, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Final Premium (with GST)</strong></label>
                                <p class="form-control-static">
                                    @if($policy->final_premium_with_gst)
                                        <span class="h5 text-success font-weight-bold">₹{{ number_format($policy->final_premium_with_gst, 2) }}</span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Sum Assured</strong></label>
                                <p class="form-control-static">
                                    @if($policy->sum_assured)
                                        ₹{{ number_format($policy->sum_assured, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policy Status & Actions -->
        <div class="col-xl-4">
            <!-- Policy Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Policy Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="small mb-1"><strong>Current Status</strong></label>
                        <p class="form-control-static">
                            @if($isExpired)
                                <span class="badge badge-danger">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="badge badge-warning">Expiring Soon</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="small mb-1"><strong>Policy Start Date</strong></label>
                        <p class="form-control-static">{{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('d M Y') : 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label class="small mb-1"><strong>Policy Expiry Date</strong></label>
                        <p class="form-control-static">
                            {{ $policy->expired_date ? \Carbon\Carbon::parse($policy->expired_date)->format('d M Y') : 'N/A' }}
                        </p>
                    </div>

                    @if($policy->expired_date && !$isExpired)
                    <div class="form-group">
                        <label class="small mb-1"><strong>Days Remaining</strong></label>
                        <p class="form-control-static">
                            <span class="badge {{ $daysUntilExpiry <= 30 ? 'badge-warning' : 'badge-info' }}">
                                {{ $daysUntilExpiry }} days
                            </span>
                        </p>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="small mb-1"><strong>Created Date</strong></label>
                        <p class="form-control-static">{{ $policy->created_at->format('d M Y') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="small mb-1"><strong>Last Updated</strong></label>
                        <p class="form-control-static">{{ $policy->updated_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Document Information -->
            @if($policy->policy_document_path)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-pdf mr-2"></i>Policy Document
                    </h6>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                    <p class="text-muted mb-3">Policy document is available for download</p>
                    <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-success btn-block">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                </div>
            </div>
            @else
            <div class="card border-left-warning shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-file-times fa-3x text-warning mb-3"></i>
                    <h6>No Document Available</h6>
                    <p class="text-muted small">Policy document is not available for download</p>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools mr-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('customer.policies') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-list mr-2"></i>All Policies
                    </a>
                    
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>

                    @if($policy->policy_document_path)
                    <a href="{{ route('customer.policies.download', $policy->id) }}" class="btn btn-success btn-block">
                        <i class="fas fa-download mr-2"></i>Download Document
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    @if($policy->remark)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-comment mr-2"></i>Remarks
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $policy->remark }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection