{{--
    Generic Data Table Component
    
    Usage:
    <x-tables.data-table 
        :headers="[
            ['key' => 'name', 'label' => 'Name', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'class' => 'text-center']
        ]"
        :rows="$users"
        :actions="true"
        search-placeholder="Search users..."
        :show-export="true"
        export-url="/users/export"
        :pagination="$users">
        
        <x-slot name="row" slot-scope="{ item }">
            <td>{{ $item->name }}</td>
            <td>{{ $item->email }}</td>
        </x-slot>
        
        <x-slot name="actions" slot-scope="{ item }">
            <button class="btn btn-primary btn-sm">Edit</button>
            <button class="btn btn-danger btn-sm">Delete</button>
        </x-slot>
    </x-tables.data-table>
--}}

@props([
    'headers' => [],
    'rows' => collect(),
    'actions' => false,
    'searchPlaceholder' => 'Search...',
    'showExport' => false,
    'exportUrl' => '',
    'exportText' => 'Export',
    'pagination' => null,
    'emptyMessage' => 'No records found',
    'tableClass' => 'table table-hover',
    'responsive' => true,
    'striped' => true,
    'bordered' => false,
    'showSearch' => true,
    'showPerPage' => true,
    'perPageOptions' => [10, 25, 50, 100],
    'currentPerPage' => 10
])

<div class="data-table-container">
    <!-- Table Controls -->
    @if($showSearch || $showExport || $showPerPage)
        <div class="table-controls d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <!-- Left Controls -->
            <div class="d-flex align-items-center gap-2">
                @if($showPerPage)
                    <div class="per-page-selector">
                        <select class="form-select form-select-sm" onchange="changePerPage(this.value)" style="width: auto;">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" {{ $option == $currentPerPage ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted ms-1">per page</small>
                    </div>
                @endif
            </div>
            
            <!-- Right Controls -->
            <div class="d-flex align-items-center gap-2">
                @if($showSearch)
                    <x-forms.search-field 
                        id="dataTableSearch"
                        placeholder="{{ $searchPlaceholder }}"
                        oninput="filterTable(this.value)"
                        :with-button="false"
                        :clear-button="true" />
                @endif
                
                @if($showExport && $exportUrl)
                    <a href="{{ $exportUrl }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> {{ $exportText }}
                    </a>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Table -->
    <div class="table-responsive {{ $responsive ? '' : 'd-block' }}">
        <table class="{{ $tableClass }} {{ $striped ? 'table-striped' : '' }} {{ $bordered ? 'table-bordered' : '' }}">
            <thead class="table-dark">
                <tr>
                    @foreach($headers as $header)
                        @php
                            $key = $header['key'] ?? '';
                            $label = $header['label'] ?? ucfirst($key);
                            $sortable = $header['sortable'] ?? false;
                            $class = $header['class'] ?? '';
                            $width = $header['width'] ?? '';
                        @endphp
                        
                        <th class="{{ $class }}" 
                            @if($width) style="width: {{ $width }}" @endif
                            @if($sortable) 
                                onclick="sortTable('{{ $key }}')" 
                                style="cursor: pointer;" 
                                title="Click to sort"
                            @endif>
                            {{ $label }}
                            @if($sortable)
                                <i class="fas fa-sort ms-1 sort-icon" data-column="{{ $key }}"></i>
                            @endif
                        </th>
                    @endforeach
                    
                    @if($actions)
                        <th class="text-center" style="width: 150px;">Actions</th>
                    @endif
                </tr>
            </thead>
            
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        {{ $slot ?? '' }}
                        
                        @if($actions)
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    {{ $actions ?? '' }}
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($pagination && method_exists($pagination, 'links'))
        <div class="table-pagination mt-3">
            {{ $pagination->links() }}
        </div>
    @endif
    
    <!-- Table Info -->
    @if($rows->count() > 0)
        <div class="table-info mt-2">
            <small class="text-muted">
                Showing {{ $pagination ? $pagination->firstItem() : 1 }} 
                to {{ $pagination ? $pagination->lastItem() : $rows->count() }} 
                of {{ $pagination ? $pagination->total() : $rows->count() }} results
            </small>
        </div>
    @endif
