@extends('layouts.app')

@section('title', 'Edit Family Group')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Family Group: {{ $familyGroup->name }}</h1>
        <div class="d-sm-flex">
            <a href="{{ route('family_groups.show', $familyGroup) }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> View Details
            </a>
            <a href="{{ route('family_groups.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('family_groups.update', $familyGroup) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                           id="name" name="name" value="{{ old('name', $familyGroup->name) }}" 
                                           placeholder="Enter family name" required>
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
                                            <option value="{{ $customer->id }}" 
                                                    {{ old('family_head_id', $familyGroup->family_head_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('family_head_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-left-warning">
                                    <div class="card-body py-3">
                                        <h6 class="font-weight-bold text-warning mb-3">
                                            <i class="fas fa-key mr-2"></i>Family Head Password Management
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="family_head_password">Change Password for Family Head</label>
                                                    <input type="password" class="form-control @error('family_head_password') is-invalid @enderror" 
                                                           id="family_head_password" name="family_head_password" 
                                                           value="{{ old('family_head_password') }}"
                                                           placeholder="Leave blank to keep current password">
                                                    @error('family_head_password')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Only fill this if you want to change the current password
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="family_head_password_confirmation">Confirm New Password</label>
                                                    <input type="password" class="form-control" 
                                                           id="family_head_password_confirmation" name="family_head_password_confirmation" 
                                                           placeholder="Re-enter new password to confirm">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-shield-alt mr-1"></i>
                                                        Password must be at least 8 characters long
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" value="1" id="force_password_change" name="force_password_change" {{ old('force_password_change', 0) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="force_password_change">
                                                Force password change on next login
                                            </label>
                                        </div>
                                        
                                        @if($familyGroup->familyHead)
                                        <div class="mt-3 p-2 bg-light rounded">
                                            <small class="text-muted">
                                                <i class="fas fa-user-check mr-1"></i>
                                                <strong>Current Family Head:</strong> {{ $familyGroup->familyHead->name }} ({{ $familyGroup->familyHead->email }})
                                                <br>
                                                <i class="fas fa-calendar mr-1"></i>
                                                <strong>Last Password Change:</strong> {{ $familyGroup->familyHead->password_changed_at ? formatDateForUi($familyGroup->familyHead->password_changed_at, 'd M Y H:i') : 'Never' }}
                                                <br>
                                                <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
                                                <strong>Must Change Password:</strong> {{ $familyGroup->familyHead->must_change_password ? 'Yes' : 'No' }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Family Members -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Current Family Members</h6>
                    </div>
                    <div class="card-body">
                        @if($familyGroup->familyMembers->count() > 0)
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Relationship</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($familyGroup->familyMembers as $member)
                                        <tr class="{{ $member->is_head ? 'table-warning' : '' }}">
                                            <td>
                                                {{ $member->customer->name }}
                                                @if($member->is_head)
                                                    <i class="fas fa-crown text-warning ml-2" title="Family Head"></i>
                                                @endif
                                            </td>
                                            <td>{{ $member->customer->email }}</td>
                                            <td>
                                                @if($member->relationship)
                                                    <span class="badge badge-info">{{ ucfirst($member->relationship) }}</span>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->is_head)
                                                    <span class="badge badge-warning">Head</span>
                                                @else
                                                    <span class="badge badge-secondary">Member</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Editing will replace all current family members with the new selection below. The family head will automatically be included as a member.
                        </div>
                    </div>
                </div>

                <!-- New Family Members -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Update Family Members</h6>
                        <small class="text-muted">Select new family members (excluding family head)</small>
                    </div>
                    <div class="card-body">
                        <div id="family-members-container">
                            <!-- Pre-populate with existing members (excluding head) -->
                            @foreach($familyGroup->familyMembers->where('is_head', false) as $index => $member)
                                <div class="member-row border rounded p-3 mb-3 bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Family Member</label>
                                                <select class="form-control member-select" name="member_ids[]">
                                                    <option value="">Select Member</option>
                                                    @foreach($availableCustomers as $customer)
                                                        <option value="{{ $customer->id }}" 
                                                                {{ $member->customer_id == $customer->id ? 'selected' : '' }}>
                                                            {{ $customer->name }} - {{ $customer->email }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Relationship</label>
                                                <select class="form-control" name="relationships[]">
                                                    <option value="">Select Relationship</option>
                                                    <option value="spouse" {{ $member->relationship == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                                    <option value="child" {{ $member->relationship == 'child' ? 'selected' : '' }}>Child</option>
                                                    <option value="parent" {{ $member->relationship == 'parent' ? 'selected' : '' }}>Parent</option>
                                                    <option value="sibling" {{ $member->relationship == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                                    <option value="grandparent" {{ $member->relationship == 'grandparent' ? 'selected' : '' }}>Grandparent</option>
                                                    <option value="grandchild" {{ $member->relationship == 'grandchild' ? 'selected' : '' }}>Grandchild</option>
                                                    <option value="other" {{ $member->relationship == 'other' ? 'selected' : '' }}>Other</option>
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
                            @endforeach
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
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                                       {{ old('status', $familyGroup->status) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">Active Family Group</label>
                            </div>
                            <small class="form-text text-muted">Only active family groups can be used for customer login</small>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Update Family Group
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Current Info -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Current Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless small">
                            <tr>
                                <td class="font-weight-bold">Total Members:</td>
                                <td>{{ $familyGroup->familyMembers->count() }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Current Head:</td>
                                <td>{{ $familyGroup->familyHead->name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Status:</td>
                                <td>
                                    @if($familyGroup->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Created:</td>
                                <td>{{ formatDateForUi($familyGroup->created_at) }}</td>
                            </tr>
                        </table>
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

    $('.member-select').select2({
        placeholder: "Select member",
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
        
        // Apply current restrictions
        updateMemberSelections();
    });

    // Remove Family Member
    $(document).on('click', '.remove-member', function() {
        $(this).closest('.member-row').remove();
        updateMemberSelections();
    });

    // Update member selections when family head changes
    $(document).on('change', '#family_head_id', function() {
        updateMemberSelections();
    });

    // Update member selections when any member selection changes
    $(document).on('change', '.member-select', function() {
        updateMemberSelections();
    });

    function updateMemberSelections() {
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

    // Initial restriction update
    updateMemberSelections();
});
</script>
@endsection