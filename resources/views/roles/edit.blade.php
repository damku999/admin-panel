@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Role Edit Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit Role</h6>
                <a href="{{ route('roles.index') }}" onclick="window.history.go(-1); return false;"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
            <form method="POST" action="{{ route('roles.update', ['role' => $role->id]) }}">
                @csrf
                @method('PUT')
                <div class="card-body py-3">
                    <!-- Section 1: Role Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user-shield me-2"></i>Role Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter role name" value="{{ old('name', $role->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Guard Name</label>
                                <select class="form-select form-select-sm @error('guard_name') is-invalid @enderror" name="guard_name">
                                    <option value="web" @if (old('guard_name', $role->guard_name) == 'web') selected @endif>Web</option>
                                    <option value="api" @if (old('guard_name', $role->guard_name) == 'api') selected @endif>API</option>
                                </select>
                                @error('guard_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <!-- Empty column for consistent 3-column layout -->
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Permissions -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-shield-alt me-2"></i>Permissions</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="check-all" id="checkAllPermissions" 
                                    {{ (count($permissions) == count($role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="checkAllPermissions">
                                    Select All Permissions
                                </label>
                            </div>
                        </div>
                        <div class="row g-2">
                            @foreach ($permissions as $permissionIndex => $permission)
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input permission-input" type="checkbox" 
                                            {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}
                                            name="permissions[]" id="permission_{{ $permissionIndex }}" value="{{ $permission->id }}">
                                        <label class="form-check-label" for="permission_{{ $permissionIndex }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('roles.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Update Role
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
<script>
    $("#checkAllPermissions").click(function(){
        $('.permission-input').not(this).prop('checked', this.checked);
    });
</script>
@endsection