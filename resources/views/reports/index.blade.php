@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Modern Reports Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0 rounded">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-bar fa-lg me-2"></i>
                            <div>
                                <h5 class="mb-0 font-weight-bold">Reports Dashboard</h5>
                                <small class="opacity-75">Generate comprehensive insurance reports</small>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('reports.index') }}" method="POST" id="reportForm" class="modern-form">
                        @csrf
                        <div class="card-body p-3">
                            <!-- Report Type Selection - Compact Design -->
                            <div class="row mb-3">
                                <div class="col-lg-8 col-md-10 mx-auto">
                                    <div class="report-selector-card">
                                        <label class="form-label fw-bold text-primary mb-2">
                                            <i class="fas fa-chart-line me-1"></i>Select Report Type <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-select-wrapper">
                                            <select class="custom-select form-select @error('report_name') is-invalid @enderror" 
                                                    id="reportName" name="report_name" required>
                                                <option value="">Choose a report to generate...</option>
                                                @foreach (config('constants.REPORTS') as $reportName => $reportDescription)
                                                    <option value="{{ $reportName }}"{{ request('report_name') === $reportName ? ' selected' : '' }}>
                                                        {{ $reportDescription }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down select-arrow"></i>
                                        </div>
                                        @error('report_name')
                                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Primary Date Filters (Always Visible) -->
                            <div class="primary-filters-container mb-3">
                                <div class="row mb-2">
                                    <!-- Issue Date Range (Required for insurance_detail, Optional for cross_selling) -->
                                    <div class="col-lg-6 col-md-6 mb-2 fields-to-toggle insurance_detail cross_selling" style="display: none;">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white py-1">
                                                <small>
                                                    <i class="fas fa-calendar-alt me-1"></i>Issue Date Range 
                                                    <span class="date-requirement insurance_detail text-warning fw-bold">(Required)</span>
                                                    <span class="date-requirement cross_selling text-light">(Optional)</span>
                                                </small>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control form-control-sm datepicker" 
                                                                   id="issue_start_date" name="issue_start_date" 
                                                                   value="{{ request('issue_start_date') }}" 
                                                                   placeholder="From Date" readonly>
                                                            <label for="issue_start_date"><small>From Date</small></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control form-control-sm datepicker" 
                                                                   id="issue_end_date" name="issue_end_date" 
                                                                   value="{{ request('issue_end_date') }}" 
                                                                   placeholder="To Date" readonly>
                                                            <label for="issue_end_date"><small>To Date</small></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Due Policy Period (Required for due_policy_detail) -->
                                    <div class="col-lg-6 col-md-6 mb-2 fields-to-toggle due_policy_detail" style="display: none;">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark py-1">
                                                <small>
                                                    <i class="fas fa-calendar-check me-1"></i>Due Policy Period 
                                                    <span class="text-danger fw-bold">(Required)</span>
                                                </small>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control form-control-sm datepicker_month" 
                                                                   id="due_start_date" name="due_start_date" 
                                                                   value="{{ request('due_start_date') }}" 
                                                                   placeholder="From Month" readonly>
                                                            <label for="due_start_date"><small>From Month</small></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control form-control-sm datepicker_month" 
                                                                   id="due_end_date" name="due_end_date" 
                                                                   value="{{ request('due_end_date') }}" 
                                                                   placeholder="To Month" readonly>
                                                            <label for="due_end_date"><small>To Month</small></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Filters Section -->
                            <div class="advanced-filters-container mb-3">
                                <div class="filter-toggle-header text-center mb-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="toggleFilters">
                                        <i class="fas fa-filter me-1"></i>Advanced Filters <small>(Optional)</small>
                                        <i class="fas fa-chevron-down ms-1" id="filterChevron"></i>
                                    </button>
                                </div>
                                
                                <div class="filters-content" id="filtersContent" style="display: none;">
                                    <!-- Optional Date Range Filters Row -->
                                    <div class="row mb-2">
                                        <!-- Record Creation Date -->
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-plus-circle me-1"></i><small>Record Creation Date</small>
                                                </div>
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm datepicker" 
                                                               id="record_creation_start_date" name="record_creation_start_date" 
                                                               value="{{ request('record_creation_start_date') }}" 
                                                               placeholder="From Date" readonly>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm datepicker" 
                                                               id="record_creation_end_date" name="record_creation_end_date" 
                                                               value="{{ request('record_creation_end_date') }}" 
                                                               placeholder="To Date" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Business Entity Filters Row -->
                                    <div class="row mb-2">
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-user-tie me-1"></i><small>Broker</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="broker_id">
                                                    <option value="">All Brokers</option>
                                                    @if(isset($brokers))
                                                        @foreach($brokers as $broker)
                                                            <option value="{{ $broker->id }}" {{ request('broker_id') == $broker->id ? 'selected' : '' }}>{{ $broker->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-user-friends me-1"></i><small>RM</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="relationship_manager_id">
                                                    <option value="">All RMs</option>
                                                    @if(isset($relationship_managers))
                                                        @foreach($relationship_managers as $rm)
                                                            <option value="{{ $rm->id }}" {{ request('relationship_manager_id') == $rm->id ? 'selected' : '' }}>{{ $rm->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-shield-alt me-1"></i><small>Insurance Company</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="insurance_company_id">
                                                    <option value="">All Companies</option>
                                                    @if(isset($insurance_companies))
                                                        @foreach($insurance_companies as $company)
                                                            <option value="{{ $company->id }}" {{ request('insurance_company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-users me-1"></i><small>Customer</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="customer_id">
                                                    <option value="">All Customers</option>
                                                    @if(isset($customers))
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Policy & Premium Filters Row -->
                                    <div class="row mb-2">
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-file-contract me-1"></i><small>Policy Type</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="policy_type_id">
                                                    <option value="">All Policy Types</option>
                                                    @if(isset($policy_types))
                                                        @foreach($policy_types as $policyType)
                                                            <option value="{{ $policyType->id }}" {{ request('policy_type_id') == $policyType->id ? 'selected' : '' }}>{{ $policyType->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-gas-pump me-1"></i><small>Fuel Type</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="fuel_type_id">
                                                    <option value="">All Fuel Types</option>
                                                    @if(isset($fuel_types))
                                                        @foreach($fuel_types as $fuelType)
                                                            <option value="{{ $fuelType->id }}" {{ request('fuel_type_id') == $fuelType->id ? 'selected' : '' }}>{{ $fuelType->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-money-bill-wave me-1"></i><small>Premium Type</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="premium_type_id">
                                                    <option value="">All Premium Types</option>
                                                    @if(isset($premium_types))
                                                        @foreach($premium_types as $premiumType)
                                                            <option value="{{ $premiumType->id }}" {{ request('premium_type_id') == $premiumType->id ? 'selected' : '' }}>{{ $premiumType->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-toggle-on me-1"></i><small>Status</small>
                                                </div>
                                                <select class="form-select form-select-sm" name="status">
                                                    <option value="">All Status</option>
                                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-8 mb-2">
                                            <div class="filter-card">
                                                <div class="filter-header">
                                                    <i class="fas fa-rupee-sign me-1"></i><small>Premium Amount Range</small>
                                                </div>
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="premium_amount_min" 
                                                               value="{{ request('premium_amount_min') }}" 
                                                               placeholder="Min Amount" min="0" step="0.01">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="premium_amount_max" 
                                                               value="{{ request('premium_amount_max') }}" 
                                                               placeholder="Max Amount" min="0" step="0.01">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="card-footer bg-light p-2">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="resetForm()">
                                    <i class="fas fa-redo me-1"></i>Reset
                                </button>
                                <button type="submit" name="view" value="1" class="btn btn-primary btn-sm px-3" onclick="return validateForm(this);">
                                    <i class="fas fa-eye me-1"></i>View Report
                                </button>
                                <button type="button" class="btn btn-success btn-sm px-3" onclick="downloadReport(this)">
                                    <i class="fas fa-download me-1"></i>Download Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Report Results Section -->
        @if(isset($cross_selling_report) && !empty($cross_selling_report))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fas fa-table me-2"></i>Cross Selling Report Results</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            @foreach($cross_selling_report->first() as $key => $value)
                                                <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cross_selling_report as $row)
                                            <tr>
                                                @foreach($row as $cell)
                                                    <td>{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($insurance_reports) && !empty($insurance_reports))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Insurance Detail Report Results</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Customer Name</th>
                                            <th>Policy Number</th>
                                            <th>Insurance Company</th>
                                            <th>Issue Date</th>
                                            <th>Expiry Date</th>
                                            <th>Premium Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($insurance_reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $report->customer_name ?? 'N/A' }}</td>
                                                <td>{{ $report->policy_number ?? 'N/A' }}</td>
                                                <td>{{ $report->insurance_company ?? 'N/A' }}</td>
                                                <td>{{ $report->issue_date ?? 'N/A' }}</td>
                                                <td>{{ $report->expired_date ?? 'N/A' }}</td>
                                                <td>{{ $report->premium_amount ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $report->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $report->status == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($due_policy_reports) && !empty($due_policy_reports))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Due Policy Report Results</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Customer Name</th>
                                            <th>Policy Number</th>
                                            <th>Insurance Company</th>
                                            <th>Issue Date</th>
                                            <th>Expiry Date</th>
                                            <th>Premium Amount</th>
                                            <th>Days Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($due_policy_reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $report->customer_name ?? 'N/A' }}</td>
                                                <td>{{ $report->policy_number ?? 'N/A' }}</td>
                                                <td>{{ $report->insurance_company ?? 'N/A' }}</td>
                                                <td>{{ $report->issue_date ?? 'N/A' }}</td>
                                                <td>{{ $report->expired_date ?? 'N/A' }}</td>
                                                <td>{{ $report->premium_amount ?? 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $daysRemaining = \Carbon\Carbon::parse($report->expired_date)->diffInDays(now());
                                                    @endphp
                                                    <span class="badge {{ $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 30 ? 'bg-warning' : 'bg-success') }}">
                                                        {{ $daysRemaining }} days
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .modern-form {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(2.5rem + 2px);
        }
        
        .form-floating > .form-control-sm {
            height: calc(2rem + 2px);
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            border-radius: 0.5rem;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        
        .fields-to-toggle {
            transition: all 0.5s ease;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.75rem;
        }
        
        /* Compact Dropdown Styling */
        .report-selector-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .report-selector-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .custom-select-wrapper {
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .custom-select-wrapper:hover {
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .custom-select {
            appearance: none;
            background: transparent;
            border: 2px solid transparent;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .custom-select:focus {
            outline: none;
            border-color: #007bff;
            background: #f8f9ff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .custom-select option {
            padding: 0.75rem;
            font-weight: 500;
            background: white;
            color: #2c3e50;
        }
        
        .custom-select option:hover {
            background: #e3f2fd;
        }
        
        .select-arrow {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            font-size: 0.8rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        
        .custom-select-wrapper:hover .select-arrow {
            color: #0056b3;
            transform: translateY(-50%) rotate(180deg);
        }
        
        .custom-select:focus + .select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }
        
        /* Enhanced Date Range Cards */
        .fields-to-toggle .card {
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .fields-to-toggle .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .fields-to-toggle .card-header {
            border: none;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }
        
        .form-floating > .form-control {
            border-radius: 6px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
        }
        
        /* Action Buttons Enhancement */
        .btn-sm {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-sm::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-sm:hover::before {
            left: 100%;
        }
        
        .btn-sm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        
        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        
        .btn-outline-secondary {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        
        /* Primary Date Filters Container */
        .primary-filters-container {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #ffc107;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.1);
        }
        
        /* Advanced Filters Container */
        .advanced-filters-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #dee2e6;
        }
        
        .filter-toggle-header button {
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        }
        
        .filter-toggle-header button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }
        
        .filters-content {
            transition: all 0.5s ease;
            overflow: hidden;
        }
        
        /* Compact Filter Cards */
        .filter-card {
            background: white;
            border-radius: 6px;
            padding: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .filter-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #007bff;
        }
        
        .filter-header {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-card .form-select,
        .filter-card .form-control {
            border: 1px solid #ced4da;
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .filter-card .form-select:focus,
        .filter-card .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 1px rgba(0, 123, 255, 0.1);
        }
        
        /* Responsive adjustments for filters */
        @media (max-width: 768px) {
            .filter-card {
                margin-bottom: 0.5rem;
            }
            
            .advanced-filters-container {
                padding: 0.75rem;
            }
        }
        
        /* Animation for chevron */
        #filterChevron {
            transition: transform 0.3s ease;
        }
        
        #filterChevron.rotated {
            transform: rotate(180deg);
        }
    </style>

    <script>
        // Enhanced validation for view action with date requirements
        function validateForm(button) {
            const reportName = document.getElementById('reportName').value;
            
            if (!reportName) {
                toastr.error('Please select a Report Type first.', 'Validation Error');
                return false;
            }
            
            // Validate required date fields based on report type
            if (reportName === 'insurance_detail') {
                const startDate = document.getElementById('issue_start_date').value;
                const endDate = document.getElementById('issue_end_date').value;
                
                if (!startDate || !endDate) {
                    toastr.error('Issue Date Range is required for Insurance Detail reports.', 'Validation Error');
                    return false;
                }
            }
            
            if (reportName === 'due_policy_detail') {
                const startMonth = document.getElementById('due_start_date').value;
                const endMonth = document.getElementById('due_end_date').value;
                
                if (!startMonth || !endMonth) {
                    toastr.error('Due Policy Period is required for Due Policy reports.', 'Validation Error');
                    return false;
                }
            }
            
            // Show loading state
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }, 5000);
            
            return true; // Allow form submission
        }
        
        // Download action with enhanced validation
        function downloadReport(button) {
            const reportName = document.getElementById('reportName').value;
            
            if (!reportName) {
                toastr.error('Please select a Report Type first.', 'Validation Error');
                return;
            }
            
            // Validate required date fields based on report type
            if (reportName === 'insurance_detail') {
                const startDate = document.getElementById('issue_start_date').value;
                const endDate = document.getElementById('issue_end_date').value;
                
                if (!startDate || !endDate) {
                    toastr.error('Issue Date Range is required for Insurance Detail reports.', 'Validation Error');
                    return;
                }
            }
            
            if (reportName === 'due_policy_detail') {
                const startMonth = document.getElementById('due_start_date').value;
                const endMonth = document.getElementById('due_end_date').value;
                
                if (!startMonth || !endMonth) {
                    toastr.error('Due Policy Period is required for Due Policy reports.', 'Validation Error');
                    return;
                }
            }
            
            // Show loading state
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Downloading...';
            button.disabled = true;
            
            // Build query string from form data
            const form = document.getElementById('reportForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value && key !== '_token' && key !== '_method') {
                    params.append(key, value);
                }
            }
            
            // Create download URL
            const downloadUrl = "{{ route('reports.export') }}?" + params.toString();
            
            // Trigger download
            window.location.href = downloadUrl;
            
            // Reset button state
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }, 3000);
        }

        // Advanced Filters Toggle
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const filtersContent = document.getElementById('filtersContent');
            const chevron = document.getElementById('filterChevron');
            
            if (filtersContent.style.display === 'none') {
                filtersContent.style.display = 'block';
                chevron.classList.add('rotated');
                this.innerHTML = '<i class="fas fa-filter me-1"></i>Hide Filters <small>(Optional)</small> <i class="fas fa-chevron-up ms-1" id="filterChevron"></i>';
            } else {
                filtersContent.style.display = 'none';
                chevron.classList.remove('rotated');
                this.innerHTML = '<i class="fas fa-filter me-1"></i>Advanced Filters <small>(Optional)</small> <i class="fas fa-chevron-down ms-1" id="filterChevron"></i>';
            }
        });

        // Toggle fields based on report type
        document.getElementById('reportName').addEventListener('change', function() {
            const selectedReport = this.value;
            const fieldsToToggle = document.querySelectorAll('.fields-to-toggle');
            
            // Hide all conditional fields first
            fieldsToToggle.forEach(field => {
                field.style.display = 'none';
            });
            
            // Show relevant primary date filters immediately (always visible when report is selected)
            if (selectedReport) {
                const primaryFields = document.querySelectorAll('.primary-filters-container .fields-to-toggle.' + selectedReport);
                primaryFields.forEach(field => {
                    field.style.display = 'block';
                });
                
                // Show/hide appropriate requirement indicators
                const dateRequirements = document.querySelectorAll('.date-requirement');
                dateRequirements.forEach(req => {
                    req.style.display = 'none';
                });
                
                if (selectedReport === 'insurance_detail') {
                    const reqElements = document.querySelectorAll('.date-requirement.insurance_detail');
                    reqElements.forEach(req => req.style.display = 'inline');
                } else if (selectedReport === 'cross_selling') {
                    const reqElements = document.querySelectorAll('.date-requirement.cross_selling');
                    reqElements.forEach(req => req.style.display = 'inline');
                }
                
                // Show relevant advanced filter fields if filters are open
                if (document.getElementById('filtersContent').style.display === 'block') {
                    const advancedFields = document.querySelectorAll('.filters-content .fields-to-toggle.' + selectedReport);
                    advancedFields.forEach(field => {
                        field.style.display = 'block';
                    });
                }
            }
        });

        // Reset form function
        function resetForm() {
            document.getElementById('reportForm').reset();
            
            // Hide filters
            const filtersContent = document.getElementById('filtersContent');
            const chevron = document.getElementById('filterChevron');
            const toggleButton = document.getElementById('toggleFilters');
            
            filtersContent.style.display = 'none';
            chevron.classList.remove('rotated');
            toggleButton.innerHTML = '<i class="fas fa-filter me-1"></i>Advanced Filters <small>(Optional)</small> <i class="fas fa-chevron-down ms-1" id="filterChevron"></i>';
            
            // Hide all conditional fields
            const fieldsToToggle = document.querySelectorAll('.fields-to-toggle');
            fieldsToToggle.forEach(field => {
                field.style.display = 'none';
            });
        }


        // Initialize form functionality when document is ready
        $(document).ready(function() {
            console.log('Reports dashboard initialized successfully');
        });
    </script>
@endsection