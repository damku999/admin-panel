@extends('layouts.app')

@section('title', 'Add Add-on Cover')

@section('content')

    <div class="container-fluid">


        {{-- Alert Messages --}}
        @include('common.alert')

        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-12 mb-1">
                <div class="card shadow mb-1">
                    <div class="card-header py-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Add New Add-on Cover</h6>
                            <a href="{{ route('addon-covers.index') }}" onclick="window.history.go(-1); return false;"
                                class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                        </div>
                    </div>
            <form method="POST" action="{{ route('addon-covers.store') }}">
                @csrf
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
                        
                        <div class="row g-2">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label for="name" class="form-label text-sm">Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    id="name" placeholder="Add-on Cover Name" name="name" value="{{ old('name') }}" required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 col-sm-6 mb-1">
                                <label for="order_no" class="form-label text-sm">Order No <span class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control form-control-sm @error('order_no') is-invalid @enderror"
                                    id="order_no" placeholder="Display Order" name="order_no" value="{{ old('order_no', 0) }}" min="0" required>
                                <small class="text-muted">Lower numbers appear first. Set to 0 for auto-assignment.</small>

                                @error('order_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 col-sm-6 mb-1">
                                <label class="form-label text-sm">Status</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="status" id="status" value="1" 
                                        {{ old('status', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        <i class="fas fa-toggle-on"></i> Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-1">
                            <label for="description" class="form-label text-sm">Description</label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                id="description" placeholder="Add-on Cover Description" name="description" rows="3">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer p-2">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('addon-covers.index') }}" class="btn btn-secondary btn-sm mr-2">Cancel</a>
                            <button type="submit" class="btn btn-success btn-sm">Save Add-on Cover</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script></script>
@endsection