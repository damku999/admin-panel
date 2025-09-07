{{--
    Export Button Component
    
    Usage:
    <x-buttons.export-button 
        export-url="{{ route('customers.export') }}"
        format="xlsx"
        :with-filters="true"
        :show-dropdown="true"
        size="sm"
        title="Export Customers">
        Export Data
    </x-buttons.export-button>
--}}

@props([
    'exportUrl' => '',
    'format' => 'xlsx', // xlsx, csv, pdf
    'formats' => ['xlsx', 'csv'], // Available formats for dropdown
    'withFilters' => false,
    'showDropdown' => false,
    'size' => 'sm',
    'variant' => 'success',
    'icon' => 'fas fa-download',
    'title' => 'Export Data',
    'disabled' => false,
    'onclick' => '',
    'ajaxExport' => false,
    'filterFormId' => '',
    'customParams' => []
])

@if($showDropdown && count($formats) > 1)
    <!-- Export Dropdown Button -->
    <div class="btn-group export-button-group" role="group">
        <button type="button" 
                class="btn btn-{{ $variant }} btn-{{ $size }} export-btn"
                onclick="{{ $onclick ?: 'initiateExport(\'' . $exportUrl . '\', \'' . $format . '\', ' . json_encode($customParams) . ')' }}"
                @if($disabled) disabled @endif
                title="{{ $title }} ({{ strtoupper($format) }})">
            <i class="{{ $icon }}"></i>
            @if($slot->isNotEmpty())
                <span class="d-none d-sm-inline">{{ $slot }}</span>
            @else
                <span class="d-none d-sm-inline">Export</span>
            @endif
        </button>
        
        <button type="button" 
                class="btn btn-{{ $variant }} btn-{{ $size }} dropdown-toggle dropdown-toggle-split"
                data-toggle="dropdown" 
                aria-expanded="false"
                @if($disabled) disabled @endif>
            <span class="sr-only">Export options</span>
        </button>
        
        <ul class="dropdown-menu export-formats-menu">
            @foreach($formats as $fmt)
                @php
                    $formatIcon = match($fmt) {
                        'xlsx' => 'fas fa-file-excel text-success',
                        'csv' => 'fas fa-file-csv text-info',
                        'pdf' => 'fas fa-file-pdf text-danger',
                        default => 'fas fa-file text-secondary'
                    };
                    $formatLabel = strtoupper($fmt);
                @endphp
                
                <li>
                    <a class="dropdown-item export-format-option" 
                       href="#" 
                       onclick="initiateExport('{{ $exportUrl }}', '{{ $fmt }}', {{ json_encode($customParams) }})"
                       data-format="{{ $fmt }}">
                        <i class="{{ $formatIcon }} mr-2"></i>
                        Export as {{ $formatLabel }}
                    </a>
                </li>
            @endforeach
            
            @if($withFilters)
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" 
                       href="#" 
                       onclick="showExportFiltersModal()">
                        <i class="fas fa-filter text-primary mr-2"></i>
                        Export with Filters
                    </a>
                </li>
            @endif
        </ul>
    </div>
@else
    <!-- Single Export Button -->
    <button type="button" 
            class="btn btn-{{ $variant }} btn-{{ $size }} export-btn"
            onclick="{{ $onclick ?: 'initiateExport(\'' . $exportUrl . '\', \'' . $format . '\', ' . json_encode($customParams) . ')' }}"
            @if($disabled) disabled @endif
            title="{{ $title }}"
            data-export-url="{{ $exportUrl }}"
            data-format="{{ $format }}"
            data-with-filters="{{ $withFilters ? 'true' : 'false' }}"
            data-ajax="{{ $ajaxExport ? 'true' : 'false' }}"
            data-filter-form-id="{{ $filterFormId }}">
        
        <i class="{{ $icon }}"></i>
        @if($slot->isNotEmpty())
            <span class="d-none d-sm-inline">{{ $slot }}</span>
        @else
            <span class="d-none d-sm-inline">Export</span>
        @endif
    </button>
@endif

<!-- Export Progress Modal -->
<div class="modal fade" id="exportProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="export-progress-spinner">
                    <div class="spinner-border text-success mb-3" role="status">
                        <span class="sr-only">Exporting...</span>
                    </div>
                </div>
                <h5 class="export-progress-text">Preparing Export...</h5>
                <p class="text-muted small mb-0">Please wait while we process your data</p>
                <div class="progress mt-3" style="display: none;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Filters Modal -->
@if($withFilters)
<div class="modal fade" id="exportFiltersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter text-primary mr-2"></i>
                    Export Filters
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportFiltersForm">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.date-range-picker 
                                start-id="export_start_date"
                                end-id="export_end_date"
                                label="Date Range"
                                :required="false">
                            </x-forms.date-range-picker>
                        </div>
                        <div class="col-md-6">
                            <label for="export_status" class="form-label">Status Filter</label>
                            <select id="export_status" name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="export_search" class="form-label">Search Term</label>
                            <input type="text" 
                                   id="export_search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Filter by name, email, etc.">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="exportWithFilters()">
                    <i class="fas fa-download mr-1"></i> Export with Filters
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- JavaScript and CSS moved to /public/admin/js/components.js and /resources/sass/app.scss --}}