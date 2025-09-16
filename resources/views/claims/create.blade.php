@extends('layouts.app')

@section('title', 'Create New Claim')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Claim Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Create New Claim</h6>
                <a href="{{ route('claims.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('claims.store') }}" id="claimForm">
                @csrf
                <div class="card-body py-3">
                    <!-- Section 1: Policy Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-file-contract me-2"></i>Policy Information</h6>
                        <x-claims.policy-selector mode="create" :selectedValue="old('customer_insurance_id')" />
                    </div>

                    <!-- Section 2: Claim Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-clipboard-list me-2"></i>Claim Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Incident Date</label>
                                <input type="text" class="form-control datepicker @error('incident_date') is-invalid @enderror"
                                       name="incident_date" id="incident_date" placeholder="DD/MM/YYYY"
                                       value="{{ old('incident_date') }}" required>
                                @error('incident_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">WhatsApp Number</label>
                                <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                       name="whatsapp_number" id="whatsapp_number" placeholder="WhatsApp number for updates"
                                       value="{{ old('whatsapp_number') }}">
                                @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Will be auto-filled from customer mobile if not provided</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          name="description" rows="3" placeholder="Describe the incident details..."
                                          maxlength="1000">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maximum 1000 characters</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Email Notifications</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="send_email_notifications"
                                           id="send_email_notifications" value="1"
                                           {{ old('send_email_notifications') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_email_notifications">
                                        Send email notifications to customer
                                    </label>
                                </div>
                                <small class="text-muted">Customer will receive claim updates via email</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="window.history.back();">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Claim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            // Form validation with policy selector component
            $('#claimForm').on('submit', function(e) {
                // Use policy selector validation
                if (typeof window.validatePolicySelection === 'function' && !window.validatePolicySelection()) {
                    e.preventDefault();
                    return false;
                }

                if (!$('#insurance_type').val()) {
                    e.preventDefault();
                    alert('Please select insurance type.');
                    $('#insurance_type').focus();
                    return false;
                }

                if (!$('#incident_date').val()) {
                    e.preventDefault();
                    alert('Please select incident date.');
                    $('#incident_date').focus();
                    return false;
                }
            });
        });
    </script>
@endpush