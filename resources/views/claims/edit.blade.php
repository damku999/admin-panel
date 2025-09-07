@extends('layouts.app')

@section('title', 'Edit Claim - ' . ($claim->insurance_claim_number ?: 'ID: ' . $claim->id))

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <div class="row">
            <!-- Claim Form -->
            <div class="col-lg-8">
                <div class="card shadow mb-1">
                    <div class="card-header py-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Claim: {{ $claim->insurance_claim_number ?: 'ID: ' . $claim->id }}</h6>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('claims.show', $claim) }}" class="btn btn-info btn-sm mr-1" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('claims.index') }}" onclick="window.history.go(-1); return false;"
                                    class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('claims.update', $claim) }}" id="claimForm">
                            @csrf
                            @method('PUT')

                            <!-- Claim Information -->
                            <div class="row">
                                <div class="col-12">
                            <div class="card border-left-primary mb-1">
                                <div class="card-header bg-primary text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-file-medical-alt"></i> Claim Information</h6>
                                </div>
                                <div class="card-body p-2">
                                    <!-- Form Fields Row -->
                                    <div class="row mb-1">
                                        <!-- Insurance Type -->
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="insurance_type" class="form-label text-sm">
                                                    <span class="text-danger">*</span> Insurance Type
                                                </label>
                                                <select name="insurance_type" id="insurance_type" required class="form-control form-control-sm">
                                                    <option value="">Select Insurance Type</option>
                                                    <option value="Health" {{ old('insurance_type', $claim->insurance_type) == 'Health' ? 'selected' : '' }}>Health Insurance</option>
                                                    <option value="Truck" {{ old('insurance_type', $claim->insurance_type) == 'Truck' ? 'selected' : '' }}>Truck Insurance</option>
                                                </select>
                                                @error('insurance_type')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Universal Search Field -->
                                        <div class="col-md-5">
                                            <div class="form-group position-relative">
                                                <label for="universal_search_input" id="search_label" class="form-label text-sm">Search by Policy Number, Customer Name, or Mobile Number</label>
                                                <input type="text" id="universal_search_input" class="form-control form-control-sm"
                                                    placeholder="Enter policy number, customer name, or mobile..."
                                                    value="{{ old('policy_no', $claim->policy_no) ?: old('vehicle_number', $claim->vehicle_number) }}"
                                                    autocomplete="off">
                                                <div id="universal_search_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" style="top: 100%; left: 0; z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                                            </div>
                                        </div>

                                        <!-- Incident Date -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="incident_date" class="form-label text-sm">Incident Date</label>
                                                <input type="date" name="incident_date" id="incident_date" class="form-control form-control-sm"
                                                    value="{{ old('incident_date', $claim->incident_date ? $claim->incident_date->format('Y-m-d') : date('Y-m-d')) }}">
                                                @error('incident_date')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden fields for form submission -->
                                    <input type="hidden" name="policy_no" id="policy_no" value="{{ old('policy_no', $claim->policy_no) }}">
                                    <input type="hidden" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number', $claim->vehicle_number) }}">
                                    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $claim->customer_id) }}">
                                    <input type="hidden" name="customer_insurance_id" id="customer_insurance_id" value="{{ old('customer_insurance_id', $claim->customer_insurance_id) }}">

                                    <!-- Auto-populated Policy Details -->
                                    <div class="row" id="policy_details_section" style="display: {{ $claim->customer_insurance_id ? 'block' : 'none' }};">
                                        <div class="col-12">
                                            <div class="card border-left-info mb-1">
                                                <div class="card-header bg-info text-white py-1">
                                                    <h6 class="m-0"><i class="fas fa-info-circle"></i> Policy Details <small>(Auto-populated)</small></h6>
                                                </div>
                                                <div class="card-body bg-light" style="font-size: 13px;">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12 mb-1">
                                                            <div class="row">
                                                                <div class="col-4"><strong>Customer:</strong></div>
                                                                <div class="col-8" id="display_customer_name">{{ $claim->customer->name ?? '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Mobile:</strong></div>
                                                                <div class="col-8" id="display_customer_mobile">{{ $claim->customer->mobile_number ?? '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Email:</strong></div>
                                                                <div class="col-8" id="display_customer_email">{{ $claim->customer->email ?? '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Insurance:</strong></div>
                                                                <div class="col-8" id="display_insurance_company">{{ $claim->customerInsurance->insuranceCompany->name ?? '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Policy Type:</strong></div>
                                                                <div class="col-8" id="display_policy_type">{{ $claim->customerInsurance->policyType->name ?? '' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12 mb-1">
                                                            <div class="row">
                                                                <div class="col-4"><strong>Sum Insured:</strong></div>
                                                                <div class="col-8" id="display_sum_insured">₹{{ $claim->customerInsurance->sum_insured ?? 0 }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Policy Start:</strong></div>
                                                                <div class="col-8" id="display_policy_start">{{ $claim->customerInsurance->start_date ? date('d-m-Y', strtotime($claim->customerInsurance->start_date)) : '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Policy End:</strong></div>
                                                                <div class="col-8" id="display_policy_end">{{ $claim->customerInsurance->expired_date ? date('d-m-Y', strtotime($claim->customerInsurance->expired_date)) : '' }}</div>
                                                            </div>
                                                            <div class="row mt-1">
                                                                <div class="col-4"><strong>Vehicle:</strong></div>
                                                                <div class="col-8" id="display_vehicle_info">
                                                                    <span id="display_vehicle_make">{{ $claim->customerInsurance->vehicle_make ?? '' }}</span>
                                                                    <span id="display_vehicle_model">{{ $claim->customerInsurance->vehicle_model ?? '' }}</span>
                                                                    <span id="display_vehicle_year">({{ $claim->customerInsurance->vehicle_year ?? '' }})</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Additional Claim Fields -->
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="claim_amount" class="form-label text-sm">Claim Amount (₹)</label>
                                                <input type="number" name="claim_amount" id="claim_amount"
                                                    class="form-control form-control-sm @error('claim_amount') is-invalid @enderror"
                                                    value="{{ old('claim_amount', $claim->claim_amount) }}" min="0" step="1"
                                                    placeholder="0">
                                                @error('claim_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="intimation_date" class="form-label text-sm">Intimation Date</label>
                                                <input type="date" name="intimation_date" id="intimation_date"
                                                    class="form-control form-control-sm @error('intimation_date') is-invalid @enderror"
                                                    value="{{ old('intimation_date', $claim->intimation_date ? $claim->intimation_date->format('Y-m-d') : '') }}">
                                                @error('intimation_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="claim_status" class="form-label text-sm">Claim Status</label>
                                                <select name="claim_status" id="claim_status"
                                                    class="form-control form-control-sm @error('claim_status') is-invalid @enderror">
                                                    <option value="Initiated" {{ old('claim_status', $claim->claim_status) == 'Initiated' ? 'selected' : '' }}>Initiated</option>
                                                    <option value="Documents Collected" {{ old('claim_status', $claim->claim_status) == 'Documents Collected' ? 'selected' : '' }}>Documents Collected</option>
                                                    <option value="Submitted to Insurance" {{ old('claim_status', $claim->claim_status) == 'Submitted to Insurance' ? 'selected' : '' }}>Submitted to Insurance</option>
                                                    <option value="Under Review" {{ old('claim_status', $claim->claim_status) == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="Approved" {{ old('claim_status', $claim->claim_status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="Rejected" {{ old('claim_status', $claim->claim_status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="Closed" {{ old('claim_status', $claim->claim_status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                                @error('claim_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <div class="form-group">
                                                <label for="insurance_claim_number" class="form-label text-sm">Insurance Claim Number</label>
                                                <input type="text" name="insurance_claim_number" id="insurance_claim_number"
                                                    class="form-control form-control-sm @error('insurance_claim_number') is-invalid @enderror"
                                                    value="{{ old('insurance_claim_number', $claim->insurance_claim_number) }}"
                                                    placeholder="Claim number from insurance company">
                                                @error('insurance_claim_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Health Insurance Specific Fields -->
                    <div class="row" id="health_fields" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-success mb-1">
                                <div class="card-header bg-success text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-heartbeat"></i> Health Insurance Details</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="patient_name" class="form-label text-sm">Patient Name <span class="text-danger health-required">*</span></label>
                                                <input type="text" name="patient_name" id="patient_name"
                                                    class="form-control form-control-sm @error('patient_name') is-invalid @enderror"
                                                    value="{{ old('patient_name', $claim->patient_name) }}">
                                                @error('patient_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="patient_age" class="form-label text-sm">Patient Age</label>
                                                <input type="number" name="patient_age" id="patient_age"
                                                    class="form-control form-control-sm @error('patient_age') is-invalid @enderror"
                                                    value="{{ old('patient_age', $claim->patient_age) }}" min="0">
                                                @error('patient_age')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="patient_relation" class="form-label text-sm">Patient Relation</label>
                                                <select name="patient_relation" id="patient_relation"
                                                    class="form-control form-control-sm @error('patient_relation') is-invalid @enderror">
                                                    <option value="">Select Relation</option>
                                                    <option value="Self" {{ old('patient_relation', $claim->patient_relation) == 'Self' ? 'selected' : '' }}>Self</option>
                                                    <option value="Spouse" {{ old('patient_relation', $claim->patient_relation) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                                    <option value="Son" {{ old('patient_relation', $claim->patient_relation) == 'Son' ? 'selected' : '' }}>Son</option>
                                                    <option value="Daughter" {{ old('patient_relation', $claim->patient_relation) == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                                    <option value="Father" {{ old('patient_relation', $claim->patient_relation) == 'Father' ? 'selected' : '' }}>Father</option>
                                                    <option value="Mother" {{ old('patient_relation', $claim->patient_relation) == 'Mother' ? 'selected' : '' }}>Mother</option>
                                                    <option value="Other" {{ old('patient_relation', $claim->patient_relation) == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('patient_relation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="hospital_name" class="form-label text-sm">Hospital Name</label>
                                                <input type="text" name="hospital_name" id="hospital_name"
                                                    class="form-control form-control-sm @error('hospital_name') is-invalid @enderror"
                                                    value="{{ old('hospital_name', $claim->hospital_name) }}">
                                                @error('hospital_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="admission_date" class="form-label text-sm">Admission Date <span class="text-danger health-required">*</span></label>
                                                <input type="date" name="admission_date" id="admission_date"
                                                    class="form-control form-control-sm @error('admission_date') is-invalid @enderror"
                                                    value="{{ old('admission_date', $claim->admission_date ? $claim->admission_date->format('Y-m-d') : '') }}">
                                                @error('admission_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="discharge_date" class="form-label text-sm">Discharge Date</label>
                                                <input type="date" name="discharge_date" id="discharge_date"
                                                    class="form-control form-control-sm @error('discharge_date') is-invalid @enderror"
                                                    value="{{ old('discharge_date', $claim->discharge_date ? $claim->discharge_date->format('Y-m-d') : '') }}">
                                                @error('discharge_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-1">
                                            <div class="form-group">
                                                <label for="disease_diagnosis" class="form-label text-sm">Disease/Diagnosis</label>
                                                <textarea name="disease_diagnosis" id="disease_diagnosis"
                                                    class="form-control form-control-sm @error('disease_diagnosis') is-invalid @enderror"
                                                    rows="2" placeholder="Describe the medical condition...">{{ old('disease_diagnosis', $claim->disease_diagnosis) }}</textarea>
                                                @error('disease_diagnosis')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Truck Insurance Specific Fields -->
                    <div class="row" id="truck_fields" style="display: none;">
                        <div class="col-12">
                            <div class="card border-left-danger mb-1">
                                <div class="card-header bg-danger text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-truck"></i> Truck Insurance Details</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="driver_name" class="form-label text-sm">Driver Name</label>
                                                <input type="text" name="driver_name" id="driver_name"
                                                    class="form-control form-control-sm @error('driver_name') is-invalid @enderror"
                                                    value="{{ old('driver_name', $claim->driver_name) }}">
                                                @error('driver_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="driver_contact_number" class="form-label text-sm">Driver Contact <span class="text-danger truck-required">*</span></label>
                                                <input type="text" name="driver_contact_number" id="driver_contact_number"
                                                    class="form-control form-control-sm @error('driver_contact_number') is-invalid @enderror"
                                                    value="{{ old('driver_contact_number', $claim->driver_contact_number) }}">
                                                @error('driver_contact_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-group">
                                                <label for="accident_location" class="form-label text-sm">Accident Location</label>
                                                <input type="text" name="accident_location" id="accident_location"
                                                    class="form-control form-control-sm @error('accident_location') is-invalid @enderror"
                                                    value="{{ old('accident_location', $claim->accident_location) }}">
                                                @error('accident_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <div class="form-group">
                                                <label for="police_station" class="form-label text-sm">Police Station</label>
                                                <input type="text" name="police_station" id="police_station"
                                                    class="form-control form-control-sm @error('police_station') is-invalid @enderror"
                                                    value="{{ old('police_station', $claim->police_station) }}">
                                                @error('police_station')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <div class="form-group">
                                                <label for="fir_number" class="form-label text-sm">FIR Number</label>
                                                <input type="text" name="fir_number" id="fir_number"
                                                    class="form-control form-control-sm @error('fir_number') is-invalid @enderror"
                                                    value="{{ old('fir_number', $claim->fir_number) }}">
                                                @error('fir_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-1">
                                            <div class="form-group">
                                                <label for="accident_description" class="form-label text-sm">Accident Description <span class="text-danger truck-required">*</span></label>
                                                <textarea name="accident_description" id="accident_description"
                                                    class="form-control form-control-sm @error('accident_description') is-invalid @enderror"
                                                    rows="3" placeholder="Describe how the accident occurred...">{{ old('accident_description', $claim->accident_description) }}</textarea>
                                                @error('accident_description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-secondary mb-1">
                                <div class="card-header bg-secondary text-white py-1">
                                    <h6 class="m-0"><i class="fas fa-sticky-note"></i> Additional Information</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="form-group">
                                        <label for="description" class="form-label text-sm">Description/Notes</label>
                                        <textarea name="description" id="description"
                                            class="form-control form-control-sm @error('description') is-invalid @enderror"
                                            rows="4" placeholder="Any additional information about the claim...">{{ old('description', $claim->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                            <!-- Form Actions -->
                        </form>
                    </div>
                    <div class="card-footer p-2">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('claims.show', $claim) }}" class="btn btn-secondary btn-sm mr-2">Cancel</a>
                            <button type="submit" form="claimForm" class="btn btn-primary btn-sm" id="submitBtn">Update Claim</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Actions -->
            <div class="col-lg-4 col-md-6 mb-1">
                <!-- Actions Card -->
                <div class="card shadow mb-1">
                    <div class="card-header py-1">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tools"></i> Quick Actions
                        </h6>
                    </div>
                    <div class="card-body p-2">
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

                            @if ($claim->claim_status != 'Closed')
                                <button type="button" class="btn btn-secondary btn-sm btn-block mb-2"
                                        onclick="closeClaimModal({{ $claim->id }})">
                                    <i class="fas fa-times-circle"></i> Close Claim
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Claim Info Card -->
                <div class="card shadow mb-1">
                    <div class="card-header py-1">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Claim Status
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="text-center">
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
                            <h4><span class="badge badge-{{ $color }}">{{ $claim->claim_status }}</span></h4>
                            @if($claim->insurance_claim_number)
                                <p class="mb-1"><strong>Claim Number:</strong></p>
                                <p class="text-muted">{{ $claim->insurance_claim_number }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Close Claim Modal -->
        <x-modals.form-modal 
            id="closeClaimModal" 
            title="Close Claim"
            size="lg"
            :show-footer="true">
            
            <x-slot name="title">
                <i class="fas fa-times-circle text-warning"></i> Close Claim
            </x-slot>
            
            <x-slot name="body">
                        <!-- Customer Info -->
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Customer Name</label>
                                    <p class="form-control-plaintext border rounded px-3 py-1 bg-light">{{ $claim->customer->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Mobile Number</label>
                                    <p class="form-control-plaintext border rounded px-3 py-1 bg-light">{{ $claim->customer->mobile_number ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Claim Reference</label>
                                    <p class="form-control-plaintext border rounded px-3 py-1 bg-light">{{ $claim->insurance_claim_number ?: 'ID: ' . $claim->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Vehicle Number</label>
                                    <p class="form-control-plaintext border rounded px-3 py-1 bg-light">{{ $claim->vehicle_number ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Closure Reason -->
                        <div class="form-group">
                            <label for="closure_reason" class="form-label font-weight-bold">Closure Reason</label>
                            <textarea name="closure_reason" id="closure_reason" class="form-control form-control-sm" rows="3"
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
            </x-slot>
            
            <x-slot name="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="closeClaimBtn" class="btn btn-danger" onclick="submitCloseClaim()">
                    <i class="fab fa-whatsapp"></i> Close Claim & Send WhatsApp
                </button>
            </x-slot>
        </x-modals.form-modal>

        <!-- Assign Claim Number Modal -->
        <x-modals.form-modal 
            id="assignClaimNumberModal" 
            title="Assign Claim Number & Send WhatsApp"
            size="lg"
            :show-footer="true">
            
            <x-slot name="title">
                <i class="fas fa-tag"></i> Assign Claim Number & Send WhatsApp
            </x-slot>
            
            <x-slot name="body">
                            <!-- Customer Information Display -->
                            <div class="row mb-1">
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <strong>Customer:</strong> {{ $claim->customer->name }}
                                </div>
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <strong>Mobile:</strong> {{ $claim->customer->mobile_number ?: 'N/A' }}
                                </div>
                            </div>

                            <div class="row mb-1">
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <strong>Insurance Type:</strong> {{ $claim->insurance_type }}
                                </div>
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <strong>Vehicle/Policy:</strong> {{ $claim->vehicle_number ?: $claim->policy_no ?: 'N/A' }}
                                </div>
                            </div>

                            <hr>

                            <!-- Claim Number Input -->
                            <div class="form-group">
                                <label for="insurance_claim_number">Insurance Claim Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="insurance_claim_number" name="insurance_claim_number" required
                                       placeholder="Enter claim number from insurance company"
                                       oninput="updateClaimNumberPreview()">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Enter the claim number provided by the insurance company
                                </small>
                            </div>

                            <!-- WhatsApp Message Preview -->
                            <div class="form-group">
                                <label>WhatsApp Message Preview:</label>
                                <div class="border p-3 bg-light rounded">
                                    <small class="text-muted">Message that will be sent to customer:</small>
                                    <div id="whatsapp-preview" class="mt-2" style="white-space: pre-line; font-family: monospace;"></div>
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
            </x-slot>
            
            <x-slot name="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="assignClaimBtn" class="btn btn-warning" onclick="event.preventDefault(); submitAssignClaim(); return false;">
                    <i class="fas fa-tag"></i> <i class="fab fa-whatsapp"></i> Assign & Send WhatsApp
                </button>
            </x-slot>
        </x-modals.form-modal>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/js/claims-common.js') }}"></script>
    <style>
        .form-label {
            margin-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
    <script>
        $(document).ready(function() {
            
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Insurance Type Change Handler - Update Search Field Label
            $('#insurance_type').on('change', function() {
                const insuranceType = $(this).val();
                
                // Clear previous search and hide suggestions
                $('#universal_search_suggestions').hide().empty();
                
                // Update label and placeholder based on insurance type selection
                if (insuranceType === 'Health') {
                    $('#search_label').text('Search by Policy Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter policy number, customer name, or mobile...');
                } else if (insuranceType === 'Truck') {
                    $('#search_label').text('Search by Registration Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter registration number, customer name, or mobile...');
                } else {
                    $('#search_label').text('Search by Policy Number, Registration Number, Customer Name, or Mobile Number');
                    $('#universal_search_input').attr('placeholder', 'Enter search term...');
                }
                
                // Show/hide insurance type specific fields
                toggleInsuranceFields(insuranceType);
            });

            // Universal Search - One API for ALL fields
            let universalSearchTimeout;
            $('#universal_search_input').on('input', function() {
                let query = $(this).val().trim();
                const insuranceType = $('#insurance_type').val();
                
                // Auto-uppercase for truck insurance searches
                if (insuranceType === 'Truck' && /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/i.test(query)) {
                    query = query.toUpperCase();
                    $(this).val(query);
                }
                
                clearTimeout(universalSearchTimeout);
                
                if (query.length >= 3) {
                    universalSearchTimeout = setTimeout(() => {
                        performUniversalSearch(query);
                    }, 300);
                } else {
                    $('#universal_search_suggestions').hide().empty();
                }
            });

            // Universal Search Function
            function performUniversalSearch(query) {
                $.get('{{ route("claims.search") }}?q=' + encodeURIComponent(query))
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            showUniversalSearchSuggestions(response.data);
                        } else {
                            $('#universal_search_suggestions').hide().empty();
                        }
                    })
                    .fail(function() {
                        $('#universal_search_suggestions').hide().empty();
                    });
            }

            // Show Universal Search Suggestions
            function showUniversalSearchSuggestions(results) {
                let html = '';
                
                results.forEach(result => {
                    html += `<div class="p-2 universal-suggestion" 
                                data-policy="${result.policy_number}" 
                                data-vehicle="${result.vehicle_number || ''}"
                                data-customer-id="${result.customer_id}"
                                data-policy-id="${result.policy_id}"
                                data-insurance-type="${result.insurance_type}"
                                style="cursor: pointer; border-bottom: 1px solid #eee;">`;
                    
                    // Show what field matched
                    if (result.matched_field === 'policy') {
                        html += `<strong class="text-primary">Policy: ${result.policy_number}</strong><br>`;
                    } else if (result.matched_field === 'vehicle' && result.vehicle_number) {
                        html += `<strong class="text-success">Vehicle: ${result.vehicle_number}</strong><br>`;
                    } else if (result.matched_field === 'customer_name') {
                        html += `<strong class="text-info">Customer: ${result.customer_name}</strong><br>`;
                    } else if (result.matched_field === 'mobile') {
                        html += `<strong class="text-warning">Mobile: ${result.customer_mobile}</strong><br>`;
                    }
                    
                    // Additional info
                    if (result.matched_field !== 'policy' && result.policy_number) {
                        html += `<small>Policy: ${result.policy_number}</small><br>`;
                    }
                    if (result.matched_field !== 'vehicle' && result.vehicle_number) {
                        html += `<small>Vehicle: ${result.vehicle_number}</small><br>`;
                    }
                    if (result.matched_field !== 'customer_name') {
                        html += `<small class="text-muted">${result.customer_name} - ${result.customer_mobile}</small><br>`;
                    }
                    
                    html += `<small class="text-info">${result.insurance_company} (${result.insurance_type})</small>`;
                    html += `</div>`;
                });
                
                $('#universal_search_suggestions').html(html).show();
            }

            // Handle Universal Search Selection
            $(document).on('click', '.universal-suggestion', function() {
                const policyNumber = $(this).data('policy');
                const vehicleNumber = $(this).data('vehicle') || '';
                const customerId = $(this).data('customer-id');
                const policyId = $(this).data('policy-id');
                
                // Hide suggestions
                $('#universal_search_suggestions').hide().empty();
                
                // Set form values
                $('#universal_search_input').val(policyNumber || vehicleNumber);
                $('#policy_no').val(policyNumber);
                $('#vehicle_number').val(vehicleNumber);
                $('#customer_id').val(customerId);
                $('#customer_insurance_id').val(policyId);
                
                // Lookup full details
                lookupPolicyDetails('policy', policyNumber);
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#universal_search_input, #universal_search_suggestions').length) {
                    $('#universal_search_suggestions').hide().empty();
                }
            });

            // Lookup policy details
            function lookupPolicyDetails(type, value) {
                if (!value) return;

                $.get('{{ route("claims.lookup", ":type") }}'.replace(':type', type) + '?value=' + encodeURIComponent(value))
                    .done(function(response) {
                        if (response.success) {
                            populateFields(response.data);
                            showPolicyDetails(response.data);
                        }
                    })
                    .fail(function() {
                        // Handle error silently for edit form
                    });
            }

            // Populate fields
            function populateFields(data) {
                $('#customer_id').val(data.customer_id || '');
                $('#customer_insurance_id').val(data.policy_id || '');

                if (!$('#policy_no').val() && data.policy_number) {
                    $('#policy_no').val(data.policy_number);
                }
                if (!$('#vehicle_number').val() && data.vehicle_number) {
                    $('#vehicle_number').val(data.vehicle_number.toUpperCase());
                }

                showPolicyDetails(data);
                const userSelectedInsuranceType = $('#insurance_type').val();
                toggleInsuranceFields(userSelectedInsuranceType);
            }

            // Show policy details
            function showPolicyDetails(data) {
                $('#display_customer_name').text(data.customer_name || '-');
                $('#display_customer_mobile').text(data.customer_mobile || '-');
                $('#display_insurance_company').text(data.insurance_company || '-');
                $('#display_sum_insured').text('₹' + (data.sum_insured || 0));
                
                let policyPeriod = '';
                if (data.policy_start_date && data.policy_end_date) {
                    policyPeriod = formatDate(data.policy_start_date) + ' to ' + formatDate(data.policy_end_date);
                }
                $('#display_policy_period').text(policyPeriod || '-');
                
                let vehicleInfo = '';
                if (data.vehicle_make || data.vehicle_model || data.vehicle_year) {
                    vehicleInfo = [data.vehicle_make, data.vehicle_model, data.vehicle_year ? '(' + data.vehicle_year + ')' : ''].filter(Boolean).join(' ');
                }
                $('#display_vehicle_info').text(vehicleInfo || '-');

                $('#policy_details_section').show();
            }

            // Toggle insurance type specific fields
            function toggleInsuranceFields(type) {
                $('#health_fields, #truck_fields').hide();
                $('#health_fields input, #truck_fields input').prop('required', false);

                if (type === 'Health') {
                    $('#health_fields').show();
                } else if (type === 'Truck') {
                    $('#truck_fields').show();
                }
            }

            // Helper function to format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB');
            }

            // Form submission
            $('#claimForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            });

            // Initialize on page load
            $('#insurance_type').trigger('change');

            // Modal Functions for Quick Actions

            // Close Claim Modal
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


        });

        // Global functions for modal interactions
        function showAssignClaimNumberModal(claimId) {
            // Clear form and show modal
            document.getElementById('insurance_claim_number').value = '';
            document.getElementById('whatsapp-preview').innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
            
            // Show modal with backdrop protection
            showModal('assignClaimNumberModal', {
                closeOnBackdrop: false,
                closeOnEscape: false
            });
        }

        function updateClaimNumberPreview() {
            const claimNumber = document.getElementById('insurance_claim_number').value.trim();
            const previewDiv = document.getElementById('whatsapp-preview');
            
            if (!claimNumber) {
                previewDiv.innerHTML = '<em class="text-muted">Enter claim number to see preview</em>';
                return;
            }
            
            // Generate preview message
            const customerName = '{{ $claim->customer->name }}';
            const vehicleText = '{{ $claim->vehicle_number ? " against your vehicle number *" . $claim->vehicle_number . "*" : "" }}';
            const advisorName = '{{ \App\Services\AppSettingService::get("insurance_advisor_name", "Parth Rawal") }}';
            const contactPhone = '{{ \App\Services\AppSettingService::get("contact_phone", "+919727793123") }}';
            
            const message = `Dear *${customerName}*,

Your Claim Number *${claimNumber}* is generated${vehicleText}. For further assistance kindly contact me.

Best regards,
${advisorName}
${contactPhone}`;
            
            previewDiv.textContent = message;
        }

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

        function submitAssignClaim() {
            alert('Function called!'); // Debug
            const claimNumber = document.getElementById('insurance_claim_number').value.trim();
            
            if (!claimNumber) {
                show_notification('error', 'Please enter the insurance claim number');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('assignClaimBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Submit via AJAX
            $.ajax({
                url: `{{ route('claims.assignClaimNumber', $claim->id) }}`,
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    insurance_claim_number: claimNumber
                },
                success: function(response) {
                    if (response.success) {
                        show_notification('success', response.message || 'Claim number assigned successfully');
                        hideModal('assignClaimNumberModal');
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        show_notification('error', response.message || 'Failed to assign claim number');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while assigning the claim number';
                    
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                    
                    show_notification('error', errorMessage);
                },
                complete: function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        function resendClaimNumber(claimId) {
            showConfirmationModal({
                title: 'Resend Claim Number',
                message: 'Are you sure you want to resend the claim number via WhatsApp?',
                confirmText: 'Yes, Resend',
                confirmClass: 'btn-success',
                onConfirm: function() {
                    // Show loading
                    showLoading('Sending WhatsApp message...');
                    
                    $.ajax({
                        url: `{{ route('claims.resendClaimNumber', $claim->id) }}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            hideLoading();
                            if (response.success) {
                                show_notification('success', response.message);
                            } else {
                                show_notification('error', response.message || 'Failed to resend message');
                            }
                        },
                        error: function(xhr) {
                            hideLoading();
                            const errorMessage = xhr.responseJSON?.message || 'An error occurred while sending the message';
                            show_notification('error', errorMessage);
                        }
                    });
                }
            });
        }
    </script>
@endsection
