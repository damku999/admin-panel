# UI Component Library Guide
**Laravel Insurance Management System**

*A comprehensive guide for maintaining design consistency and implementing new UI features*

---

## Table of Contents

1. [Overview](#overview)
2. [UI Component Patterns](#ui-component-patterns)
3. [User Experience Flows](#user-experience-flows)
4. [Responsive Design Strategy](#responsive-design-strategy)
5. [Asset Organization](#asset-organization)
6. [Component Library](#component-library)
7. [Implementation Guidelines](#implementation-guidelines)
8. [Best Practices](#best-practices)

---

## Overview

This Laravel insurance management system employs a dual-interface architecture with distinct design patterns for admin and customer portals. The system leverages Bootstrap 5 as the core framework with custom SCSS layers for theming and responsive behavior.

### Architecture Summary

- **Framework**: Laravel 10 + Bootstrap 5 + jQuery
- **CSS Methodology**: SCSS with component-based architecture
- **JavaScript Pattern**: jQuery-centric with modern ES6 features
- **Icon System**: FontAwesome 6 (Solid, Brands)
- **Build Tool**: Laravel Mix
- **Asset Compilation**: Webpack with Hot Module Replacement

---

## UI Component Patterns

### 1. Layout Structure

#### Admin Portal Layout
```html
<!-- Pattern: Admin Sidebar + Topbar Layout -->
<div id="wrapper">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar"><!-- Collapsible Sidebar --></div>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar"><!-- Topbar --></nav>
            @yield('content')
        </div>
        <footer><!-- Footer --></footer>
    </div>
</div>
```

#### Customer Portal Layout
```html
<!-- Pattern: Simple Navigation Layout -->
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand-lg sticky-top">
                <!-- Customer Navigation -->
            </nav>
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <footer><!-- Footer --></footer>
    </div>
</div>
```

### 2. Bootstrap 5 Usage and Customization

#### Color System
```scss
// Admin Portal Colors (SB Admin 2 Compatible)
$primary: #4e73df;
$secondary: #858796;
$success: #1cc88a;
$info: #36b9cc;
$warning: #f6c23e;
$danger: #e74a3b;

// Customer Portal Colors (Modern Design System)
$webmonks-primary: #2563eb;
$webmonks-secondary: #64748b;
$webmonks-success: #10b981;
$webmonks-info: #06b6d4;
$webmonks-warning: #f59e0b;
$webmonks-danger: #ef4444;
```

#### Custom Bootstrap Extensions
```scss
// Enhanced Card Components
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
}

// Metric Cards for Dashboards
.card-metric {
    cursor: pointer;
    transition: all 0.3s ease;

    &:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
}
```

### 3. Blade Component Architecture

#### Reusable Modal Component
```php
<!-- File: resources/views/components/modal.blade.php -->
<div class="modal fade" id="{{ $id }}">
    <div class="modal-dialog {{ $size ?? 'modal-dialog-centered' }}">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    @if(isset($icon))<i class="{{ $icon }} me-2"></i>@endif
                    {{ $title }}
                </h5>
                <x-modal-close-button :modalId="$id" />
            </div>
            <div class="modal-body">{{ $slot }}</div>
            @if(isset($footer))
            <div class="modal-footer border-0">{{ $footer }}</div>
            @endif
        </div>
    </div>
</div>
```

#### Alert Component Pattern
```php
<!-- File: resources/views/components/alert.blade.php -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

#### Pagination with Info Component
```php
<!-- File: resources/views/components/pagination-with-info.blade.php -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
        <i class="fas fa-info-circle me-1"></i>
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }}
        of {{ $paginator->total() }} total records
    </div>
    <div>{{ $paginator->links() }}</div>
</div>
```

### 4. Form Patterns and Validation UI

#### Standard Form Layout
```html
<!-- Pattern: Form with Validation -->
<form method="POST" action="{{ route('example.store') }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save
        </button>
        <a href="{{ route('example.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancel
        </a>
    </div>
</form>
```

#### Select2 Integration Pattern
```javascript
// Initialize Select2 with Bootstrap 5 theme
$('.select2').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Select an option...',
    allowClear: true
});
```

### 5. Table/Listing Display Patterns

#### Responsive Table Pattern
```html
<!-- Pattern: Data Table with Actions -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Data Table
            </h5>
            <a href="{{ route('example.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>ID</th>
                        <th><i class="fas fa-user me-1"></i>Name</th>
                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                        <th><i class="fas fa-chart-line me-1"></i>Status</th>
                        <th><i class="fas fa-tools me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status ? 'success' : 'danger' }}">
                                <i class="fas fa-{{ $item->status ? 'check' : 'times' }}-circle me-1"></i>
                                {{ $item->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('example.show', $item->id) }}"
                                   class="btn btn-info btn-sm"
                                   data-bs-toggle="tooltip"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('example.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $item->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                            <span class="text-muted">No records found</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Component -->
        <x-pagination-with-info :paginator="$items" :request="$request" />
    </div>
</div>
```

---

## User Experience Flows

### 1. Admin Dashboard Navigation Patterns

#### Sidebar Navigation Structure
```html
<!-- Hierarchical Menu with Collapse Support -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse"
           data-target="#collapseCustomers" aria-expanded="false">
            <i class="fas fa-users"></i>
            <span>Customers</span>
        </a>
        <div id="collapseCustomers" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('customers.index') }}">All Customers</a>
                <a class="collapse-item" href="{{ route('customers.create') }}">Add Customer</a>
                <a class="collapse-item" href="{{ route('family_groups.index') }}">Family Groups</a>
            </div>
        </div>
    </li>
</ul>
```

#### Responsive Sidebar Behavior
```javascript
// Sidebar toggle functionality (jQuery-only implementation)
$('#sidebarToggleTop').on('click', function(e) {
    e.preventDefault();
    toggleSidebar();
});

function toggleSidebar() {
    const sidebar = $('#accordionSidebar');
    const overlay = $('#sidebarOverlay');

    if ($(window).width() <= 768) {
        // Mobile behavior - show/hide with overlay
        if (sidebar.hasClass('show')) {
            sidebar.removeClass('show');
            overlay.removeClass('show');
        } else {
            sidebar.addClass('show');
            overlay.addClass('show');
        }
    } else {
        // Desktop behavior - collapse/expand
        sidebar.toggleClass('toggled');
        $('#wrapper').toggleClass('sidebar-toggled');
    }
}
```

### 2. Customer Portal User Journey

#### Clean Navigation Flow
```html
<!-- Horizontal Navigation with Brand -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
            <img src="{{ asset('images/parth_logo.png') }}" style="max-height: 40px;" alt="Logo">
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                       href="{{ route('customer.dashboard') }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.policies*') ? 'active' : '' }}"
                       href="{{ route('customer.policies') }}">
                        <i class="fas fa-shield-alt me-2"></i> My Policies
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text">Welcome, {{ Auth::guard('customer')->user()->name }}</span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

### 3. Form Workflows and Multi-step Processes

#### Progressive Enhancement Pattern
```html
<!-- Step-based Form Layout -->
<div class="card">
    <div class="card-header">
        <nav aria-label="Form steps">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Basic Information</li>
                <li class="breadcrumb-item">Vehicle Details</li>
                <li class="breadcrumb-item">Insurance Options</li>
                <li class="breadcrumb-item">Review & Submit</li>
            </ol>
        </nav>
    </div>
    <div class="card-body">
        <!-- Step content with form validation -->
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" id="prevBtn">
                <i class="fas fa-arrow-left me-1"></i> Previous
            </button>
            <button type="button" class="btn btn-primary" id="nextBtn">
                Next <i class="fas fa-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</div>
```

### 4. Modal and Popup Interaction Patterns

#### Centralized Modal Management
```javascript
// Modal utility functions (Bootstrap 5 compatible)
window.showModal = function(modalId) {
    $('#' + modalId).css('display', 'block').addClass('show');
    $('body').addClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').append('<div class="modal-backdrop fade show"></div>');
};

window.hideModal = function(modalId) {
    $('#' + modalId).css('display', 'none').removeClass('show');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
};

// Close modals on Escape key
$(document).keydown(function(e) {
    if (e.keyCode === 27) { // ESC key
        $('.modal.show').each(function() {
            hideModal(this.id);
        });
    }
});
```

---

## Responsive Design Strategy

### 1. Mobile-First Approach Implementation

#### Breakpoint Strategy
```scss
// Bootstrap 5 breakpoints with custom extensions
$grid-breakpoints: (
    xs: 0,
    sm: 576px,   // Small devices (landscape phones)
    md: 768px,   // Medium devices (tablets)
    lg: 992px,   // Large devices (desktops)
    xl: 1200px,  // X-Large devices (large desktops)
    xxl: 1400px  // XX-Large devices (larger desktops)
);

// Custom responsive utilities
@media (max-width: 768px) {
    .sidebar {
        &.toggled { width: 0; }
    }

    #content { padding: 1rem; }

    .table-responsive {
        .btn-group {
            flex-direction: column;
            .btn { margin: 2px 0; }
        }
    }
}
```

### 2. Touch-Friendly Interface Elements

#### Button Sizing and Spacing
```scss
// Enhanced touch targets
.btn {
    min-height: 44px; // iOS accessibility guideline
    padding: 0.75rem 1.5rem;

    &.btn-sm {
        min-height: 36px;
        padding: 0.5rem 1rem;
    }
}

// Card interactions for touch
.card-metric {
    cursor: pointer;
    touch-action: manipulation;

    @media (max-width: 768px) {
        padding: 1rem;
        margin-bottom: 1rem;

        &:hover, &:active {
            transform: scale(0.98);
        }
    }
}
```

### 3. Accessibility Considerations

#### ARIA Labels and Screen Reader Support
```html
<!-- Proper ARIA labeling -->
<button type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#exampleModal"
        aria-label="Open example modal">
    <i class="fas fa-plus" aria-hidden="true"></i>
    <span class="d-none d-md-inline">Add New</span>
</button>

<!-- Skip navigation for keyboard users -->
<a class="sr-only sr-only-focusable" href="#main-content">Skip to main content</a>

<!-- Proper heading hierarchy -->
<h1>Page Title</h1>
  <h2>Section Title</h2>
    <h3>Subsection Title</h3>
```

#### Color Contrast and Focus States
```scss
// Enhanced focus states for accessibility
.btn:focus,
.form-control:focus,
.form-select:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

// Color contrast compliance
.text-muted { color: #6c757d !important; } // 4.5:1 contrast ratio
.bg-light { background-color: #f8f9fa !important; } // Sufficient contrast

// High contrast mode support
@media (prefers-contrast: high) {
    .card {
        border: 1px solid #000;
    }

    .btn-outline-primary {
        border-width: 2px;
    }
}
```

---

## Asset Organization

### 1. SCSS Structure and Theming

#### File Organization
```
resources/sass/
├── app.scss                 # Main entry point
├── _variables.scss          # Global variables
├── admin/
│   ├── admin.scss          # Admin-specific styles
│   └── admin-clean.scss    # Clean admin theme
└── customer/
    └── customer.scss       # Customer portal styles
```

#### Theming Architecture
```scss
// File: resources/sass/_variables.scss
$body-bg: #f8fafc;
$font-family-sans-serif: 'Nunito', sans-serif;
$font-size-base: 0.9rem;
$line-height-base: 1.6;

// File: resources/sass/admin/admin.scss
@use '~bootstrap/scss/bootstrap';
@use '~@fortawesome/fontawesome-free/scss/fontawesome';

// SB Admin 2 compatibility
$primary: #4e73df;
$sidebar-width: 224px;
$sidebar-collapsed-width: 6.5rem;

// File: resources/sass/customer/customer.scss
$webmonks-primary: #2563eb;
$card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
$border-color: #e2e8f0;
```

### 2. JavaScript Organization

#### Module Structure
```
resources/js/
├── app.js                   # Main application entry
├── bootstrap.js             # Bootstrap dependencies
├── admin/
│   ├── admin.js            # Admin-specific functionality
│   └── admin-clean.js      # Clean theme JavaScript
└── customer/
    └── customer.js         # Customer portal functionality

public/js/
├── app.js                  # Compiled main bundle
├── admin.js                # Compiled admin bundle
├── customer.js             # Compiled customer bundle
└── form-validation.js      # Standalone validation library
```

#### jQuery Pattern Implementation
```javascript
// File: resources/js/admin/admin.js
(function($) {
    "use strict";

    // Admin-specific functionality
    $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
    });

    // Enhanced AJAX operations with loading states
    window.performAjaxOperation = function(options) {
        const defaults = {
            showLoader: true,
            loaderMessage: 'Processing...',
            showSuccessNotification: true
        };
        options = $.extend(defaults, options);

        if (options.showLoader) showLoading(options.loaderMessage);

        return $.ajax(options)
            .done(function(response) {
                if (options.showSuccessNotification && response.message) {
                    show_notification('success', response.message);
                }
            })
            .always(function() {
                if (options.showLoader) hideLoading();
            });
    };

})(jQuery);
```

### 3. Icon Usage and Visual Consistency

#### FontAwesome Integration Strategy
```html
<!-- Consistent icon usage patterns -->

<!-- Navigation Icons -->
<i class="fas fa-home me-2"></i> Dashboard
<i class="fas fa-users me-2"></i> Customers
<i class="fas fa-shield-alt me-2"></i> Policies

<!-- Action Icons -->
<i class="fas fa-eye"></i> View
<i class="fas fa-edit"></i> Edit
<i class="fas fa-trash"></i> Delete
<i class="fas fa-plus me-1"></i> Add New
<i class="fas fa-download me-1"></i> Download

<!-- Status Icons -->
<i class="fas fa-check-circle text-success"></i> Active
<i class="fas fa-times-circle text-danger"></i> Inactive
<i class="fas fa-clock text-warning"></i> Pending
<i class="fas fa-exclamation-triangle text-warning"></i> Expiring
```

#### Icon Sizing and Spacing Guidelines
```scss
// Consistent icon sizing
.fa-xs { font-size: 0.75em; }
.fa-sm { font-size: 0.875em; }
.fa-lg { font-size: 1.33em; }
.fa-2x { font-size: 2em; }
.fa-3x { font-size: 3em; }
.fa-4x { font-size: 4em; }

// Icon spacing utilities
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.ms-1 { margin-left: 0.25rem; }
.ms-2 { margin-left: 0.5rem; }

// Icon colors matching status
.text-primary { color: #4e73df; }
.text-success { color: #1cc88a; }
.text-warning { color: #f6c23e; }
.text-danger { color: #e74a3b; }
.text-info { color: #36b9cc; }
```

### 4. Performance Optimization Strategies

#### Asset Compilation and Minification
```javascript
// webpack.mix.js configuration
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/admin/admin.js', 'public/js')
   .js('resources/js/customer/customer.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/admin/admin.scss', 'public/css')
   .sass('resources/sass/customer/customer.scss', 'public/css')
   .options({
       processCssUrls: false,
       postCss: [
           require('autoprefixer'),
           require('cssnano')({
               preset: 'default'
           })
       ]
   })
   .version()
   .sourceMaps();

// Development with HMR
if (mix.inProduction()) {
    mix.minify('public/js/app.js');
} else {
    mix.browserSync('localhost:8000');
}
```

#### Lazy Loading and Code Splitting
```javascript
// Conditional script loading
$(document).ready(function() {
    // Load Chart.js only on dashboard pages
    if ($('#earningsChart').length) {
        $.getScript('https://cdn.jsdelivr.net/npm/chart.js', function() {
            initializeChart();
        });
    }

    // Load date picker only when needed
    if ($('.datepicker').length) {
        $.getScript('https://cdn.jsdelivr.net/npm/flatpickr', function() {
            $('.datepicker').flatpickr({
                dateFormat: 'd/m/Y',
                allowInput: true
            });
        });
    }
});
```

---

## Component Library

### 1. Reusable UI Components

#### Card Components
```html
<!-- Standard Card -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>Card Title
        </h5>
    </div>
    <div class="card-body">Card content</div>
</div>

<!-- Metric Card -->
<div class="card card-metric card-primary h-100" style="cursor: pointer;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="metric-label">Total Count</div>
                <div class="metric-value">1,234</div>
            </div>
            <div>
                <i class="fas fa-chart-line fa-2x text-primary opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<!-- Alert Card -->
<div class="card alert-card border-warning">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Important Notice
        </h5>
    </div>
    <div class="card-body">Alert content</div>
</div>
```

#### Button Components
```html
<!-- Primary Actions -->
<button type="submit" class="btn btn-primary">
    <i class="fas fa-save me-1"></i> Save Changes
</button>

<!-- Secondary Actions -->
<a href="{{ route('back') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-1"></i> Back
</a>

<!-- Action Group -->
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="View">
        <i class="fas fa-eye"></i>
    </button>
    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>

<!-- Loading State -->
<button type="submit" class="btn btn-primary" disabled>
    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
    Processing...
</button>
```

#### Badge Components
```html
<!-- Status Badges -->
<span class="badge bg-success">
    <i class="fas fa-check-circle me-1"></i>Active
</span>

<span class="badge bg-warning">
    <i class="fas fa-clock me-1"></i>Pending
</span>

<span class="badge bg-danger">
    <i class="fas fa-times-circle me-1"></i>Expired
</span>

<!-- Count Badges -->
<h5 class="mb-0">
    Notifications
    <span class="badge bg-danger ms-2">5</span>
</h5>
```

### 2. Form Components

#### Input Fields
```html
<!-- Standard Input -->
<div class="form-group mb-3">
    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
    <input type="text"
           class="form-control @error('name') is-invalid @enderror"
           id="name"
           name="name"
           value="{{ old('name') }}"
           placeholder="Enter your full name"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Select Dropdown -->
<div class="form-group mb-3">
    <label for="status" class="form-label">Status</label>
    <select class="form-select" id="status" name="status">
        <option value="">Select Status</option>
        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<!-- Date Picker -->
<div class="form-group mb-3">
    <label for="birth_date" class="form-label">Birth Date</label>
    <input type="text"
           class="form-control datepicker"
           id="birth_date"
           name="birth_date"
           placeholder="dd/mm/yyyy"
           readonly>
</div>

<!-- File Upload -->
<div class="form-group mb-3">
    <label for="document" class="form-label">Upload Document</label>
    <input type="file"
           class="form-control"
           id="document"
           name="document"
           accept=".pdf,.jpg,.jpeg,.png">
    <small class="form-text text-muted">
        Accepted formats: PDF, JPG, PNG (Max: 2MB)
    </small>
</div>
```

### 3. Loading and State Components

#### Loading Spinner
```html
<!-- Global Loading Overlay -->
<div id="cover-spin" style="display: none;">
    <div class="d-flex justify-content-center align-items-center position-fixed w-100 h-100"
         style="top: 0; left: 0; background: rgba(0,0,0,0.6); z-index: 9999;">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="text-white fw-medium">Processing...</div>
        </div>
    </div>
</div>

<!-- Inline Loading -->
<div class="d-flex justify-content-center align-items-center py-4">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
```

#### Empty States
```html
<!-- No Data State -->
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
    </div>
    <h5 class="text-muted mb-3">No Records Found</h5>
    <p class="text-muted mb-4">There are no records to display at this time.</p>
    <a href="{{ route('create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add First Record
    </a>
</div>

<!-- Error State -->
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
    </div>
    <h5 class="text-muted mb-3">Something went wrong</h5>
    <p class="text-muted mb-4">We couldn't load the requested data.</p>
    <button type="button" class="btn btn-outline-primary" onclick="location.reload()">
        <i class="fas fa-redo me-2"></i>Try Again
    </button>
</div>
```

---

## Implementation Guidelines

### 1. Creating New Pages

#### Step 1: Choose Layout Template
```php
// For Admin pages
@extends('layouts.app')

// For Customer pages
@extends('layouts.customer')
```

#### Step 2: Follow Content Structure
```html
@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-icon me-2"></i>Page Title
        </h1>

        <!-- Action Buttons -->
        <div>
            <a href="{{ route('create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add New
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @include('components.alert')

    <!-- Main Content Card -->
    <div class="card">
        <div class="card-body">
            <!-- Page content -->
        </div>
    </div>

</div>
@endsection
```

#### Step 3: Add Page-Specific Scripts
```html
@push('scripts')
<script>
$(document).ready(function() {
    // Page-specific JavaScript
    $('.select2').select2();

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
```

### 2. Form Validation Integration

#### Client-Side Validation Setup
```javascript
// Initialize form validator
const validator = new FormValidator('#myForm');

// Add validation rules
validator.addRules({
    'name': {
        rules: { required: true, minLength: 2 },
        displayName: 'Full Name'
    },
    'email': {
        rules: { required: true, email: true },
        displayName: 'Email Address'
    },
    'phone': {
        rules: { required: true, phone: true },
        displayName: 'Phone Number'
    }
});

// Enable real-time validation
validator.enableRealTimeValidation();
```

### 3. AJAX Operations Pattern

#### Standardized AJAX Calls
```javascript
function saveData(formData) {
    performAjaxOperation({
        type: "POST",
        url: "{{ route('save.data') }}",
        data: formData,
        dataType: "json",
        loaderMessage: 'Saving data...',
        success: function(response) {
            if (response.status === 'success') {
                // Redirect or update UI
                setTimeout(() => {
                    window.location.href = response.redirect_url;
                }, 1500);
            }
        },
        error: function(xhr) {
            // Error handling is automatic via global setup
        }
    });
}
```

### 4. Component Integration Checklist

#### New Component Implementation
- [ ] Create Blade component file in `resources/views/components/`
- [ ] Add SCSS styles in appropriate theme file
- [ ] Write JavaScript functionality if needed
- [ ] Test responsive behavior across breakpoints
- [ ] Verify accessibility compliance
- [ ] Add to this documentation guide
- [ ] Test with real data scenarios
- [ ] Validate cross-browser compatibility

---

## Best Practices

### 1. Code Organization

#### Blade Templates
- Use consistent indentation (4 spaces)
- Separate logic from presentation
- Utilize Blade components for reusable elements
- Comment complex template logic
- Follow naming conventions: `kebab-case` for files

#### SCSS Architecture
- Use BEM methodology for custom classes
- Leverage Bootstrap utilities before writing custom CSS
- Organize styles by component hierarchy
- Use variables for consistent theming
- Comment complex selectors and calculations

#### JavaScript Patterns
- Wrap functionality in immediately invoked function expressions (IIFE)
- Use meaningful variable and function names
- Implement error handling for all AJAX calls
- Utilize jQuery's delegation for dynamic content
- Comment complex logic and algorithms

### 2. Performance Considerations

#### Image Optimization
```html
<!-- Use appropriate image formats and sizes -->
<img src="{{ asset('images/logo.webp') }}"
     alt="Company Logo"
     style="max-height: 40px;"
     loading="lazy">

<!-- Provide multiple formats for better compatibility -->
<picture>
    <source srcset="{{ asset('images/hero.webp') }}" type="image/webp">
    <source srcset="{{ asset('images/hero.jpg') }}" type="image/jpeg">
    <img src="{{ asset('images/hero.jpg') }}" alt="Hero Image">
</picture>
```

#### CSS and JavaScript Optimization
```php
<!-- Combine and minify assets -->
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<script src="{{ mix('js/app.js') }}"></script>

<!-- Load non-critical CSS asynchronously -->
<link rel="preload" href="{{ mix('css/charts.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- Defer non-critical JavaScript -->
<script src="{{ asset('js/analytics.js') }}" defer></script>
```

### 3. Accessibility Guidelines

#### Keyboard Navigation
- Ensure all interactive elements are keyboard accessible
- Implement logical tab order
- Provide visible focus indicators
- Support Escape key for modal dismissal

#### Screen Reader Support
- Use semantic HTML elements
- Provide ARIA labels for complex interactions
- Include skip navigation links
- Test with screen reader software

#### Color and Contrast
- Maintain WCAG AA compliance (4.5:1 contrast ratio)
- Don't rely solely on color to convey information
- Support high contrast mode
- Test with color blindness simulators

### 4. Browser Compatibility

#### Supported Browsers
- **Modern Browsers**: Chrome 88+, Firefox 85+, Safari 14+, Edge 88+
- **Mobile**: iOS Safari 14+, Chrome Mobile 88+
- **Fallbacks**: Provide graceful degradation for older browsers

#### Progressive Enhancement
```javascript
// Feature detection before implementation
if ('IntersectionObserver' in window) {
    // Use Intersection Observer for lazy loading
    const observer = new IntersectionObserver(callback);
} else {
    // Fallback: load all images immediately
    loadAllImages();
}

// CSS Grid with Flexbox fallback
.grid-container {
    display: flex;
    flex-wrap: wrap;

    @supports (display: grid) {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}
```

### 5. Security Considerations

#### CSRF Protection
```html
<!-- Always include CSRF token in forms -->
<form method="POST" action="{{ route('save') }}">
    @csrf
    <!-- form fields -->
</form>
```

#### XSS Prevention
```php
<!-- Escape output by default -->
<h1>{{ $user->name }}</h1>

<!-- Use {!! !!} only for trusted HTML -->
<div class="content">{!! $trustedHtml !!}</div>

<!-- Validate and sanitize user input -->
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
]);
```

#### Content Security Policy
```php
// Add CSP headers in middleware
$response->headers->set('Content-Security-Policy',
    "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' fonts.googleapis.com;"
);
```

---

## Conclusion

This UI Component Library Guide provides a comprehensive framework for maintaining design consistency and implementing new features in the Laravel insurance management system. By following these patterns and guidelines, developers can ensure a cohesive user experience across both admin and customer interfaces while maintaining code quality and accessibility standards.

### Key Takeaways

1. **Consistency**: Use established patterns for layouts, components, and interactions
2. **Accessibility**: Follow WCAG guidelines and implement proper ARIA labeling
3. **Performance**: Optimize assets and implement lazy loading where appropriate
4. **Maintainability**: Organize code systematically and document complex implementations
5. **Responsiveness**: Ensure all components work seamlessly across devices and screen sizes

For questions or clarifications on implementing these patterns, refer to the existing codebase examples or consult the development team.

---

*Last Updated: {{ date('Y-m-d') }}*
*Version: 1.0*