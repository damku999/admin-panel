@extends('layouts.app')

@section('title', 'Claim Details - ' . ($claim->insurance_claim_number ?: 'ID: ' . $claim->id))

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-medical"></i> Claim Details
                </h1>
                <small class="text-muted">{{ $claim->insurance_claim_number ?: 'ID: ' . $claim->id }}</small>
            </div>
            <div class="d-flex gap-2">
                @if (auth()->user()->hasPermissionTo('claim-edit'))
                    <a href="{{ route('claims.edit', $claim) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit Claim
                    </a>
                @endif
                <a href="{{ route('claims.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <div class="row">
            <!-- Claim Information -->
            <div class="col-lg-8">
                <!-- Basic Claim Info -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Claim Information
                            </h6>
                            <div>
                                @php
                                    $statusColors = [
                                        'Initiated' => 'warning',
                                        'Documents Collected' => 'info',
                                        'Submitted to Insurance' => 'primary',
                                        'Under Review' => 'secondary',
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                        'Closed' => 'dark'
                                    ];
                                    $color = $statusColors[$claim->claim_status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }} badge-lg">{{ $claim->claim_status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Insurance Claim Number:</th>
                                        <td>{{ $claim->insurance_claim_number ?: 'Not assigned yet' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Insurance Type:</th>
                                        <td>
                                            <span class="badge badge-{{ $claim->insurance_type == 'Health' ? 'success' : 'info' }}">
                                                {{ $claim->insurance_type }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Customer:</th>
                                        <td>{{ $claim->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile:</th>
                                        <td>{{ $claim->customer->mobile_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Policy Number:</th>
                                        <td>{{ $claim->policy_no ?: 'N/A' }}</td>
                                    </tr>
                                    @if($claim->vehicle_number)
                                        <tr>
                                            <th>Vehicle Number:</th>
                                            <td>{{ $claim->vehicle_number }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Incident Date:</th>
                                        <td>{{ $claim->incident_date ? $claim->incident_date->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Intimation Date:</th>
                                        <td>{{ $claim->intimation_date ? $claim->intimation_date->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Claim Amount:</th>
                                        <td>
                                            @if($claim->claim_amount)
                                                <strong>₹{{ number_format($claim->claim_amount, 0) }}</strong>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created Date:</th>
                                        <td>{{ $claim->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $claim->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($claim->description)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="font-weight-bold">Description:</h6>
                                    <p class="mb-0">{{ $claim->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Insurance Type Specific Details -->
                @if($claim->isHealthInsurance())
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-heartbeat"></i> Health Insurance Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Patient Name:</th>
                                            <td>{{ $claim->patient_name ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Patient Age:</th>
                                            <td>{{ $claim->patient_age ? $claim->patient_age . ' years' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Patient Relation:</th>
                                            <td>{{ $claim->patient_relation ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hospital Name:</th>
                                            <td>{{ $claim->hospital_name ?: 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Admission Date:</th>
                                            <td>{{ $claim->admission_date ? $claim->admission_date->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discharge Date:</th>
                                            <td>{{ $claim->discharge_date ? $claim->discharge_date->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Treatment Duration:</th>
                                            <td>
                                                @if($claim->admission_date && $claim->discharge_date)
                                                    {{ $claim->admission_date->diffInDays($claim->discharge_date) + 1 }} days
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if($claim->disease_diagnosis)
                                <hr>
                                <h6 class="font-weight-bold">Disease/Diagnosis:</h6>
                                <p class="mb-0">{{ $claim->disease_diagnosis }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-danger text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-truck"></i> Truck Insurance Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Driver Name:</th>
                                            <td>{{ $claim->driver_name ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Driver Contact:</th>
                                            <td>{{ $claim->driver_contact_number ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Accident Location:</th>
                                            <td>{{ $claim->accident_location ?: 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Police Station:</th>
                                            <td>{{ $claim->police_station ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>FIR Number:</th>
                                            <td>{{ $claim->fir_number ?: 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if($claim->accident_description)
                                <hr>
                                <h6 class="font-weight-bold">Accident Description:</h6>
                                <p class="mb-0">{{ $claim->accident_description }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Documents -->
                @if($claim->documents->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-folder-open"></i> Required Documents
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Document Name</th>
                                            <th>Status</th>
                                            <th>Required</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($claim->documents as $document)
                                            <tr>
                                                <td>{{ $document->document_name }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'Required' => 'warning',
                                                            'Received' => 'success',
                                                            'Not Required' => 'secondary'
                                                        ];
                                                        $color = $statusColors[$document->document_status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $color }}">{{ $document->document_status }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $document->is_mandatory ? 'danger' : 'info' }}">
                                                        {{ $document->is_mandatory ? 'Mandatory' : 'Optional' }}
                                                    </span>
                                                </td>
                                                <td>{{ $document->document_notes ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Claim Stages -->
                @if($claim->stages->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-route"></i> Claim Progress
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($claim->stages as $stage)
                                    <div class="timeline-item {{ $stage->is_current ? 'active' : '' }}">
                                        <div class="timeline-marker">
                                            @if($stage->stage_status == 'Completed')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif($stage->is_current)
                                                <i class="fas fa-clock text-warning"></i>
                                            @else
                                                <i class="fas fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ $stage->stage_name }}</h6>
                                            <p class="mb-1 text-muted">{{ $stage->stage_description }}</p>
                                            <small class="text-muted">
                                                {{ $stage->stage_date ? $stage->stage_date->format('d/m/Y H:i') : '' }}
                                                @if($stage->stage_status)
                                                    - <span class="badge badge-sm badge-{{ $stage->stage_status == 'Completed' ? 'success' : 'warning' }}">{{ $stage->stage_status }}</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs"></i> Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (auth()->user()->hasPermissionTo('claim-edit'))
                            @if (!$claim->insurance_claim_number)
                                <button type="button" class="btn btn-warning btn-sm btn-block mb-2"
                                        onclick="showAssignClaimNumberModal({{ $claim->id }})">
                                    <i class="fas fa-tag"></i> Assign Claim Number
                                </button>
                            @endif

                            @if ($claim->insurance_claim_number)
                                <button type="button" class="btn btn-success btn-sm btn-block mb-2"
                                        onclick="resendClaimNumber({{ $claim->id }})">
                                    <i class="fab fa-whatsapp"></i> Resend Claim Number
                                </button>
                            @endif

                            @if (!$claim->document_request_sent)
                                <a href="{{ route('claims.intimateDocument', $claim) }}" 
                                   class="btn btn-success btn-sm btn-block mb-2">
                                    <i class="fab fa-whatsapp"></i> Send Document List
                                </a>
                            @else
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle"></i> Document list sent
                                    <br><small>{{ $claim->document_request_sent_at ? $claim->document_request_sent_at->format('d/m/Y H:i') : '' }}</small>
                                </div>
                            @endif


                            @if ($claim->claim_status != 'Closed')
                                <button type="button" class="btn btn-secondary btn-sm btn-block mb-2"
                                        onclick="closeClaimModal({{ $claim->id }})">
                                    <i class="fas fa-times-circle"></i> Close Claim
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Customer Insurance -->
                @if($claim->customerInsurance)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-shield-alt"></i> Insurance Policy
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Insurance Company:</th>
                                    <td>{{ $claim->customerInsurance->insuranceCompany->name }}</td>
                                </tr>
                                <tr>
                                    <th>Policy Number:</th>
                                    <td>{{ $claim->customerInsurance->policy_no }}</td>
                                </tr>
                                <tr>
                                    <th>Policy Type:</th>
                                    <td>{{ $claim->customerInsurance->policyType->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Premium Amount:</th>
                                    <td>₹{{ number_format($claim->customerInsurance->premium_amount, 0) }}</td>
                                </tr>
                                <tr>
                                    <th>Policy Start:</th>
                                    <td>{{ $claim->customerInsurance->start_date ? date('d/m/Y', strtotime($claim->customerInsurance->start_date)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Policy End:</th>
                                    <td>{{ $claim->customerInsurance->expired_date ? date('d/m/Y', strtotime($claim->customerInsurance->expired_date)) : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Liability Information -->
                @if($claim->liability)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calculator"></i> Liability Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Liability Type:</th>
                                    <td>{{ $claim->liability->liability_type }}</td>
                                </tr>
                                <tr>
                                    <th>Claim Amount:</th>
                                    <td>₹{{ number_format($claim->liability->claim_amount, 0) }}</td>
                                </tr>
                                @if($claim->liability->salvage_amount > 0)
                                    <tr>
                                        <th>Salvage Amount:</th>
                                        <td>₹{{ number_format($claim->liability->salvage_amount, 0) }}</td>
                                    </tr>
                                @endif
                                @if($claim->liability->deductions > 0)
                                    <tr>
                                        <th>Deductions:</th>
                                        <td>₹{{ number_format($claim->liability->deductions, 0) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        @php
                                            $paymentColors = [
                                                'Pending' => 'warning',
                                                'Processed' => 'info',
                                                'Completed' => 'success',
                                                'Failed' => 'danger'
                                            ];
                                            $color = $paymentColors[$claim->liability->payment_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $color }}">{{ $claim->liability->payment_status }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Close Claim Modal -->
        <div class="modal fade" id="closeClaimModal" tabindex="-1" role="dialog" aria-labelledby="closeClaimModalLabel" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="closeClaimModalLabel">
                            <i class="fas fa-times-circle text-warning"></i> Close Claim
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span >&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Customer Info -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Customer Name</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">{{ $claim->customer->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Mobile Number</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">{{ $claim->customer->mobile_number ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Claim Reference</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">{{ $claim->insurance_claim_number ?: 'ID: ' . $claim->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Vehicle Number</label>
                                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">{{ $claim->vehicle_number ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Closure Reason -->
                        <div class="form-group">
                            <label for="closure_reason" class="form-label font-weight-bold">Closure Reason</label>
                            <textarea name="closure_reason" id="closure_reason" class="form-control" rows="3"
                                placeholder="Please provide the reason for closing this claim..."
                                oninput="updateClaimClosurePreview()"></textarea>
                            <small class="form-text text-muted">This reason will be included in the WhatsApp message to the customer.</small>
                        </div>

                        <!-- WhatsApp Message Preview -->
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fab fa-whatsapp text-success"></i> WhatsApp Message Preview
                            </label>
                            <div class="border rounded p-3 bg-light" style="white-space: pre-line; min-height: 120px;">
                                <div id="whatsapp-preview-close">
                                    <em class="text-muted">Enter closure reason to see message preview</em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" id="closeClaimBtn" class="btn btn-danger" onclick="submitCloseClaim()">
                            <i class="fab fa-whatsapp"></i> Close Claim & Send WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Claim Number Modal -->
        <div class="modal fade" id="assignClaimNumberModal" tabindex="-1" role="dialog" aria-labelledby="assignClaimNumberModalLabel" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignClaimNumberModalLabel">
                            <i class="fas fa-tag"></i> Assign Claim Number & Send WhatsApp
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span >&times;</span>
                        </button>
                    </div>
                    <form id="assignClaimNumberForm" method="POST" action="{{ route('claims.assignClaimNumber', $claim) }}">
                        @csrf
                        <div class="modal-body">
                            <!-- Customer Info -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Customer:</strong> {{ $claim->customer->name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Mobile:</strong> {{ $claim->customer->mobile_number }}
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <strong>Insurance Type:</strong> 
                                    <span class="badge badge-{{ $claim->insurance_type == 'Health' ? 'success' : 'info' }}">
                                        {{ $claim->insurance_type }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Vehicle/Policy:</strong> 
                                    {{ $claim->vehicle_number ?: ($claim->policy_no ?: 'N/A') }}
                                </div>
                            </div>

                            <hr>

                            <!-- Claim Number Input -->
                            <div class="form-group">
                                <label for="insurance_claim_number">Insurance Claim Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="insurance_claim_number" name="insurance_claim_number" required
                                       placeholder="Enter claim number from insurance company"
                                       oninput="updateClaimNumberPreviewShow()">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Enter the claim number provided by the insurance company
                                </small>
                            </div>

                            <!-- WhatsApp Message Preview -->
                            <div class="form-group">
                                <label>WhatsApp Message Preview:</label>
                                <div class="border p-3 bg-light rounded">
                                    <small class="text-muted">Message that will be sent to customer:</small>
                                    <div id="whatsapp-preview-show" class="mt-2" style="white-space: pre-line; font-family: monospace;">
                                        <em class="text-muted">Enter claim number to see preview</em>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>What will happen:</strong>
                                <ol class="mb-0 mt-2">
                                    <li>The claim number will be saved to the system</li>
                                    <li>WhatsApp message will be sent automatically to the customer</li>
                                    <li>You'll see confirmation of the action</li>
                                </ol>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-tag"></i> <i class="fab fa-whatsapp"></i> Assign & Send WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 50%;
        }

        .timeline-item.active .timeline-marker {
            border-color: #ffc107;
            background: #fff3cd;
        }

        .timeline-content {
            padding-left: 10px;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        function closeClaimModal(claimId) {
            // Clear form and preview
            document.getElementById('closure_reason').value = '';
            document.getElementById('whatsapp-preview-close').innerHTML = '<em class="text-muted">Enter closure reason to see message preview</em>';
            
            // Show modal with backdrop protection
            showModal('closeClaimModal', {
                closeOnBackdrop: false,
                closeOnEscape: false
            });
        }

        function updateClaimClosurePreview() {
            const closureReason = document.getElementById('closure_reason').value.trim();
            const previewDiv = document.getElementById('whatsapp-preview-close');
            
            if (!closureReason) {
                previewDiv.innerHTML = '<em class="text-muted">Enter closure reason to see message preview</em>';
                return;
            }
            
            // Generate preview message using the backend data
            const customerName = '{{ $claim->customer->name }}';
            const claimReference = '{{ $claim->insurance_claim_number ?: "ID: " . $claim->id }}';
            const vehicleNumber = '{{ $claim->vehicle_number }}';
            const vehicleText = vehicleNumber ? ` for vehicle number *${vehicleNumber}*` : '';
            
            const advisorName = '{{ \App\Services\AppSettingService::get("insurance_advisor_name", "Parth Rawal") }}';
            const website = '{{ \App\Services\AppSettingService::get("business_website", "https://parthrawal.in") }}';
            const tagline = '{{ \App\Services\AppSettingService::get("business_tagline", "Think of Insurance, Think of Us.") }}';
            const contactPhone = '{{ \App\Services\AppSettingService::get("contact_phone", "+919727793123") }}';
            
            const message = `Dear *${customerName}*,

Your Claim *${claimReference}*${vehicleText} has been closed.

*Closure Reason:* ${closureReason}

If you have any questions regarding this claim closure, please feel free to contact us.

Best regards,
${advisorName}
${website}
Your Trusted Insurance Advisor
"${tagline}"
${contactPhone}`;
            
            previewDiv.textContent = message;
        }

        function submitCloseClaim() {
            const closureReason = document.getElementById('closure_reason').value.trim();
            
            if (!closureReason) {
                show_notification('error', 'Please provide a closure reason');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('closeClaimBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Submit via AJAX
            $.ajax({
                url: `{{ route('claims.closeClaim', $claim->id) }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    closure_reason: closureReason,
                    send_whatsapp: true
                },
                success: function(response) {
                    if (response.success) {
                        show_notification('success', response.message);
                        hideModal('closeClaimModal');
                        
                        // Reload page after short delay to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        show_notification('error', response.message || 'Failed to close claim');
                    }
                },
                error: function(xhr) {
                    console.error('Close claim error:', xhr);
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while closing the claim';
                    show_notification('error', errorMessage);
                },
                complete: function() {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        function showAssignClaimNumberModal(claimId) {
            // Clear form and show modal
            document.getElementById('insurance_claim_number').value = '';
            document.getElementById('whatsapp-preview-show').innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
            
            // Show modal with backdrop protection
            showModal('assignClaimNumberModal', {
                closeOnBackdrop: false,
                closeOnEscape: false
            });
        }

        function updateClaimNumberPreviewShow() {
            const claimNumber = document.getElementById('insurance_claim_number').value.trim();
            const previewDiv = document.getElementById('whatsapp-preview-show');
            
            if (claimNumber) {
                let message = `Dear *{{ $claim->customer->name }}*,\n\n`;
                message += `Your Claim Number *${claimNumber}* is generated`;
                
                @if($claim->vehicle_number)
                message += ` against your vehicle number *{{ $claim->vehicle_number }}*`;
                @endif
                
                message += `. For further assistance kindly contact me.\n\n`;
                message += `Best regards,\n`;
                message += `{{ \App\Services\AppSettingService::get('insurance_advisor_name', 'Parth Rawal') }}\n`;
                message += `{{ \App\Services\AppSettingService::get('contact_phone', '+919727793123') }}`;
                
                previewDiv.innerHTML = message;
            } else {
                previewDiv.innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
            }
        }

        function resendClaimNumber(claimId) {
            showConfirmationModal({
                title: 'Resend WhatsApp Message',
                message: 'Are you sure you want to resend the claim number via WhatsApp?',
                confirmText: 'Yes, Resend',
                confirmClass: 'btn-success',
                onConfirm: function() {
                    window.location.href = `{{ url('/claims') }}/${claimId}/resend-claim-number`;
                }
            });
        }
    </script>
@endsection