@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title"><i class="fas fa-list"></i> Security Audit Logs</h4>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filters
                            </button>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportLogs('csv')">
                                    <i class="fas fa-download"></i> CSV
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportLogs('json')">
                                    <i class="fas fa-download"></i> JSON
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Active Filters Display -->
                    @if(!empty(array_filter($filters)))
                        <div class="alert alert-info">
                            <strong>Active Filters:</strong>
                            @foreach($filters as $key => $value)
                                @if($value)
                                    <span class="badge badge-primary me-2">{{ ucwords(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                @endif
                            @endforeach
                            <a href="{{ route('security.audit-logs') }}" class="btn btn-sm btn-outline-secondary ms-2">Clear All</a>
                        </div>
                    @endif

                    <!-- Audit Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Event</th>
                                    <th>Category</th>
                                    <th>Actor</th>
                                    <th>Target</th>
                                    <th>IP Address</th>
                                    <th>Risk</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="{{ $log->is_suspicious ? 'table-warning' : '' }}">
                                        <td>
                                            <div>{{ $log->occurred_at->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ $log->occurred_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $log->event }}</span>
                                            @if($log->is_suspicious)
                                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Suspicious Activity"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $log->event_category }}</span>
                                        </td>
                                        <td>
                                            @if($log->actor)
                                                <div>{{ class_basename($log->actor_type) }}#{{ $log->actor_id }}</div>
                                                @if($log->actor)
                                                    <small class="text-muted">
                                                        @if(method_exists($log->actor, 'getFullNameAttribute'))
                                                            {{ $log->actor->full_name ?? $log->actor->name }}
                                                        @else
                                                            {{ $log->actor->name ?? $log->actor->email }}
                                                        @endif
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->auditable)
                                                <div>{{ class_basename($log->auditable_type) }}#{{ $log->auditable_id }}</div>
                                                @if($log->auditable && method_exists($log->auditable, 'getDisplayNameAttribute'))
                                                    <small class="text-muted">{{ $log->auditable->display_name }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $log->ip_address }}</code>
                                            @if($log->formatted_location)
                                                <br><small class="text-muted">{{ $log->formatted_location }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $log->risk_badge_class }}">
                                                {{ $log->risk_level }} ({{ $log->risk_score }})
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="viewLogDetails({{ $log->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-search fa-2x mb-2"></i>
                                            <div>No audit logs found matching your criteria</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Audit Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('security.audit-logs') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event" class="form-label">Event</label>
                                <select name="event" id="event" class="form-select">
                                    <option value="">All Events</option>
                                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                                    <option value="login" {{ request('event') === 'login' ? 'selected' : '' }}>Login</option>
                                    <option value="logout" {{ request('event') === 'logout' ? 'selected' : '' }}>Logout</option>
                                    <option value="login_failed" {{ request('event') === 'login_failed' ? 'selected' : '' }}>Failed Login</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_category" class="form-label">Category</label>
                                <select name="event_category" id="event_category" class="form-select">
                                    <option value="">All Categories</option>
                                    <option value="authentication" {{ request('event_category') === 'authentication' ? 'selected' : '' }}>Authentication</option>
                                    <option value="authorization" {{ request('event_category') === 'authorization' ? 'selected' : '' }}>Authorization</option>
                                    <option value="data_access" {{ request('event_category') === 'data_access' ? 'selected' : '' }}>Data Access</option>
                                    <option value="system" {{ request('event_category') === 'system' ? 'selected' : '' }}>System</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="risk_level" class="form-label">Risk Level</label>
                                <select name="risk_level" id="risk_level" class="form-select">
                                    <option value="">All Risk Levels</option>
                                    <option value="low" {{ request('risk_level') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('risk_level') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="critical" {{ request('risk_level') === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_suspicious" class="form-label">Suspicious Activity</label>
                                <select name="is_suspicious" id="is_suspicious" class="form-select">
                                    <option value="">All Activity</option>
                                    <option value="1" {{ request('is_suspicious') === '1' ? 'selected' : '' }}>Suspicious Only</option>
                                    <option value="0" {{ request('is_suspicious') === '0' ? 'selected' : '' }}>Normal Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control"
                               placeholder="192.168.1.1" value="{{ request('ip_address') }}">
                    </div>
                    <div class="mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Search in events, IP addresses, user agents..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logDetailsBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function exportLogs(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);

    const exportUrl = '{{ route("security.export-logs") }}?' + params.toString();
    window.open(exportUrl, '_blank');
}

function viewLogDetails(logId) {
    // In a real implementation, you'd fetch the log details via AJAX
    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    document.getElementById('logDetailsBody').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    modal.show();

    // Simulate loading details
    setTimeout(() => {
        document.getElementById('logDetailsBody').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Basic Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>ID:</strong></td><td>${logId}</td></tr>
                        <tr><td><strong>Event:</strong></td><td>example_event</td></tr>
                        <tr><td><strong>Category:</strong></td><td>data_access</td></tr>
                        <tr><td><strong>Risk Score:</strong></td><td>45</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Context</h6>
                    <table class="table table-sm">
                        <tr><td><strong>IP Address:</strong></td><td>192.168.1.100</td></tr>
                        <tr><td><strong>User Agent:</strong></td><td>Mozilla/5.0...</td></tr>
                        <tr><td><strong>Session ID:</strong></td><td>abc123...</td></tr>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <h6>Risk Factors</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-warning">unusual_time</span>
                    <span class="badge badge-warning">new_ip</span>
                </div>
            </div>
        `;
    }, 1000);
}

// Auto-refresh every 30 seconds if viewing real-time logs
document.addEventListener('DOMContentLoaded', function() {
    // Only auto-refresh if no specific filters are applied
    const hasFilters = window.location.search.includes('=');
    if (!hasFilters) {
        setInterval(function() {
            location.reload();
        }, 30000);
    }
});
</script>
@endsection