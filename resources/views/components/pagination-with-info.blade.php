{{--
    Reusable Pagination Component with Record Count
    
    Usage:
    <x-pagination-with-info :paginator="$items" :request="$request" />
    
    Parameters:
    - paginator: The paginated collection (required)
    - request: Current request object for preserving filters (optional)
--}}

@props(['paginator', 'request' => null])

<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
        <i class="fas fa-info-circle me-1"></i>
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} 
        of {{ $paginator->total() }} total records
    </div>
    <div>
        @if($request)
            {{ $paginator->appends($request)->links() }}
        @else
            {{ $paginator->links() }}
        @endif
    </div>
</div>