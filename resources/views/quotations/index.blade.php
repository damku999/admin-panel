@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt"></i> Insurance Quotations Management
                    </h6>
                    @can('quotation-create')
                    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Quotation
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('quotations.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by customer name, mobile, vehicle number..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Generated" {{ request('status') == 'Generated' ? 'selected' : '' }}>Generated</option>
                                    <option value="Sent" {{ request('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="Accepted" {{ request('status') == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <div class="col-md-3 text-right">
                                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Quotations Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Quote Ref</th>
                                    <th>Customer</th>
                                    <th>Vehicle Details</th>
                                    <th>Policy Type</th>
                                    <th>Best Quote</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $quotation)
                                    <tr>
                                        <td>
                                            <strong>{{ $quotation->getQuoteReference() }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $quotation->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $quotation->customer->mobile_number }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $quotation->make_model_variant }}</strong><br>
                                            <small class="text-muted">
                                                {{ $quotation->vehicle_number ?? 'To be registered' }} | 
                                                {{ $quotation->fuel_type }} | 
                                                IDV: ₹{{ number_format($quotation->total_idv) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $quotation->policy_type }}</span><br>
                                            <small>{{ $quotation->policy_tenure_years }} Year(s)</small>
                                        </td>
                                        <td>
                                            @if($quotation->bestQuote())
                                                <strong class="text-success">{{ $quotation->bestQuote()->getFormattedPremium() }}</strong><br>
                                                <small class="text-muted">{{ $quotation->bestQuote()->insuranceCompany->name }}</small>
                                            @else
                                                <span class="text-muted">Not generated</span>
                                            @endif
                                        </td>
                                        <td>
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
                                            @if($quotation->sent_at)
                                                <br><small class="text-muted">
                                                    Sent: {{ $quotation->sent_at->format('M d, Y H:i') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $quotation->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $quotation->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm">
                                                @can('quotation-edit')
                                                <a href="{{ route('quotations.show', $quotation) }}" 
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('quotations.edit', $quotation) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit Quotation">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                
                                                @if($quotation->quotationCompanies->count() > 0)
                                                    @can('quotation-download-pdf')
                                                    <a href="{{ route('quotations.download-pdf', $quotation) }}" 
                                                       class="btn btn-primary btn-sm" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @endcan
                                                    
                                                    @if($quotation->status !== 'Sent')
                                                        @can('quotation-send-whatsapp')
                                                        <form method="POST" action="{{ route('quotations.send-whatsapp', $quotation) }}" 
                                                              style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" 
                                                                    title="Send via WhatsApp"
                                                                    onclick="return confirm('Send quotation via WhatsApp?')">
                                                                <i class="fab fa-whatsapp"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    @endif
                                                @else
                                                    @can('quotation-generate')
                                                    <form method="POST" action="{{ route('quotations.generate-quotes', $quotation) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm" 
                                                                title="Generate Quotes">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                @endif
                                                
                                                @can('quotation-delete')
                                                <form method="POST" action="{{ route('quotations.delete', $quotation) }}" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            title="Delete Quotation"
                                                            onclick="return confirm('Are you sure you want to delete this quotation?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No quotations found</h5>
                                            <p class="text-muted">Start by creating your first insurance quotation.</p>
                                            @can('quotation-create')
                                            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Create Quotation
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($quotations->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $quotations->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection