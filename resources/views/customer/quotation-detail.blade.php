@extends('layouts.customer')

@section('title', 'Quotation Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">
            <i class="fas fa-file-alt me-2"></i> Quotation Details
        </h1>
        <div class="d-flex">
            @if ($quotation->quotationCompanies->count() > 0)
                <a href="{{ route('customer.quotations.download', $quotation->id) }}"
                    class="btn btn-webmonks btn-sm me-2">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            @endif
            <a href="{{ route('customer.quotations') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-webmonks-dashboard">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <div class="row">
        <!-- Quotation Summary -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 fw-bold text-dark">Quotation Summary</h6>
                    @php
                        $statusColors = [
                            'Draft' => 'secondary',
                            'Generated' => 'info',
                            'Sent' => 'warning',
                            'Accepted' => 'success',
                            'Rejected' => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$quotation->status] ?? 'secondary' }}">
                        {{ ucfirst($quotation->status ?? 'Draft') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Quote Reference:</strong><br>
                        <span class="h6 fw-bold quote-reference">{{ $quotation->getQuoteReference() }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Policy Holder:</strong><br>
                        {{ $quotation->customer->name }}
                        @if ($quotation->customer_id === $customer->id)
                            <span class="badge bg-info ms-2">You</span>
                        @endif
                        <br>
                        <small class="text-muted">
                            <strong>Mobile:</strong> {{ $quotation->customer->mobile_number }}
                            @if ($quotation->whatsapp_number && $quotation->whatsapp_number !== $quotation->customer->mobile_number)
                                <br><strong>WhatsApp:</strong> {{ $quotation->whatsapp_number }}
                            @endif
                        </small>
                    </div>

                    @if ($quotation->vehicle_number)
                        <div class="mb-3">
                            <strong>Vehicle Details:</strong><br>
                            {{ $quotation->make_model_variant }}<br>
                            <small class="text-muted">
                                <strong>Number:</strong> {{ $quotation->vehicle_number ?? 'New Vehicle' }}<br>
                                <strong>RTO:</strong> {{ $quotation->rto_location }} |
                                <strong>Fuel:</strong> {{ $quotation->fuel_type }} |
                                <strong>NCB:</strong> {{ $quotation->ncb_percentage ?? 0 }}%<br>
                                <strong>Year:</strong> {{ $quotation->manufacturing_year }}
                                <br>
                                @if ($quotation->cubic_capacity_kw)
                                    <strong>CC/KW:</strong> {{ number_format($quotation->cubic_capacity_kw) }} |
                                @endif
                                @if ($quotation->seating_capacity)
                                    <strong>Seating:</strong> {{ $quotation->seating_capacity }} seats
                                @endif
                            </small>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Policy Details:</strong><br>
                        {{ $quotation->policy_type ?? 'Comprehensive' }} - {{ $quotation->policy_tenure_years ?? 1 }} Year(s)<br>
                        <small class="text-muted">
                            <div class="mt-2">
                                <strong>Vehicle Valuation (IDV):</strong><br>
                                <div class="ms-2">
                                    @if ($quotation->idv_vehicle)
                                        Vehicle: ₹{{ number_format($quotation->idv_vehicle) }}<br>
                                    @endif
                                    @if ($quotation->idv_trailer > 0)
                                        Trailer: ₹{{ number_format($quotation->idv_trailer) }}<br>
                                    @endif
                                    @if ($quotation->idv_cng_lpg_kit > 0)
                                        CNG/LPG Kit: ₹{{ number_format($quotation->idv_cng_lpg_kit) }}<br>
                                    @endif
                                    @if ($quotation->idv_electrical_accessories > 0)
                                        Electrical Accessories: ₹{{ number_format($quotation->idv_electrical_accessories) }}<br>
                                    @endif
                                    @if ($quotation->idv_non_electrical_accessories > 0)
                                        Non-Electrical Accessories: ₹{{ number_format($quotation->idv_non_electrical_accessories) }}<br>
                                    @endif
                                </div>
                                @if ($quotation->total_idv)
                                    <strong class="text-info">Total Vehicle Value (IDV): ₹{{ number_format($quotation->total_idv) }}</strong><br>
                                    <small class="text-muted">This is the maximum claim amount for total loss/theft</small>
                                @endif
                            </div>
                        </small>
                    </div>

                    @if ($quotation->addon_covers && count($quotation->addon_covers) > 0)
                        <div class="mb-3">
                            <strong>Add-on Covers:</strong><br>
                            @foreach ($quotation->addon_covers as $addon)
                                <span class="badge bg-light text-dark me-1">{{ $addon }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if ($quotation->notes)
                        <div class="mb-3">
                            <strong>Notes:</strong><br>
                            <small>{{ $quotation->notes }}</small>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <small class="text-muted">
                            {{ $quotation->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>

                    @if ($quotation->sent_at)
                        <div class="mb-3">
                            <strong>Sent:</strong><br>
                            <small class="text-muted">
                                {{ $quotation->sent_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Company Quotes -->
        <div class="col-lg-8 col-md-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-building me-2"></i> Insurance Company Quotes
                        @if ($quotation->quotationCompanies->count() > 0)
                            <span class="badge bg-info ms-2">{{ $quotation->quotationCompanies->count() }}</span>
                        @endif
                    </h6>
                    @if ($quotation->quotationCompanies->count() > 0 && $quotation->bestQuote())
                        <div class="text-end">
                            <small class="text-white fw-bold">Best Quote:</small><br>
                            <span class="h5 fw-bold mb-0 text-white">₹{{ number_format($quotation->bestQuote()->final_premium, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if ($quotation->quotationCompanies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Insurance Company</th>
                                        <th>Plan Name</th>
                                        <th>Basic OD</th>
                                        <th>TP Premium</th>
                                        <th>Add-on</th>
                                        <th>Net Premium</th>
                                        <th>GST</th>
                                        <th>Final Premium</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotation->quotationCompanies->sortBy('ranking') as $company)
                                        <tr class="{{ $company->is_recommended ? 'table-success' : '' }}">
                                            <td>
                                                @if ($company->is_recommended)
                                                    <span class="badge bg-success text-white" data-bs-toggle="tooltip" 
                                                          title="{{ $company->recommendation_note ?? 'Recommended by our experts' }}">
                                                        <i class="fas fa-star"></i> {{ $company->ranking }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $company->ranking }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $company->insuranceCompany->name }}</strong>
                                                @if ($company->is_recommended)
                                                    <span class="badge bg-success text-white ms-1">
                                                        <i class="fas fa-thumbs-up"></i> Recommended
                                                    </span>
                                                @endif
                                                <br>
                                                <small class="text-muted">
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
                                                        <i class="fas fa-quote-left me-1"></i> {{ $company->recommendation_note }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $company->plan_name ?? 'Standard Plan' }}</td>
                                            <td>₹{{ number_format($company->basic_od_premium ?? 0) }}</td>
                                            <td>₹{{ number_format($company->tp_premium ?? 0) }}</td>
                                            <td>₹{{ number_format($company->total_addon_premium ?? 0) }}</td>
                                            <td>₹{{ number_format($company->net_premium ?? 0) }}</td>
                                            <td>₹{{ number_format(($company->sgst_amount ?? 0) + ($company->cgst_amount ?? 0)) }}
                                            </td>
                                            <td>
                                                <strong
                                                    class="fw-bold final-premium-amount">₹{{ number_format($company->final_premium ?? 0, 2) }}</strong>
                                                @if ($company->roadside_assistance > 0)
                                                    <br><small class="text-muted">+RSA</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Add-on Coverage Breakdown -->
                        @if ($quotation->quotationCompanies->where('total_addon_premium', '>', 0)->count() > 0)
                            <div class="mt-4">
                                <h6 class="fw-bold text-success">
                                    <i class="fas fa-plus-circle me-2"></i> Add-on Coverage Breakdown
                                </h6>
                                <div class="row">
                                    @foreach ($quotation->quotationCompanies->where('total_addon_premium', '>', 0) as $company)
                                        <div class="col-md-12 mb-4">
                                            <div class="card border-start border-success border-3">
                                                <div class="card-header py-2" style="background: var(--webmonks-success); color: white;">
                                                    <h6 class="m-0 fw-bold">
                                                        {{ $company->insuranceCompany->name }}
                                                        @if ($company->quote_number)
                                                            <small class="ms-2">({{ $company->quote_number }})</small>
                                                        @endif
                                                        <span class="float-end">Total:
                                                            ₹{{ number_format($company->total_addon_premium) }}</span>
                                                    </h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    @if ($company->addon_covers_breakdown)
                                                        <div class="row">
                                                            @php
                                                                $addonCount = 0;
                                                                $addonsWithPrice = collect(
                                                                    $company->addon_covers_breakdown,
                                                                )->filter(function ($data) {
                                                                    return (is_array($data) &&
                                                                        isset($data['price']) &&
                                                                        $data['price'] > 0) ||
                                                                        (is_numeric($data) && $data > 0);
                                                                });
                                                            @endphp
                                                            @foreach ($company->addon_covers_breakdown as $addon => $data)
                                                                @if (is_array($data) && isset($data['price']) && $data['price'] > 0)
                                                                    @php $addonCount++; @endphp
                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <strong
                                                                                    class="small fw-bold addon-label">{{ $addon }}:</strong>
                                                                                <strong
                                                                                    class="small fw-bold">₹{{ number_format($data['price']) }}</strong>
                                                                            </div>
                                                                            @if (!empty($data['note']))
                                                                                <div class="text-muted addon-note">
                                                                                    <em>{{ $data['note'] }}</em>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    @if ($addonCount % 3 == 0 && $addonCount < $addonsWithPrice->count())
                                                        </div>
                                                        <div class="row">
                                                    @endif
                                                @elseif(is_numeric($data) && $data > 0)
                                                    @php $addonCount++; @endphp
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <strong
                                                                    class="small fw-bold addon-label">{{ $addon }}:</strong>
                                                                <strong class="small fw-bold">₹{{ number_format($data) }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($addonCount % 3 == 0 && $addonCount < $addonsWithPrice->count())
                                                </div>
                                                <div class="row">
                                    @endif
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted">
                    <small>No addon breakdown details available</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    </div>
    </div>
    @endif

@else
    <div class="text-center py-5">
        <i class="fas fa-building fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No Company Quotes Available</h5>
        <p class="text-muted">This quotation does not have any insurance company quotes generated yet.</p>
        <div class="alert alert-info">
            <strong>Note:</strong> Contact your insurance agent or admin to generate quotes from multiple insurance
            companies.
        </div>
    </div>
    @endif
    </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
    <style>
        /* Page-specific styles only */
    </style>
    
    <script>
        $(document).ready(function() {
            // Initialize tooltips using jQuery (compatible with both Bootstrap versions)
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Add hover effects for recommended quotes
            $('.table-success').hover(
                function() {
                    $(this).addClass('bg-success text-white');
                },
                function() {
                    $(this).removeClass('bg-success text-white');
                }
            );
            
            // Add visual emphasis to best quote
            $('.table tbody tr:first-child').addClass('border-primary').css('border-width', '2px');
            
            // Fix layout on page load
            $('.container-fluid').css('max-width', '100%');
            $('.row').css('margin', '0 -15px');
            
            // Show informational alerts for first-time users
            if (sessionStorage.getItem('quotation_info_shown') !== 'true') {
                setTimeout(function() {
                    if ($('.bg-success').length > 0) {
                        // Use a more subtle notification instead of alert
                        if ($('.alert').length === 0) {
                            $('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                              '<i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Look for the <span class="badge bg-success text-white"><i class="fas fa-star"></i> Recommended</span> badges - these are quotes our experts suggest based on coverage and value!' +
                              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                              '</div>').insertAfter('.mb-4:first');
                        }
                        sessionStorage.setItem('quotation_info_shown', 'true');
                    }
                }, 2000);
            }
        });
    </script>
@endsection
