@extends('layouts.app')

@section('title', 'Edit Add-on Cover')

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Add-on Cover Form -->
        <div class="card shadow mb-3 mt-2">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Edit Add-on Cover</h6>
                <a href="{{ route('addon-covers.index') }}" onclick="window.history.go(-1); return false;"
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
            <form method="POST" action="{{ route('addon-covers.update', ['addon_cover' => $addon_cover->id]) }}">
                @csrf
                @method('PUT')
                <div class="card-body py-3">
                    <!-- Section: Add-on Cover Information -->
                    <div class="mb-4">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-shield-alt me-2"></i>Add-on Cover Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Name</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    name="name" placeholder="Enter add-on cover name" 
                                    value="{{ old('name') ? old('name') : $addon_cover->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"><span class="text-danger">*</span> Order No</label>
                                <input type="number" class="form-control form-control-sm @error('order_no') is-invalid @enderror"
                                    name="order_no" placeholder="Display order" 
                                    value="{{ old('order_no') ?? $addon_cover->order_no }}" min="0">
                                <small class="text-muted">Lower numbers appear first. Set to 0 for auto-assignment.</small>
                                @error('order_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" name="status" id="status" value="1" 
                                        {{ (old('status') ?? $addon_cover->status) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Description -->
                    <div class="mb-3">
                        <h6 class="text-muted fw-bold mb-3"><i class="fas fa-file-text me-2"></i>Description</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                    name="description" rows="4" placeholder="Enter add-on cover description">{{ old('description') ? old('description') : $addon_cover->description }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer py-2 bg-light">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-secondary btn-sm px-4" href="{{ route('addon-covers.index') }}">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Update Add-on Cover
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

@endsection