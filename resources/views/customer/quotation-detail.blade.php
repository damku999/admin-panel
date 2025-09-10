@extends('layouts.customer')

@section('title', 'Quotation Details')

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <div class="row">
            <!-- Quotation Summary -->
            <div class="col-lg-4 col-md-12">
                <!-- Quote Header Card -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 0ms">
                    <div class="card-header bg-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 text-white fw-bold">
                                    <i class="fas fa-calculator me-2"></i>{{ $quotation->getQuoteReference() }}
                                </h5>
                            </div>
                            <div>
                                @php
                                    $statusColors = [
                                        'Draft' => 'secondary',
                                        'Generated' => 'info',
                                        'Sent' => 'warning',
                                        'Accepted' => 'primary',
                                        'Rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$quotation->status] ?? 'secondary' }}">
                                    {{ ucfirst($quotation->status ?? 'Draft') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($quotation->quotationCompanies->count() > 0)
                                <a href="{{ route('customer.quotations.download', $quotation->id) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-1"></i> Download PDF
                                </a>
                            @endif
                            <a href="{{ route('customer.quotations') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quote Details Card -->
                <div class="card mb-4 fade-in-scale" style="animation-delay: 100ms">
                    <div class="card-header bg-success">
                        <h5 class="mb-0 text-white fw-bold">
                            <i class="fas fa-info-circle me-2"></i>Quote Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Policy Holder</h6>
                                <p class="mb-0 text-muted">{{ $quotation->customer->name }}</p>
                                @if ($quotation->customer_id === $customer->id)
                                    <span class="badge bg-success">You</span>
                                @endif
                                <small class="text-muted">
                                    <strong>Mobile:</strong> {{ $quotation->customer->mobile_number ?? 'N/A' }}
                                    @if ($quotation->whatsapp_number && $quotation->whatsapp_number !== $quotation->customer->mobile_number)
                                        <br><strong>WhatsApp:</strong> {{ $quotation->whatsapp_number }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        @if ($quotation->vehicle_number || $quotation->make_model_variant)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-car fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Vehicle Details</h6>
                                    <p class="mb-0 text-muted">{{ $quotation->make_model_variant }}</p>
                                    <small class="text-muted">
                                        <strong>Number:</strong> {{ $quotation->vehicle_number ?? 'New Vehicle' }}<br>
                                        <strong>RTO:</strong> {{ $quotation->rto_location }} | 
                                        <strong>Fuel:</strong> {{ $quotation->fuel_type }} | 
                                        <strong>NCB:</strong> {{ $quotation->ncb_percentage ?? 0 }}%<br>
                                        <strong>Year:</strong> {{ $quotation->manufacturing_year }}
                                        @if ($quotation->cubic_capacity_kw)
                                            | <strong>CC/KW:</strong> {{ number_format($quotation->cubic_capacity_kw) }}
                                        @endif
                                        @if ($quotation->seating_capacity)
                                            | <strong>Seating:</strong> {{ $quotation->seating_capacity }} seats
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-file-contract fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Policy Details</h6>
                                <p class="mb-0 text-muted">{{ $quotation->policy_type ?? 'Comprehensive' }} - {{ $quotation->policy_tenure_years ?? 1 }} Year(s)</p>
                            </div>
                        </div>

                        @if ($quotation->total_idv)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-shield-alt fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Vehicle Valuation (IDV)</h6>
                                    <div class="mt-1">
                                        @if ($quotation->idv_vehicle)
                                            <small class="text-muted">Vehicle: <strong>₹{{ number_format($quotation->idv_vehicle) }}</strong></small><br>
                                        @endif
                                        @if ($quotation->idv_trailer > 0)
                                            <small class="text-muted">Trailer: <strong>₹{{ number_format($quotation->idv_trailer) }}</strong></small><br>
                                        @endif
                                        @if ($quotation->idv_cng_lpg_kit > 0)
                                            <small class="text-muted">CNG/LPG Kit: <strong>₹{{ number_format($quotation->idv_cng_lpg_kit) }}</strong></small><br>
                                        @endif
                                        @if ($quotation->idv_electrical_accessories > 0)
                                            <small class="text-muted">Electrical Accessories: <strong>₹{{ number_format($quotation->idv_electrical_accessories) }}</strong></small><br>
                                        @endif
                                        @if ($quotation->idv_non_electrical_accessories > 0)
                                            <small class="text-muted">Non-Electrical Accessories: <strong>₹{{ number_format($quotation->idv_non_electrical_accessories) }}</strong></small><br>
                                        @endif
                                        <strong class="text-success">Total IDV: ₹{{ number_format($quotation->total_idv, 0) }}</strong><br>
                                        <small class="text-muted">Maximum claim amount for total loss/theft</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($quotation->addon_covers && count($quotation->addon_covers) > 0)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-plus-circle fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Add-on Covers</h6>
                                    <div class="mt-1">
                                        @foreach ($quotation->addon_covers as $addon)
                                            <span class="badge bg-light text-dark me-1 mb-1">{{ $addon }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($quotation->notes)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-sticky-note fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Notes</h6>
                                    <small class="text-muted">{{ $quotation->notes }}</small>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-calendar fa-2x text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Created Date</h6>
                                <p class="mb-0 text-muted">{{ formatDateForUi($quotation->created_at, 'd M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if ($quotation->sent_at)
                            <div class="d-flex align-items-center mb-0">
                                <div class="me-3">
                                    <i class="fas fa-paper-plane fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Sent Date</h6>
                                    <p class="mb-0 text-muted">{{ formatDateForUi($quotation->sent_at, 'd M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Company Quotes -->
            <div class="col-lg-8 col-md-12">
                @if ($quotation->quotationCompanies->count() > 0)
                    <!-- Best Quote Header -->
                    <div class="card mb-4 fade-in-scale" style="animation-delay: 200ms">
                        <div class="card-header bg-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white fw-bold">
                                    <i class="fas fa-building me-2"></i>Insurance Company Quotes
                                    <span class="badge bg-light text-success ms-2">{{ $quotation->quotationCompanies->count() }}</span>
                                </h5>
                                @if ($quotation->bestQuote())
                                    <div class="text-white">
                                        <small>Best Quote:</small><br>
                                        <span class="h5 fw-bold mb-0">₹{{ number_format($quotation->bestQuote()->final_premium, 0) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quotes Table -->
                    <div class="card fade-in-scale" style="animation-delay: 300ms">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th><i class="fas fa-trophy me-1"></i>Rank</th>
                                            <th><i class="fas fa-building me-1"></i>Company</th>
                                            <th><i class="fas fa-file-contract me-1"></i>Plan</th>
                                            <th><i class="fas fa-shield-check me-1"></i>Basic OD</th>
                                            <th><i class="fas fa-car-crash me-1"></i>TP Premium</th>
                                            <th><i class="fas fa-plus me-1"></i>Add-on</th>
                                            <th><i class="fas fa-gas-pump me-1"></i>CNG/LPG</th>
                                            <th><i class="fas fa-rupee-sign me-1"></i>Net</th>
                                            <th><i class="fas fa-percentage me-1"></i>GST</th>
                                            <th><i class="fas fa-money-check-alt me-1"></i>Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotation->quotationCompanies->sortBy('ranking') as $company)
                                            <tr class="{{ $company->is_recommended ? 'table-success' : '' }}">
                                                <td>
                                                    @if ($company->is_recommended)
                                                        <span class="badge bg-success text-white" title="{{ $company->recommendation_note ?? 'Recommended by our experts' }}">
                                                            <i class="fas fa-star"></i> #{{ $company->ranking }}
                                                        </span>
                                                        <br><small class="text-success fw-bold">Recommended</small>
                                                    @else
                                                        <span class="badge bg-secondary">#{{ $company->ranking }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong class="text-dark">{{ $company->insuranceCompany->name }}</strong>
                                                        @if ($company->is_recommended)
                                                            <span class="badge bg-success text-white ms-1">
                                                                <i class="fas fa-thumbs-up"></i> Recommended
                                                            </span>
                                                        @endif
                                                        <br><small class="text-muted">
                                                            @if ($company->quote_number)
                                                                Quote: {{ $company->quote_number }}
                                                            @else
                                                                Auto-generated
                                                            @endif
                                                            @if ($company->policy_type)
                                                                | {{ $company->policy_type }}
                                                            @endif
                                                            @if ($company->policy_tenure_years)
                                                                | {{ $company->policy_tenure_years }} Year(s)
                                                            @endif
                                                        </small>
                                                        @if ($company->is_recommended && $company->recommendation_note)
                                                            <br><small class="text-success fw-bold">
                                                                <i class="fas fa-quote-left me-1"></i>{{ $company->recommendation_note }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $company->plan_name ?? 'Standard Plan' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">₹{{ number_format($company->basic_od_premium ?? 0, 0) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">₹{{ number_format($company->tp_premium ?? 0, 0) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-info">₹{{ number_format($company->total_addon_premium ?? 0, 0) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">₹{{ number_format($company->cng_lpg_premium ?? 0, 0) }}</span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">₹{{ number_format($company->net_premium ?? 0, 0) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted">₹{{ number_format(($company->sgst_amount ?? 0) + ($company->cgst_amount ?? 0), 0) }}</span>
                                                </td>
                                                <td>
                                                    <strong class="text-success h6 fw-bold">₹{{ number_format($company->final_premium ?? 0, 0) }}</strong>
                                                    @if ($company->roadside_assistance > 0)
                                                        <br><small class="text-info"><i class="fas fa-tools me-1"></i>+RSA</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Company-wise Add-on Coverage Details -->
                    @if ($quotation->quotationCompanies->count() > 0 && $quotation->quotationCompanies->some(function($company) { return $company->addon_covers || $company->total_addon_premium > 0; }))
                        <div class="card mt-4 fade-in-scale" style="animation-delay: 400ms">
                            <div class="card-header bg-success">
                                <h5 class="mb-0 text-white fw-bold">
                                    <i class="fas fa-plus-circle me-2"></i>Company-wise Add-on Coverage Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($quotation->quotationCompanies->sortBy('ranking') as $company)
                                        @if ($company->addon_covers || $company->total_addon_premium > 0)
                                            <div class="col-lg-6 col-md-12 mb-4">
                                                <div class="card border-{{ $company->is_recommended ? 'success' : 'light' }} h-100">
                                                    <div class="card-header bg-{{ $company->is_recommended ? 'success' : 'light' }} {{ $company->is_recommended ? 'text-white' : 'text-dark' }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0 fw-bold">
                                                                <i class="fas fa-building me-2"></i>{{ $company->insuranceCompany->name }}
                                                            </h6>
                                                            @if ($company->is_recommended)
                                                                <span class="badge bg-light text-success">
                                                                    <i class="fas fa-star me-1"></i>Recommended
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        @if ($company->addon_covers)
                                                            <div class="mb-3">
                                                                <h6 class="text-muted mb-2">
                                                                    <i class="fas fa-shield-alt me-1"></i>Add-on Covers
                                                                </h6>
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach ($company->addon_covers as $addon)
                                                                        <span class="badge bg-success text-white">{{ $addon }}</span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="mb-3">
                                                            <h6 class="text-muted mb-2">
                                                                <i class="fas fa-rupee-sign me-1"></i>Premium Breakdown
                                                            </h6>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <small class="text-muted">Add-on Premium:</small>
                                                                    <div class="fw-bold text-success">₹{{ number_format($company->total_addon_premium ?? 0, 0) }}</div>
                                                                </div>
                                                                @if ($company->roadside_assistance > 0)
                                                                    <div class="col-6">
                                                                        <small class="text-muted">Roadside Assistance:</small>
                                                                        <div class="fw-bold text-info">₹{{ number_format($company->roadside_assistance, 0) }}</div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($company->addon_benefits)
                                                            <div class="mb-3">
                                                                <h6 class="text-muted mb-2">
                                                                    <i class="fas fa-check-circle me-1"></i>Benefits
                                                                </h6>
                                                                <div class="small text-muted">
                                                                    {{ $company->addon_benefits }}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="border-top pt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="text-muted">Total Add-on Value:</span>
                                                                <span class="h6 fw-bold text-success mb-0">₹{{ number_format($company->total_addon_premium ?? 0, 0) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state text-center py-5">
                                <div class="empty-icon">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                </div>
                                <h4>No Company Quotes Available</h4>
                                <p class="text-muted">This quotation does not have any insurance company quotes generated yet.</p>
                                <div class="alert alert-info">
                                    <strong>Note:</strong> Contact your insurance agent or admin to generate quotes from multiple insurance companies.
                                </div>
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
    $(document).ready(function() {
        // Initialize animations
        $('.card').each(function(index) {
            if (!$(this).hasClass('fade-in-scale')) {
                $(this).css('animation-delay', (index * 150) + 'ms').addClass('fade-in-scale');
            }
        });
        
        // Add emphasis to recommended quotes
        $('.table-success').addClass('border-success').css('border-width', '2px');
    });
</script>
@endpush
