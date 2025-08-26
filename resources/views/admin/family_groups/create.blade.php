@extends('layouts.app')

@section('title', 'Create Family Group')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Family Group</h1>
        <a href="{{ route('family_groups.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Family Groups
        </a>
    </div>

    <form action="{{ route('family_groups.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Family Group Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Family Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter family name (e.g., Smith Family)" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="family_head_id">Family Head <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('family_head_id') is-invalid @enderror" 
                                            id="family_head_id" name="family_head_id" required>
                                        <option value="">Select Family Head</option>
                                        @foreach($availableCustomers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('family_head_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('family_head_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Only customers not already in a family group are shown</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Members -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Family Members</h6>
                        <small class="text-muted">Add other family members (optional)</small>
                    </div>
                    <div class="card-body">
                        <div id="family-members-container">
                            <!-- Dynamic family members will be added here -->
                        </div>
                        
                        <button type="button" id="add-member-btn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Family Member
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status & Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">Active Family Group</label>
                            </div>
                            <small class="form-text text-muted">Only active family groups can be used for customer login</small>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Create Family Group
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Instructions</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check-circle text-success mr-2"></i>Select a family head who will have access to all family policies</li>
                            <li><i class="fas fa-check-circle text-success mr-2"></i>Add other family members as needed</li>
                            <li><i class="fas fa-check-circle text-success mr-2"></i>Only customers not in other families can be selected</li>
                            <li><i class="fas fa-check-circle text-success mr-2"></i>Family head will automatically be added as a member</li>
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
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Family Member</label>
                    <select class="form-control member-select" name="member_ids[]">
                        <option value="">Select Member</option>
                        @foreach($availableCustomers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->email }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Relationship</label>
                    <select class="form-control" name="relationships[]">
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
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-danger btn-sm remove-member">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
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
        minimumResultsForSearch: 0 // Always show search box
    });

    // Add Family Member
    $('#add-member-btn').click(function() {
        var template = $('#member-template').find('.member-row').clone();
        $('#family-members-container').append(template);
        
        // Re-initialize Select2 for the new element
        $('#family-members-container .member-row:last .member-select').select2({
            placeholder: "Select member",
            allowClear: true,
            minimumResultsForSearch: 0 // Always show search box
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