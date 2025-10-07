@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Message Templates</h2>
        @can('notification-template-create')
        <a href="{{ route('notification-templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Template
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @forelse($groupedTemplates as $type => $templates)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ ucwords(str_replace('_', ' ', $type)) }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Channel</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                        <tr>
                            <td>{{ $template->name }}</td>
                            <td>
                                @if($template->channel === 'whatsapp')
                                    <span class="badge bg-success">WhatsApp</span>
                                @elseif($template->channel === 'email')
                                    <span class="badge bg-info">Email</span>
                                @else
                                    <span class="badge bg-primary">Both</span>
                                @endif
                            </td>
                            <td>{{ $template->subject ?? '-' }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @can('notification-template-edit')
                                <a href="{{ route('notification-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('notification-template-delete')
                                <form action="{{ route('notification-templates.delete', $template) }}"
                                      method="POST"
                                      class="d-inline"
                                      data-confirm-submit="true"
                                      data-title="Confirm Deletion"
                                      data-message="Are you sure you want to delete the template for <strong>{{ $template->notificationType->name ?? 'this notification' }}</strong>?"
                                      data-confirm-text="Yes, Delete"
                                      data-confirm-class="btn-danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">
        No templates found. <a href="{{ route('notification-templates.create') }}">Create your first template</a>
    </div>
    @endforelse
</div>
@endsection