</div>

<script>
let currentSort = { column: '', direction: 'asc' };

function sortTable(column) {
    // Toggle sort direction
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    // Update sort icons
    updateSortIcons(column, currentSort.direction);
    
    // Trigger sort event (can be handled by parent page)
    const event = new CustomEvent('tableSort', {
        detail: { column: column, direction: currentSort.direction }
    });
    document.dispatchEvent(event);
    
    // If no custom handler, perform client-side sort
    setTimeout(() => {
        if (!event.defaultPrevented) {
            performClientSort(column, currentSort.direction);
        }
    }, 100);
}

function updateSortIcons(activeColumn, direction) {
    // Reset all sort icons
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.className = 'fas fa-sort ms-1 sort-icon';
    });
    
    // Set active sort icon
    const activeIcon = document.querySelector(`[data-column="${activeColumn}"]`);
    if (activeIcon) {
        activeIcon.className = `fas fa-sort-${direction === 'asc' ? 'up' : 'down'} ms-1 sort-icon`;
    }
}

function performClientSort(column, direction) {
    const table = document.querySelector('.data-table-container table tbody');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        // Find the column index
        let columnIndex = -1;
        const headers = document.querySelectorAll('.data-table-container thead th');
        headers.forEach((header, index) => {
            if (header.onclick && header.onclick.toString().includes(column)) {
                columnIndex = index;
            }
        });
        
        if (columnIndex === -1) return 0;
        
        const aValue = a.cells[columnIndex]?.textContent.trim() || '';
        const bValue = b.cells[columnIndex]?.textContent.trim() || '';
        
        // Try numeric comparison first
        const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
        
        let result = 0;
        if (!isNaN(aNum) && !isNaN(bNum)) {
            result = aNum - bNum;
        } else {
            result = aValue.localeCompare(bValue);
        }
        
        return direction === 'desc' ? -result : result;
    });
    
    // Re-append sorted rows
    rows.forEach(row => table.appendChild(row));
}

function filterTable(searchTerm) {
    const table = document.querySelector('.data-table-container table tbody');
    const rows = table.querySelectorAll('tr');
    
    searchTerm = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
            // Skip empty state row
            return;
        }
        
        const text = Array.from(row.cells).map(cell => cell.textContent).join(' ').toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
    
    // Update visible count
    updateVisibleCount();
}

function updateVisibleCount() {
    const table = document.querySelector('.data-table-container table tbody');
    const rows = table.querySelectorAll('tr');
    const visibleRows = Array.from(rows).filter(row => 
        row.style.display !== 'none' && 
        !(row.cells.length === 1 && row.cells[0].colSpan > 1)
    );
    
    const info = document.querySelector('.table-info small');
    if (info && visibleRows.length > 0) {
        info.textContent = `Showing ${visibleRows.length} results (filtered)`;
    }
}

function changePerPage(perPage) {
    // Trigger event for parent to handle
    const event = new CustomEvent('perPageChange', {
        detail: { perPage: parseInt(perPage) }
    });
    document.dispatchEvent(event);
    
    // If no custom handler, reload page with per_page parameter
    setTimeout(() => {
        if (!event.defaultPrevented) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }
    }, 100);
}

// Initialize table features
document.addEventListener('DOMContentLoaded', function() {
    // Set up keyboard navigation for sortable headers
    document.querySelectorAll('th[onclick]').forEach(header => {
        header.setAttribute('tabindex', '0');
        header.setAttribute('role', 'button');
        
        header.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});
</script>

<style>
.data-table-container .table th[onclick]:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.data-table-container .table th[onclick]:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

.data-table-container .sort-icon {
    opacity: 0.6;
    transition: opacity 0.2s;
}

.data-table-container th:hover .sort-icon {
    opacity: 1;
}

.data-table-container .table-controls {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.data-table-container .per-page-selector {
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .data-table-container .table-controls {
        flex-direction: column;
        align-items: stretch !important;
        gap: 10px !important;
    }
    
    .data-table-container .table-controls > div {
        justify-content: center;
    }
}
</style>