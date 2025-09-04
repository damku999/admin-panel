@extends('layouts.app')

@section('title', 'Add Add-on Cover')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Add-on Cover</h1>
            <a href="{{ route('addon-covers.index') }}" onclick="window.history.go(-1); return false;"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Add-on Cover</h6>
            </div>
            <form method="POST" action="{{ route('addon-covers.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-6 mb-3 mt-3 mb-sm-0">
                            <label><span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Add-on Cover Name" name="name" value="{{ old('name') }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-3 col-md-3 mb-3 mt-3 mb-sm-0">
                            <label><span style="color:red;">*</span>Order No</label>
                            <input type="number"
                                class="form-control @error('order_no') is-invalid @enderror"
                                placeholder="Display Order" name="order_no" value="{{ old('order_no', 0) }}" min="0">
                            <small class="text-muted">Lower numbers appear first. Set to 0 for auto-assignment.</small>

                            @error('order_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-3 col-md-3 mb-3 mt-3 mb-sm-0">
                            <label>Status</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status" id="status" value="1" 
                                    {{ old('status') ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                            <label>Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Add-on Cover Description" name="description" rows="4">{{ old('description') }}</textarea>

                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('addon-covers.index') }}">Cancel</a>
                </div>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script></script>
@endsection