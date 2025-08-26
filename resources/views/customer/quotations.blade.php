@extends('layouts.customer')

@section('title', 'Customer Quotations')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            @if($isHead)
                Family Quotations
            @else
                Your Quotations
            @endif
        </h1>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if($quotations->count() > 0)
    <!-- Quotations List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-calculator mr-2"></i>
                @if($isHead)
                    All Family Quotations ({{ $quotations->count() }} Total)
                @else
                    Your Quotations ({{ $quotations->count() }} Total)
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Quote Reference</th>
                            <th>Policy Holder</th>
                            <th>Vehicle Details</th>
                            <th>Total IDV</th>
                            <th>Best Quote</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $quotation)
                        <tr class="{{ $quotation->customer_id === $customer->id ? 'table-light' : '' }}">
                            <td>
                                <strong>{{ $quotation->getQuoteReference() }}</strong>
                            </td>
                            <td>
                                {{ $quotation->customer->name }}
                                @if($quotation->customer_id === $customer->id)
                                    <span class="badge badge-info ml-1">You</span>
                                @endif
                            </td>
                            <td>
                                @if($quotation->vehicle_number)
                                    <strong>{{ $quotation->vehicle_number }}</strong><br>
                                    <small class="text-muted">{{ $quotation->make_model_variant }}</small>
                                @else
                                    <small class="text-muted">General Insurance</small>
                                @endif
                            </td>
                            <td>
                                @if($quotation->total_idv)
                                    ₹{{ number_format($quotation->total_idv, 2) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($quotation->bestQuote())
                                    <strong>₹{{ number_format($quotation->bestQuote()->final_premium, 2) }}</strong><br>
                                    <small class="text-muted">{{ $quotation->bestQuote()->insuranceCompany->name ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">No quotes available</span>
                                @endif
                            </td>
                            <td>
                                @if($quotation->status == 'sent')
                                    <span class="badge badge-success">Sent</span>
                                @elseif($quotation->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($quotation->status ?? 'Draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $quotation->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('customer.quotations.detail', $quotation->id) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($quotation->quotationCompanies->count() > 0)
                                        <a href="{{ route('customer.quotations.download', $quotation->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> PDF
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
    <!-- No Quotations -->
    <div class="card border-left-warning shadow mb-4">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">No Quotations Found</div>
                    <div class="text-gray-800">
                        @if($customer->hasFamily())
                            No insurance quotations found for your family.
                        @else
                            You don't have any insurance quotations or are not part of a family group.
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-calculator fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Information Card -->
    <div class="card border-left-info shadow">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">About Quotations</div>
                    <div class="text-gray-800">
                        <small>
                            <strong>Read-Only Access:</strong> You can view quotation details but cannot make changes. 
                            Contact your insurance agent or admin for quotation modifications or to generate new quotes.
                        </small>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
@endsection