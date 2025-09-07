{{--
    Pagination Component
    
    Usage:
    <x-tables.pagination 
        :paginator="$users"
        :show-info="true"
        :show-per-page="true"
        :per-page-options="[10, 25, 50, 100]"
        info-text="Showing {start} to {end} of {total} users">
    </x-tables.pagination>
--}}

@props([
    'paginator' => null,
    'showInfo' => true,
    'showPerPage' => true,
    'perPageOptions' => [10, 25, 50, 100],
    'infoText' => 'Showing {start} to {end} of {total} results',
    'previousText' => 'Previous',
    'nextText' => 'Next',
    'maxLinks' => 5
])

@if($paginator && $paginator->hasPages())
    <div class="pagination-container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <!-- Pagination Info -->
        @if($showInfo)
            <div class="pagination-info">
                <small class="text-muted">
                    @php
                        $start = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
                        $end = min($paginator->currentPage() * $paginator->perPage(), $paginator->total());
                        $total = $paginator->total();
                        
                        $info = str_replace(
                            ['{start}', '{end}', '{total}'],
                            [$start, $end, $total],
                            $infoText
                        );
                    @endphp
                    {{ $info }}
                </small>
            </div>
        @endif
        
        <!-- Pagination Links -->
        <div class="pagination-links d-flex align-items-center gap-2">
            <!-- Per Page Selector -->
            @if($showPerPage)
                <div class="per-page-selector d-flex align-items-center me-3">
                    <label for="perPageSelect" class="form-label mb-0 me-2">
                        <small class="text-muted">Per page:</small>
                    </label>
                    <select id="perPageSelect" 
                            class="form-select form-select-sm" 
                            style="width: auto; min-width: 80px;"
                            onchange="changePerPage(this.value)">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" 
                                    {{ $option == $paginator->perPage() ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            <!-- Pagination Navigation -->
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Page Link -->
                    @if($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">{{ $previousText }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                                {{ $previousText }}
                            </a>
                        </li>
                    @endif
                    
                    <!-- Pagination Elements -->
                    @php
                        $start = max(1, $paginator->currentPage() - floor($maxLinks / 2));
                        $end = min($paginator->lastPage(), $start + $maxLinks - 1);
                        
                        // Adjust start if we're near the end
                        if ($end - $start < $maxLinks - 1) {
                            $start = max(1, $end - $maxLinks + 1);
                        }
                    @endphp
                    
                    <!-- First Page -->
                    @if($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                        </li>
                        @if($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif
                    
                    <!-- Page Numbers -->
                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor
                    
                    <!-- Last Page -->
                    @if($end < $paginator->lastPage())
                        @if($end < $paginator->lastPage() - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">
                                {{ $paginator->lastPage() }}
                            </a>
                        </li>
                    @endif
                    
                    <!-- Next Page Link -->
                    @if($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                                {{ $nextText }}
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">{{ $nextText }}</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
    
    <script>
    function changePerPage(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }
    
    // Keyboard navigation for pagination
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination .page-link');
        
        paginationLinks.forEach(link => {
            link.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
    </script>
    
    <style>
    .pagination-container {
        padding: 15px 0;
        border-top: 1px solid #dee2e6;
    }
    
    .pagination-info {
        flex-shrink: 0;
    }
    
    .per-page-selector label {
        white-space: nowrap;
    }
    
    .pagination .page-link {
        color: #0d6efd;
        border: 1px solid #dee2e6;
    }
    
    .pagination .page-link:hover {
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pagination-container {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px !important;
        }
        
        .pagination-links {
            flex-direction: column;
            align-items: stretch !important;
        }
        
        .per-page-selector {
            justify-content: center;
            margin-bottom: 10px !important;
        }
        
        .pagination {
            justify-content: center;
        }
        
        .pagination-info {
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }
    </style>
@endif