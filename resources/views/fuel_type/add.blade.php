@extends('layouts.app')
@section('title', 'Add Fuel Type')
@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Fuel Type</h1>
            <a href="{{ route('fuel_type.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>
        @include('common.alert')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Fuel Type</h6>
            </div>
            <form method="POST" action="{{ route('fuel_type.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control form-control-fuel_type @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-fuel_type float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('fuel_type.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
