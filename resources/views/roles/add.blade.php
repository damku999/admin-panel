@extends('layouts.app')

@section('title', 'Add Role')

@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header py-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Add Role</h6>
                    <a href="{{ route('roles.index') }}" onclick="window.history.go(-1); return false;"
                        class="btn btn-back-compact" title="Back"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            <form method="POST" action="{{ route('roles.store') }}">
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
                        {{-- Name --}}
                        <div class="col-md-6 col-sm-12 mb-1">
                            <label for="name" class="form-label text-sm"><span class="text-danger">*</span>Role Name</label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                id="name" placeholder="Enter role name" name="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Guard Name --}}
                        <div class="col-md-6 col-sm-12 mb-1">
                            <label for="guard_name" class="form-label text-sm"><span class="text-danger">*</span>Guard Name</label>
                            <select class="form-control form-control-sm @error('guard_name') is-invalid @enderror" name="guard_name">
                                <option value="">Select Guard Name</option>
                                <option value="web" {{ old('guard_name', 'web') == 'web' ? 'selected' : '' }}>Web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                            </select>
                            @error('guard_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer py-1">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-secondary btn-sm mr-2" href="{{ route('roles.index') }}">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-save mr-1"></i>Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection