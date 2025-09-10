@extends('layouts.app')

@section('title', 'Create Family Group')

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @include('common.alert')

    <form action="{{ route('family_groups.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <!-- Family Group Form -->
                <div class="card shadow mb-3 mt-2">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">Create Family Group</h6>
                        <a href="{{ route('family_groups.index') }}" onclick="window.history.go(-1); return false;"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                            <i class="fas fa-chevron-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="card-body py-3">
                        <!-- Section 1: Basic Information -->
                        <div class="mb-4">
                            <h6 class="text-muted fw-bold mb-3"><i class="fas fa-users me-2"></i>Family Group Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><span class="text-danger">*</span> Family Name</label>
                                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter family name (e.g., Smith Family)" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><span class="text-danger">*</span> Family Head</label>
                                    <select class="form-select form-select-sm select2 @error('family_head_id') is-invalid @enderror" 
                                            id="family_head_id" name="family_head_id" required>
                                        <option value="">Select Family Head</option>
                                        @foreach($availableCustomers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('family_head_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('family_head_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Only customers not already in a family group are shown</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Password Settings -->
                        <div class="mb-4">
                            <h6 class="text-muted fw-bold mb-3"><i class="fas fa-key me-2"></i>Family Head Password Settings</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Set Password for Family Head</label>
                                    <input type="password" class="form-control form-control-sm @error('family_head_password') is-invalid @enderror" 
                                           id="family_head_password" name="family_head_password" 
                                           value="{{ old('family_head_password') }}"
                                           placeholder="Leave blank to auto-generate">
                                    @error('family_head_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        If left blank, a secure password will be auto-generated
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm Password</label>
                                    <input type="password" class="form-control form-control-sm" 
                                           id="family_head_password_confirmation" name="family_head_password_confirmation" 
                                           placeholder="Re-enter password to confirm">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Password must be at least 8 characters long
                                    </small>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="force_password_change" name="force_password_change" {{ old('force_password_change', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="force_password_change">
                                            Force password change on first login
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="status">Active Family Group</label>
                                    </div>
                                    <small class="form-text text-muted">Only active family groups can be used for customer login</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Family Members -->
                        <div class="mb-3">
                            <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user-friends me-2"></i>Family Members</h6>
                            <small class="text-muted d-block mb-3">Add other family members (optional)</small>
                            <div id="family-members-container">
                                <!-- Dynamic family members will be added here -->
                            </div>
                            
                            <button type="button" id="add-member-btn" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Add Family Member
                            </button>
                        </div>
                    </div>

                    <div class="card-footer py-2 bg-light">
                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-secondary btn-sm px-4" href="{{ route('family_groups.index') }}">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-sm px-4">
                                <i class="fas fa-save me-1"></i>Create Family Group
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions Sidebar -->
            <div class="col-lg-4">
                <!-- Instructions -->
                <div class="card shadow mb-3 mt-2">
                    <div class="card-header py-2">
                        <h6 class="mb-0 fw-bold text-primary">Instructions</h6>
                    </div>
                    <div class="card-body py-3">
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Select a family head who will have access to all family policies</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Add other family members as needed</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Only customers not in other families can be selected</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Family head will automatically be added as a member</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Family Member Template (Hidden) -->
<div id="member-template" style="display: none;">
    <div class="member-row border rounded p-3 mb-3 bg-light">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Family Member</label>
                <select class="form-select form-select-sm member-select" name="member_ids[]">
                    <option value="">Select Member</option>
                    @foreach($availableCustomers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Relationship</label>
                <select class="form-select form-select-sm" name="relationships[]">
                    <option value="">Select Relationship</option>
                    <option value="spouse">Spouse</option>
                    <option value="child">Child</option>
                    <option value="parent">Parent</option>
                    <option value="sibling">Sibling</option>
                    <option value="grandparent">Grandparent</option>
                    <option value="grandchild">Grandchild</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-member d-block">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        minimumResultsForSearch: 0
    });

    // Add Family Member
    $('#add-member-btn').click(function() {
        var template = $('#member-template').find('.member-row').clone();
        $('#family-members-container').append(template);
        
        // Re-initialize Select2 for the new element
        $('#family-members-container .member-row:last .member-select').select2({
            placeholder: "Select member",
            allowClear: true,
            minimumResultsForSearch: 0
        });
        
        // Update member restrictions
        updateMemberRestrictions();
    });

    // Remove Family Member
    $(document).on('click', '.remove-member', function() {
        $(this).closest('.member-row').remove();
        updateMemberRestrictions();
    });

    // Update member restrictions when family head changes
    $(document).on('change', '#family_head_id', function() {
        updateMemberRestrictions();
    });

    // Update member restrictions when any member selection changes
    $(document).on('change', '.member-select', function() {
        updateMemberRestrictions();
    });

    // Function to update member selection restrictions
    function updateMemberRestrictions() {
        var selectedHead = $('#family_head_id').val();
        var selectedValues = [];
        
        // Get all selected member values
        $('.member-select').each(function() {
            if ($(this).val()) {
                selectedValues.push($(this).val());
            }
        });

        // Update each member select
        $('.member-select').each(function() {
            var currentSelect = $(this);
            var currentValue = currentSelect.val();
            
            // Reset all options
            currentSelect.find('option').prop('disabled', false);
            
            // Disable family head
            if (selectedHead) {
                currentSelect.find('option[value="' + selectedHead + '"]').prop('disabled', true);
            }
            
            // Disable already selected members (except current selection)
            $.each(selectedValues, function(index, value) {
                if (value !== currentValue && value) {
                    currentSelect.find('option[value="' + value + '"]').prop('disabled', true);
                }
            });
            
            // Trigger Select2 update if it's initialized
            if (currentSelect.hasClass('select2-hidden-accessible')) {
                currentSelect.trigger('change.select2');
            }
        });
    }
});
</script>
@endsection