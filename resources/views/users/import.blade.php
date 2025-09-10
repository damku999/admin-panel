@extends('layouts.app')

@section('title', 'Import Users')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Users</h1>
        <a href="{{ route('users.index') }}" onclick="window.history.go(-1); return false;" 
           class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- Main Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-upload mr-2"></i>Import Users
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.upload') }}" enctype="multipart/form-data" id="import-form">
                @csrf
                
                <!-- Sample Format Information -->
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle mr-2"></i>CSV Format Required
                    </h6>
                    <p class="mb-2">Please upload CSV file in the specified format.</p>
                    <a href="{{ asset('files/sample-data-sheet.csv') }}" 
                       target="_blank" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Sample CSV Format
                    </a>
                </div>

                <!-- File Upload Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                <span class="text-danger">*</span>Select CSV File
                            </label>
                            <input type="file" 
                                   class="form-control form-control-sm @error('file') is-invalid @enderror" 
                                   id="file"
                                   name="file" 
                                   accept=".csv"
                                   required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-file-csv mr-1"></i>
                                Only CSV files are accepted
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Form Footer -->
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" form="import-form" class="btn btn-success">
                    <i class="fas fa-upload mr-1"></i> Upload Users
                </button>
            </div>
        </div>
    </div>
</div>

@endsection