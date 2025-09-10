@extends('layouts.app')

@section('title', 'Edit Branch')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Branch</h1>
            <a href="{{ route('branches.index') }}" onclick="window.history.go(-1); return false;"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Branch</h6>
            </div>
            <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label><span style="color:red;">*</span>Name</label>
                            <input type="text"
                                class="form-control form-control-branch @error('name') is-invalid @enderror"
                                id="branchName" placeholder="Branch Name" name="name" value="{{ old('name', $branch->name) }}">

                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Email</label>
                            <input type="email"
                                class="form-control form-control-branch @error('email') is-invalid @enderror"
                                id="branchEmail" placeholder="Email" name="email" value="{{ old('email', $branch->email) }}">

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-sm-6 col-md-4 mb-3 mt-3 mb-sm-0">
                            <label>Mobile Number</label>
                            <input type="text"
                                class="form-control form-control-branch @error('mobile_number') is-invalid @enderror"
                                id="branchMobile" placeholder="Mobile Number" name="mobile_number" value="{{ old('mobile_number', $branch->mobile_number) }}">

                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </div>

@endsection