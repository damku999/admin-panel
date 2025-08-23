@extends('layouts.app')

@section('title', 'Quotation Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt"></i> Quotation Details
        </h1>
        <div class="d-flex">
            <a href="{{ route('quotations.index') }}" 
               class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can('quotation-edit')
                <a href="{{ route('quotations.edit', $quotation) }}" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit Quotation
                </a>
            @endcan
        </div>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <div class="row">
        <!-- Quotation Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quotation Summary</h6>
                    @php
                        $statusColors = [
                            'Draft' => 'secondary',
                            'Generated' => 'info',
                            'Sent' => 'warning',
                            'Accepted' => 'success',
                            'Rejected' => 'danger'
                        ];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$quotation->status] ?? 'secondary' }}">
                        {{ $quotation->status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Quote Reference:</strong><br>
                        <span class="h6 text-primary">{{ $quotation->getQuoteReference() }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Customer:</strong><br>
                        {{ $quotation->customer->name }}<br>
                        <small class="text-muted">
                            <strong>Mobile:</strong> {{ $quotation->customer->mobile_number }}
                            @if($quotation->whatsapp_number && $quotation->whatsapp_number !== $quotation->customer->mobile_number)
                                <br><strong>WhatsApp:</strong> {{ $quotation->whatsapp_number }}
                            @endif
                        </small>
                    </div>

                    <div class="mb-3">
                        <strong>Vehicle Details:</strong><br>
                        {{ $quotation->make_model_variant }}<br>
                        <small class="text-muted">
                            <strong>Number:</strong> {{ $quotation->vehicle_number ?? 'New Vehicle' }}<br>
                            <strong>RTO:</strong> {{ $quotation->rto_location }} | 
                            <strong>Fuel:</strong> {{ $quotation->fuel_type }} | 
                            <strong>Year:</strong> {{ $quotation->manufacturing_year }}<br>
                            <strong>Registration:</strong> {{ \Carbon\Carbon::parse($quotation->date_of_registration)->format('d M Y') }}<br>
                            <strong>CC/KW:</strong> {{ number_format($quotation->cubic_capacity_kw) }} | 
                            <strong>Seating:</strong> {{ $quotation->seating_capacity }} seats
                        </small>
                    </div>

                    <div class="mb-3">
                        <strong>Policy Details:</strong><br>
                        {{ $quotation->policy_type }} - {{ $quotation->policy_tenure_years }} Year(s)<br>
                        <small class="text-muted">
                            Vehicle IDV: ₹{{ number_format($quotation->idv_vehicle ?? 0) }}
                            @if($quotation->idv_trailer > 0) | Trailer: ₹{{ number_format($quotation->idv_trailer) }}@endif
                            @if($quotation->idv_cng_lpg_kit > 0) | CNG/LPG: ₹{{ number_format($quotation->idv_cng_lpg_kit) }}@endif
                            @if($quotation->idv_electrical_accessories > 0) | Elec. Acc.: ₹{{ number_format($quotation->idv_electrical_accessories) }}@endif
                            @if($quotation->idv_non_electrical_accessories > 0) | Non-Elec. Acc.: ₹{{ number_format($quotation->idv_non_electrical_accessories) }}@endif
                            <br><strong>Total IDV: ₹{{ number_format($quotation->total_idv) }}</strong>
                        </small>
                    </div>

                    @if($quotation->addon_covers)
                        <div class="mb-3">
                            <strong>Add-on Covers:</strong><br>
                            @foreach($quotation->addon_covers as $addon)
                                <span class="badge badge-light">{{ $addon }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($quotation->notes)
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

                    @if($quotation->sent_at)
                        <div class="mb-3">
                            <strong>Sent:</strong><br>
                            <small class="text-muted">
                                {{ $quotation->sent_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if($quotation->quotationCompanies->count() == 0)
                        @can('quotation-generate')
                            <form method="POST" action="{{ route('quotations.generate-quotes', $quotation) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-cog"></i> Generate Company Quotes
                                </button>
                            </form>
                        @endcan
                    @else
                        @can('quotation-download-pdf')
                            <a href="{{ route('quotations.download-pdf', $quotation) }}" 
                               class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        @endcan

                        @if($quotation->status !== 'Sent')
                            @can('quotation-send-whatsapp')
                                <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block"
                                            onclick="return confirm('Send quotation via WhatsApp?')">
                                        <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                    </button>
                                </form>
                            @endcan
                        @endif
                    @endif

                    @can('quotation-delete')
                        <form method="POST" action="{{ route('quotations.delete', $quotation) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block"
                                    onclick="return confirm('Are you sure you want to delete this quotation?')">
                                <i class="fas fa-trash"></i> Delete Quotation
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Company Quotes -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Company Quotes 
                        @if($quotation->quotationCompanies->count() > 0)
                            <span class="badge badge-info">{{ $quotation->quotationCompanies->count() }}</span>
                        @endif
                    </h6>
                    @if($quotation->quotationCompanies->count() > 0 && $quotation->bestQuote())
                        <div class="text-right">
                            <small class="text-muted">Best Quote:</small><br>
                            <strong class="text-success">{{ $quotation->bestQuote()->getFormattedPremium() }}</strong>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($quotation->quotationCompanies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Insurance Company</th>
                                        <th>Plan Name</th>
                                        <th>Basic OD</th>
                                        <th>Add-on</th>
                                        <th>CNG/LPG</th>
                                        <th>Net Premium</th>
                                        <th>GST</th>
                                        <th>Final Premium</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->quotationCompanies->sortBy('ranking') as $company)
                                        <tr class="{{ $company->is_recommended ? 'table-success' : '' }}">
                                            <td>
                                                @if($company->is_recommended)
                                                    <span class="badge badge-success">⭐ {{ $company->ranking }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $company->ranking }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $company->insuranceCompany->name }}</strong><br>
                                                <small class="text-muted">
                                                    @if($company->quote_number)
                                                        Quote: {{ $company->quote_number }}
                                                    @else
                                                        Auto-generated
                                                    @endif
                                                </small>
                                            </td>
                                            <td>{{ $company->plan_name ?? 'Standard Plan' }}</td>
                                            <td>₹{{ number_format($company->basic_od_premium) }}</td>
                                            <td>₹{{ number_format($company->total_addon_premium) }}</td>
                                            <td>₹{{ number_format($company->cng_lpg_premium ?? 0) }}</td>
                                            <td>₹{{ number_format($company->net_premium) }}</td>
                                            <td>₹{{ number_format($company->sgst_amount + $company->cgst_amount) }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $company->getFormattedPremium() }}</strong>
                                                @if($company->roadside_assistance > 0)
                                                    <br><small class="text-muted">+RSA</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Add-on Coverage Breakdown -->
                        @if($quotation->quotationCompanies->where('total_addon_premium', '>', 0)->count() > 0)
                            <div class="mt-4">
                                <h6 class="font-weight-bold text-success">
                                    <i class="fas fa-plus-circle"></i> Add-on Coverage Breakdown
                                </h6>
                                <div class="row">
                                    @foreach($quotation->quotationCompanies->where('total_addon_premium', '>', 0) as $company)
                                        <div class="col-md-12 mb-4">
                                            <div class="card border-left-success">
                                                <div class="card-header bg-success text-white py-2">
                                                    <h6 class="m-0 font-weight-bold">
                                                        {{ $company->insuranceCompany->name }}
                                                        @if($company->quote_number)
                                                            <small class="ml-2">({{ $company->quote_number }})</small>
                                                        @endif
                                                        <span class="float-right">Total: ₹{{ number_format($company->total_addon_premium) }}</span>
                                                    </h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    @if($company->addon_covers_breakdown)
                                                        <div class="row">
                                                            @php
                                                                $addonCount = 0;
                                                                $addonsWithPrice = collect($company->addon_covers_breakdown)->filter(function($data) {
                                                                    return (is_array($data) && isset($data['price']) && $data['price'] > 0) || 
                                                                           (is_numeric($data) && $data > 0);
                                                                });
                                                            @endphp
                                                            @foreach($company->addon_covers_breakdown as $addon => $data)
                                                                @if(is_array($data) && isset($data['price']) && $data['price'] > 0)
                                                                    @php $addonCount++; @endphp
                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <strong class="small text-primary">{{ $addon }}:</strong>
                                                                                <strong class="small">₹{{ number_format($data['price']) }}</strong>
                                                                            </div>
                                                                            @if(!empty($data['note']))
                                                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                                                    <em>{{ $data['note'] }}</em>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    @if($addonCount % 3 == 0 && $addonCount < $addonsWithPrice->count())
                                                                        </div><div class="row">
                                                                    @endif
                                                                @elseif(is_numeric($data) && $data > 0)
                                                                    @php $addonCount++; @endphp
                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <strong class="small text-primary">{{ $addon }}:</strong>
                                                                                <strong class="small">₹{{ number_format($data) }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if($addonCount % 3 == 0 && $addonCount < $addonsWithPrice->count())
                                                                        </div><div class="row">
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
                            <h5 class="text-muted">No Company Quotes Generated</h5>
                            <p class="text-muted">Generate quotes from multiple insurance companies to compare premiums and coverage.</p>
                            @can('quotation-generate')
                                <form method="POST" action="{{ route('quotations.generate-quotes', $quotation) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-cog"></i> Generate Quotes Now
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection