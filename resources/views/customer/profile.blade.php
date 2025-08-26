@extends('layouts.customer')

@section('title', 'Customer Profile')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Customer Profile Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Name</strong></label>
                                <p class="form-control-static">{{ $customer->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Email Address</strong></label>
                                <p class="form-control-static">
                                    {{ $customer->email }}
                                    @if($customer->hasVerifiedEmail())
                                        <span class="badge badge-success ml-2">Verified</span>
                                    @else
                                        <span class="badge badge-warning ml-2">Not Verified</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Mobile Number</strong></label>
                                <p class="form-control-static">{{ $customer->mobile_number ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Customer Type</strong></label>
                                <p class="form-control-static">
                                    <span class="badge badge-{{ $customer->type == 'Retail' ? 'info' : 'success' }}">
                                        {{ $customer->type ?? 'Not specified' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Date of Birth</strong></label>
                                <p class="form-control-static">
                                    {{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : 'Not provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Account Status</strong></label>
                                <p class="form-control-static">
                                    @if($customer->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Member Since</strong></label>
                                <p class="form-control-static">{{ $customer->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Last Updated</strong></label>
                                <p class="form-control-static">{{ $customer->updated_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($customer->type == 'Retail')
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>PAN Card Number</strong></label>
                                <p class="form-control-static">{{ $customer->getMaskedPanNumber() ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>Aadhar Card Number</strong></label>
                                <p class="form-control-static">
                                    {{ $customer->aadhar_card_number ? '****' . substr($customer->aadhar_card_number, -4) : 'Not provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($customer->type == 'Corporate')
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1"><strong>GST Number</strong></label>
                                <p class="form-control-static">{{ $customer->gst_number ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            @if($familyGroup)
            <!-- Family Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Family Information</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="small mb-1"><strong>Family Group</strong></label>
                        <p class="form-control-static">{{ $familyGroup->name }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="small mb-1"><strong>Your Role</strong></label>
                        <p class="form-control-static">
                            @if($isHead)
                                <span class="badge badge-success">Family Head</span>
                            @else
                                <span class="badge badge-info">Family Member</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="small mb-1"><strong>Total Family Members</strong></label>
                        <p class="form-control-static">{{ $familyMembers->count() }} members</p>
                    </div>

                    @if($customer->familyMember && $customer->familyMember->relationship && !$isHead)
                    <div class="form-group">
                        <label class="small mb-1"><strong>Relationship</strong></label>
                        <p class="form-control-static">{{ $customer->familyMember->relationship }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- No Family Group -->
            <div class="card border-left-warning shadow mb-4">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-users fa-3x text-warning mb-3"></i>
                        <h6>Not Part of Family Group</h6>
                        <p class="text-muted small">You are not currently assigned to any family group.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('customer.change-password') }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                    
                    @if(!$customer->hasVerifiedEmail())
                    <a href="{{ route('customer.verify-email-notice') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-envelope"></i> Verify Email
                    </a>
                    @endif
                    
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-tachometer-alt"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection