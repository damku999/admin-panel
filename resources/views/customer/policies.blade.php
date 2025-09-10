@extends('layouts.customer')

@section('title', 'My Policies')

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        <!-- Policy Status Navigation -->
        <div class="content-section">
            <div class="section-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>
                        <i class="fas fa-filter me-2"></i>Policy Status Filter
                    </h4>
                </div>
            </div>
            <div class="section-body">
                <ul class="nav nav-pills nav-justified" id="policyTabs" role="tablist">
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
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="policyTabContent">
            <!-- Active Policies Tab -->
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                @if($activePolicies->count() > 0)
                    @foreach($activePolicies as $policy)
                        <div class="card mb-4 fade-in-scale" style="animation-delay: {{ $loop->index * 100 }}ms">
                            <div class="card-header bg-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0 text-white fw-bold">
                                            <i class="fas fa-shield-check me-2"></i>
                                            {{ $policy->policy_no ?? 'Policy #' . $policy->id }}
                                            <span class="badge bg-light text-success ms-2">Active</span>
                                        </h5>
                                        @if($policy->registration_no)
                                            <small class="text-light opacity-75">
                                                <i class="fas fa-car me-1"></i>{{ $policy->registration_no }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Policy Holder</h6>
                                                <p class="mb-0 text-muted">{{ $policy->customer->name }}</p>
                                                @if($policy->customer_id === $customer->id)
                                                    <span class="badge bg-primary">You</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-building fa-2x text-info"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Insurance Company</h6>
                                                <p class="mb-0 text-muted">{{ $policy->insuranceCompany->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-tag fa-2x text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Policy Type</h6>
                                                <p class="mb-0 text-muted">{{ $policy->policyType->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-rupee-sign fa-2x text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Premium Amount</h6>
                                                @if($policy->final_premium_with_gst)
                                                    <p class="mb-0 text-success fw-bold">₹{{ number_format($policy->final_premium_with_gst, 0) }}</p>
                                                @else
                                                    <p class="mb-0 text-muted">N/A</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-calendar-check fa-2x text-warning"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Policy Period</h6>
                                                <p class="mb-0 text-muted">
                                                    {{ $policy->start_date ? formatDateForUi($policy->start_date) : 'N/A' }}
                                                    to
                                                    {{ $policy->expired_date ? formatDateForUi($policy->expired_date) : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Status</h6>
                                                @php
                                                    $isExpired = $policy->expired_date ? \Carbon\Carbon::parse($policy->expired_date)->isPast() : false;
                                                    $isExpiringSoon = false;
                                                    if ($policy->expired_date && !$isExpired) {
                                                        $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($policy->expired_date), false);
                                                        $isExpiringSoon = $daysLeft <= 30;
                                                    }
                                                @endphp
                                                @if($isExpired)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Expired
                                                    </span>
                                                @elseif($isExpiringSoon)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Expiring Soon
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h4>No Active Policies</h4>
                                <p>You don't have any active insurance policies at the moment.</p>
                                <a href="{{ route('customer.quotations') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Get a Quote
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Expired Policies Tab -->
            <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
                @if($expiredPolicies->count() > 0)
                    @foreach($expiredPolicies as $policy)
                        <div class="card mb-4 fade-in-scale" style="animation-delay: {{ $loop->index * 100 }}ms">
                            <div class="card-header bg-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0 text-white fw-bold">
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $policy->policy_no ?? 'Policy #' . $policy->id }}
                                            <span class="badge bg-light text-danger ms-2">Expired</span>
                                        </h5>
                                        @if($policy->registration_no)
                                            <small class="text-light opacity-75">
                                                <i class="fas fa-car me-1"></i>{{ $policy->registration_no }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('customer.policies.detail', $policy->id) }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Policy Holder</h6>
                                                <p class="mb-0 text-muted">{{ $policy->customer->name }}</p>
                                                @if($policy->customer_id === $customer->id)
                                                    <span class="badge bg-primary">You</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-building fa-2x text-info"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Insurance Company</h6>
                                                <p class="mb-0 text-muted">{{ $policy->insuranceCompany->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-calendar-times fa-2x text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Expired Date</h6>
                                                <p class="mb-0 text-danger">
                                                    {{ $policy->expired_date ? formatDateForUi($policy->expired_date) : 'N/A' }}
                                                </p>
                                                @if($policy->expired_date)
                                                    @php
                                                        $daysExpired = \Carbon\Carbon::parse($policy->expired_date)->diffInDays(now());
                                                    @endphp
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>Expired {{ $daysExpired }} days ago
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Status</h6>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Expired
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4>No Expired Policies</h4>
                                <p>You don't have any expired insurance policies.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips and add animation delays
    $(document).ready(function() {
        // Add fade-in animation to cards
        $('.content-section').each(function(index) {
            $(this).css('animation-delay', (index * 100) + 'ms').addClass('fade-in-scale');
        });
        
        // Tab switching animation
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('data-bs-target');
            $(target).find('.content-section').each(function(index) {
                $(this).css('animation-delay', (index * 100) + 'ms').addClass('fade-in-scale');
            });
        });
    });
</script>
@endpush