# Enhanced Edit View with Version History

This file contains the complete enhanced edit.blade.php with version history, analytics, and improved UI.

## File: resources/views/admin/notification_templates/edit_enhanced.blade.php

```blade
@extends('layouts.app')

@section('title', 'Edit Notification Template')

@section('styles')
<style>
/* Existing styles preserved */
#preview_customer_id + .select2-container .select2-selection--single,
#preview_insurance_id + .select2-container .select2-selection--single,
#preview_quotation_id + .select2-container .select2-selection--single {
    height: calc(1.5em + 0.5rem + 2px) !important;
    padding: 0.25rem 0.5rem !important;
    font-size: 0.875rem !important;
}

/* Version History Styles */
.version-item {
    border-left: 3px solid #4e73df;
    transition: all 0.2s;
}

.version-item:hover {
    background-color: #f8f9fc;
    border-left-color: #224abe;
}

.version-content {
    max-height: 200px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}

.diff-added {
    background-color: #d4edda;
    color: #155724;
}

.diff-removed {
    background-color: #f8d7da;
    color: #721c24;
}

.timeline-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.analytics-card {
    border-left: 4px solid #4e73df;
}

.stat-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
}

.stat-label {
    font-size: 12px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}
</style>
@endsection

@section('content')

    <div class="container-fluid">

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-3" id="templateTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit-panel" type="button">
                    <i class="fas fa-edit"></i> Edit Template
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-panel" type="button">
                    <i class="fas fa-history"></i> Version History
                    <span class="badge bg-primary" id="versionCount">{{ $template->versions_count ?? 0 }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-panel" type="button">
                    <i class="fas fa-chart-line"></i> Analytics
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="templateTabContent">

            <!-- Edit Panel -->
            <div class="tab-pane fade show active" id="edit-panel" role="tabpanel">
                <!-- Notification Template Form -->
                <div class="card shadow mb-3">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">Edit Notification Template</h6>
                        <a href="{{ route('notification-templates.index') }}" onclick="window.history.go(-1); return false;"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                            <i class="fas fa-chevron-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('notification-templates.update', $template) }}" id="templateForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="template_id" value="{{ $template->id }}">

                        <div class="card-body p-2">
                            <div class="row g-2">
                                <!-- Left Column: Form -->
                                <div class="col-md-6">
                                    <!-- Basic Details -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold mb-1"><span class="text-danger">*</span> Notification Type</label>
                                            <select class="form-control form-control-sm @error('notification_type_id') is-invalid @enderror"
                                                    id="notification_type_id" name="notification_type_id" required>
                                                <option value="">Select Type</option>
                                                @foreach($notificationTypes->groupBy('category') as $category => $types)
                                                    <optgroup label="{{ ucwords($category) }}">
                                                        @foreach($types as $type)
                                                            <option value="{{ $type->id }}"
                                                                {{ old('notification_type_id', $template->notification_type_id) == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @error('notification_type_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold mb-1"><span class="text-danger">*</span> Channel</label>
                                            <select class="form-control form-control-sm @error('channel') is-invalid @enderror"
                                                    id="channel" name="channel" required>
                                                <option value="">Select</option>
                                                <option value="whatsapp" {{ old('channel', $template->channel) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                                <option value="email" {{ old('channel', $template->channel) === 'email' ? 'selected' : '' }}>Email</option>
                                            </select>
                                            @error('channel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold mb-1">Status</label>
                                            <select class="form-control form-control-sm" id="is_active" name="is_active">
                                                <option value="1" {{ old('is_active', $template->is_active) ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !old('is_active', $template->is_active) ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Email Subject (conditional) -->
                                    <div class="mb-2" id="subjectSection" style="{{ old('channel', $template->channel) === 'email' ? '' : 'display:none;' }}">
                                        <label class="form-label fw-semibold mb-1"><span class="text-danger">*</span> Email Subject</label>
                                        <input type="text" class="form-control form-control-sm @error('subject') is-invalid @enderror"
                                               id="subject" name="subject" value="{{ old('subject', $template->subject) }}"
                                               placeholder="Enter email subject">
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Template Content -->
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold mb-1"><span class="text-danger">*</span> Message Template</label>
                                        <textarea class="form-control form-control-sm font-monospace @error('template_content') is-invalid @enderror"
                                                  id="template_content" name="template_content" rows="8" required
                                                  placeholder="Use {{variable_name}} for dynamic content">{{ old('template_content', $template->template_content) }}</textarea>
                                        @error('template_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Available Variables (Dynamic Loading) -->
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-semibold mb-0">Available Variables</label>
                                            <small class="text-muted"><i class="fas fa-mouse-pointer"></i> Click to insert or copy</small>
                                        </div>
                                        <div class="border rounded bg-white">
                                            <div id="variablesContainer" class="accordion accordion-flush">
                                                <div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading variables...</div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="available_variables" name="available_variables" value='{{ old('available_variables', json_encode($template->available_variables ?? [])) }}'>
                                    </div>
                                </div>

                                <!-- Right Column: Preview & Test -->
                                <div class="col-md-6">
                                    <!-- Preview Data Selector -->
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold mb-1"><i class="fas fa-database me-1"></i> Preview With Real Data</label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <select class="form-control form-control-sm select2" id="preview_customer_id">
                                                    <option value="">Random Customer</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->name }}
                                                            @if($customer->mobile_number) - {{ $customer->mobile_number }}@endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control form-control-sm select2" id="preview_insurance_id" disabled>
                                                    <option value="">Select customer first</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control form-control-sm select2" id="preview_quotation_id" disabled>
                                                    <option value="">Select customer first</option>
                                                </select>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Select customer to load their policies and quotations</small>
                                    </div>

                                    <!-- Live Preview -->
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-semibold mb-0"><i class="fas fa-eye me-1"></i> Live Preview</label>
                                            <button type="button" class="btn btn-info btn-sm py-0 px-2" id="refreshPreview">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                        <div id="previewContent" class="border rounded p-2 bg-light font-monospace" style="height: 200px; overflow-y: auto; white-space: pre-wrap; font-size: 13px;">
                                            <span class="text-muted">Preview will appear here...</span>
                                        </div>
                                        <small id="previewContext" class="text-muted d-block mt-1"></small>
                                    </div>

                                    <!-- Send Test -->
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold mb-1"><i class="fas fa-paper-plane me-1"></i> Send Test Message</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control form-control-sm" id="test_recipient"
                                                   placeholder="Phone: 919727793123 or Email: test@example.com">
                                            <button type="button" class="btn btn-warning btn-sm" id="sendTestBtn">
                                                <i class="fas fa-paper-plane"></i> Send
                                            </button>
                                        </div>
                                        <div id="testResult" class="mt-1"></div>
                                    </div>

                                    <!-- Quick Stats -->
                                    <div class="card analytics-card mb-2">
                                        <div class="card-body p-2">
                                            <h6 class="fw-bold mb-2">Quick Stats</h6>
                                            <div class="row g-2 text-center">
                                                <div class="col-4">
                                                    <div class="border rounded p-2">
                                                        <div class="fw-bold text-primary" id="stat_versions">{{ $template->versions_count ?? 0 }}</div>
                                                        <small class="text-muted">Versions</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-2">
                                                        <div class="fw-bold text-success" id="stat_variables">{{ count($template->available_variables ?? []) }}</div>
                                                        <small class="text-muted">Variables</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border rounded p-2">
                                                        <div class="fw-bold text-info" id="stat_tests">0</div>
                                                        <small class="text-muted">Tests</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer py-2 bg-light">
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-secondary btn-sm px-4" href="{{ route('notification-templates.index') }}">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success btn-sm px-4">
                                    <i class="fas fa-save me-1"></i>Update Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Version History Panel -->
            <div class="tab-pane fade" id="history-panel" role="tabpanel">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold text-primary">Template Version History</h6>
                    </div>
                    <div class="card-body">
                        <div id="versionHistoryContent">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Loading version history...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Panel -->
            <div class="tab-pane fade" id="analytics-panel" role="tabpanel">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold text-primary">Template Analytics</h6>
                    </div>
                    <div class="card-body">
                        <div id="analyticsContent">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Loading analytics...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Version Compare Modal -->
    <div class="modal fade" id="compareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compare Versions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Current Version</h6>
                            <div id="compareLeft" class="border rounded p-3 bg-light" style="max-height: 500px; overflow-y: auto;">
                                <!-- Content loaded via JS -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Version <span id="compareVersionNumber"></span></h6>
                            <div id="compareRight" class="border rounded p-3 bg-light" style="max-height: 500px; overflow-y: auto;">
                                <!-- Content loaded via JS -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="restoreVersionBtn">
                        <i class="fas fa-undo"></i> Restore This Version
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateId = {{ $template->id }};

    // [Include all existing JavaScript from edit.blade.php here]
    // ... channel select, variables loading, preview, test send ...

    // NEW: Load version history when tab is clicked
    document.getElementById('history-tab').addEventListener('shown.bs.tab', function() {
        loadVersionHistory();
    });

    // NEW: Load analytics when tab is clicked
    document.getElementById('analytics-tab').addEventListener('shown.bs.tab', function() {
        loadAnalytics();
    });

    // Load version history
    function loadVersionHistory() {
        const container = document.getElementById('versionHistoryContent');
        container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>';

        fetch(`/notification-templates/${templateId}/version-history`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.versions.length > 0) {
                    displayVersionHistory(data.versions);
                } else {
                    container.innerHTML = '<div class="alert alert-info">No version history available</div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<div class="alert alert-danger">Failed to load version history</div>';
            });
    }

    // Display version history
    function displayVersionHistory(versions) {
        const container = document.getElementById('versionHistoryContent');

        let html = '<div class="timeline">';

        versions.forEach((version, index) => {
            const badgeColor = version.change_type === 'create' ? 'success' :
                             version.change_type === 'update' ? 'primary' :
                             version.change_type === 'restore' ? 'warning' :
                             version.change_type === 'import' ? 'info' : 'secondary';

            html += `
                <div class="version-item border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-${badgeColor}">Version ${version.version_number}</span>
                            <span class="badge bg-light text-dark ms-1">${version.change_type}</span>
                            <span class="text-muted ms-2">${version.changed_at_human}</span>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary compare-btn" data-version-id="${version.id}" data-version-number="${version.version_number}">
                                <i class="fas fa-exchange-alt"></i> Compare
                            </button>
                            ${index > 0 ? `
                                <button class="btn btn-outline-success restore-btn" data-version-id="${version.id}" data-version-number="${version.version_number}">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Changed by:</strong> ${version.changed_by}
                        <strong class="ms-3">Date:</strong> ${version.changed_at}
                    </div>
                    ${version.change_notes ? `<div class="mb-2"><strong>Notes:</strong> ${version.change_notes}</div>` : ''}
                    <div class="version-content">${escapeHtml(version.template_content)}</div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;

        // Attach event listeners
        container.querySelectorAll('.compare-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const versionId = this.dataset.versionId;
                const versionNumber = this.dataset.versionNumber;
                compareVersion(versionId, versionNumber);
            });
        });

        container.querySelectorAll('.restore-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const versionId = this.dataset.versionId;
                const versionNumber = this.dataset.versionNumber;
                restoreVersion(versionId, versionNumber);
            });
        });
    }

    // Compare version
    function compareVersion(versionId, versionNumber) {
        // Load both current and selected version
        const currentContent = document.getElementById('template_content').value;

        fetch(`/notification-templates/${templateId}/version-history`)
            .then(response => response.json())
            .then(data => {
                const version = data.versions.find(v => v.id == versionId);
                if (version) {
                    document.getElementById('compareLeft').innerText = currentContent;
                    document.getElementById('compareRight').innerText = version.template_content;
                    document.getElementById('compareVersionNumber').innerText = versionNumber;
                    document.getElementById('restoreVersionBtn').dataset.versionId = versionId;

                    const modal = new bootstrap.Modal(document.getElementById('compareModal'));
                    modal.show();
                }
            });
    }

    // Restore version
    function restoreVersion(versionId, versionNumber) {
        if (!confirm(`Restore template to version ${versionNumber}? Current version will be backed up.`)) {
            return;
        }

        fetch(`/notification-templates/${templateId}/restore-version`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ version_id: versionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                location.reload();
            } else {
                toastr.error(data.message || 'Restore failed');
            }
        })
        .catch(error => {
            toastr.error('Error: ' + error.message);
        });
    }

    // Restore from compare modal
    document.getElementById('restoreVersionBtn').addEventListener('click', function() {
        const versionId = this.dataset.versionId;
        const modal = bootstrap.Modal.getInstance(document.getElementById('compareModal'));
        modal.hide();

        // Extract version number from display
        const versionNumber = document.getElementById('compareVersionNumber').innerText;
        restoreVersion(versionId, versionNumber);
    });

    // Load analytics
    function loadAnalytics() {
        const container = document.getElementById('analyticsContent');

        fetch(`/notification-templates/${templateId}/analytics`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayAnalytics(data.analytics);
                } else {
                    container.innerHTML = '<div class="alert alert-danger">Failed to load analytics</div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            });
    }

    // Display analytics
    function displayAnalytics(analytics) {
        const container = document.getElementById('analyticsContent');

        container.innerHTML = `
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number">${analytics.versions_count}</div>
                        <div class="stat-label">Total Versions</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="stat-number">${analytics.test_sends}</div>
                        <div class="stat-label">Test Sends</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="stat-number">${analytics.variables_used.length}</div>
                        <div class="stat-label">Variables Used</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="stat-number">${analytics.character_count}</div>
                        <div class="stat-label">Characters</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title fw-bold">Variable Usage</h6>
                            <div class="mb-3">
                                <strong>Used Variables (${analytics.variables_used.length}):</strong>
                                <div class="mt-2">
                                    ${analytics.variables_used.length > 0
                                        ? analytics.variables_used.map(v => `<span class="badge bg-success me-1 mb-1">${v}</span>`).join('')
                                        : '<span class="text-muted">None</span>'}
                                </div>
                            </div>
                            <div>
                                <strong>Unused Variables (${analytics.variables_unused.length}):</strong>
                                <div class="mt-2">
                                    ${analytics.variables_unused.length > 0
                                        ? analytics.variables_unused.slice(0, 15).map(v => `<span class="badge bg-secondary me-1 mb-1">${v}</span>`).join('')
                                        : '<span class="text-success">All variables used</span>'}
                                    ${analytics.variables_unused.length > 15 ? `<span class="text-muted d-block mt-1">... +${analytics.variables_unused.length - 15} more</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title fw-bold">Template Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-semibold">Template Name:</td>
                                    <td>${analytics.template_name}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Channel:</td>
                                    <td><span class="badge bg-primary">${analytics.channel}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Character Count:</td>
                                    <td>${analytics.character_count} characters</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Word Count:</td>
                                    <td>${analytics.word_count} words</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Last Modified:</td>
                                    <td>${analytics.last_modified}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Modified By:</td>
                                    <td>${analytics.last_modified_by}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title fw-bold">Test Send Statistics</h6>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-0">${analytics.test_sends}</h3>
                                <small class="text-muted">Total Tests</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-0">${analytics.test_success}</h3>
                                <small class="text-muted">Successful</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-danger mb-0">${analytics.test_failed}</h3>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Update quick stats in edit panel
        document.getElementById('stat_versions').textContent = analytics.versions_count;
        document.getElementById('stat_variables').textContent = analytics.variables_used.length;
        document.getElementById('stat_tests').textContent = analytics.test_sends;
    }

    // Utility function
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection
```

## Usage Instructions

1. **Replace the current edit.blade.php** with this enhanced version
2. **The new features include**:
   - Tab-based interface (Edit | Version History | Analytics)
   - Version history with timeline view
   - Compare versions side-by-side
   - One-click version restore
   - Comprehensive analytics dashboard
   - Quick stats in edit panel
   - Enhanced UI/UX

3. **User workflow**:
   - Edit tab: Make changes to template
   - Version History tab: View all past versions, compare, and restore
   - Analytics tab: View usage statistics and insights

All existing functionality is preserved and enhanced.
