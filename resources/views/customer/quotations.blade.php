@extends('layouts.customer')

@section('title', 'Customer Quotations')

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        @if ($quotations->count() > 0)
        <!-- Quotations Header -->
        <div class="card mb-4 fade-in-scale" style="animation-delay: 0ms">
            <div class="card-header bg-success">
                <h5 class="mb-0 text-white fw-bold">
                    <i class="fas fa-calculator me-2"></i>
                    @if ($isHead)
                        Family Insurance Quotations
                    @else
                        Your Insurance Quotations
                    @endif
                    <span class="badge bg-light text-success ms-2">{{ $quotations->count() }} Total</span>
                </h5>
            </div>
        </div>

        <!-- Quotations Table -->
        <div class="card fade-in-scale" style="animation-delay: 100ms">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>Quote Reference</th>
                                <th><i class="fas fa-user me-1"></i>Policy Holder</th>
                                <th><i class="fas fa-car me-1"></i>Vehicle Details</th>
                                <th><i class="fas fa-shield-alt me-1"></i>Total IDV</th>
                                <th><i class="fas fa-rupee-sign me-1"></i>Best Quote</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-calendar me-1"></i>Created</th>
                                <th><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotations as $quotation)
                                <tr class="{{ $quotation->customer_id === $customer->id ? 'table-light' : '' }}">
                                    <td>
                                        <strong class="text-success">{{ $quotation->getQuoteReference() }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $quotation->customer->name }}</strong>
                                            @if ($quotation->customer_id === $customer->id)
                                                <br><span class="badge bg-success">You</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($quotation->vehicle_number)
                                            <div>
                                                <strong class="text-dark">{{ $quotation->vehicle_number }}</strong>
                                                <br><small class="text-muted">{{ $quotation->make_model_variant }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">General Insurance</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($quotation->total_idv)
                                            <strong class="text-success">₹{{ number_format($quotation->total_idv, 0) }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($quotation->bestQuote())
                                            <div>
                                                <strong class="text-success">₹{{ number_format($quotation->bestQuote()->final_premium, 0) }}</strong>
                                                <br><small class="text-muted">{{ $quotation->bestQuote()->insuranceCompany->name ?? 'N/A' }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No quotes</span>
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ formatDateForUi($quotation->created_at) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('customer.quotations.detail', $quotation->id) }}" 
                                               class="btn btn-success btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($quotation->quotationCompanies->count() > 0)
                                                <a href="{{ route('customer.quotations.download', $quotation->id) }}" 
                                                   class="btn btn-outline-success btn-sm" title="Download PDF">
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
        @else
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h4>No Quotations Found</h4>
                        <p class="text-muted">
                            @if ($isHead)
                                No insurance quotations found for your family.
                            @else
                                You don't have any insurance quotations yet.
                            @endif
                        </p>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-success">
                            <i class="fas fa-home me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Information Card -->
        <div class="card mt-4">
            <div class="card-header bg-info">
                <h6 class="mb-0 text-white fw-bold">
                    <i class="fas fa-info-circle me-2"></i>About Quotations
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <strong>Read-Only Access:</strong> You can view quotation details but cannot make changes.
                    Contact your insurance agent or admin for quotation modifications or to generate new quotes.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize animations
    $(document).ready(function() {
        $('.card').each(function(index) {
            if (!$(this).hasClass('fade-in-scale')) {
                $(this).css('animation-delay', (index * 150) + 'ms').addClass('fade-in-scale');
            }
        });
    });
</script>
@endpush
