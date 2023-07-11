@extends('layouts.app')

@section('title', 'Edit Relationship Manager')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Relationship Managers</h1>
            <a href="{{ route('relationship_managers.index') }}" onclick="window.history.go(-1); return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Relationship Manager</h6>
            </div>
            <form method="POST" action="{{ route('relationship_managers.update', ['relationship_manager' => $relationship_manager->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group row">

                        {{-- First Name --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Name</label>
                            <input type="text"
                                class="form-control form-control-relationship_manager @error('name') is-invalid @enderror"
                                id="exampleFirstName" placeholder="Name" name="name"
                                value="{{ old('name') ? old('name') : $relationship_manager->name }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            Email</label>
                            <input type="email"
                                class="form-control form-control-relationship_manager @error('email') is-invalid @enderror"
                                id="exampleEmail" placeholder="Email" name="email"
                                value="{{ old('email') ? old('email') : $relationship_manager->email }}">

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-relationship_manager @error('mobile_number') is-invalid @enderror"
                                id="exampleMobile" placeholder="Mobile Number" name="mobile_number"
                                value="{{ old('mobile_number') ? old('mobile_number') : $relationship_manager->mobile_number }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-relationship_manager float-right mb-3">Update</button>
                        <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('relationship_managers.index') }}">Cancel</a>
                    </div>
            </form>
        </div>

    </div>

@endsection
