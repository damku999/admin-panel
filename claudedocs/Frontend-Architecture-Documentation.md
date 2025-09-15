# Frontend Architecture Documentation

## Overview

This Laravel insurance management system uses a dual-portal architecture with Bootstrap 5 and carefully organized asset compilation. The frontend architecture separates admin and customer interfaces while maintaining consistency and modern practices.

## 1. Vue.js 2 Integration with Laravel Mix

### Current State
The system is **NOT** actively using Vue.js components, despite having Vue dependencies. The Vue.js 2 dependency was removed from the main compilation process to eliminate console errors.

### Configuration
```javascript
// webpack.mix.js - Vue.js has been intentionally removed
require('./bootstrap'); // Only includes jQuery, Bootstrap, Axios
```

### Recommendation for Vue.js Integration
If Vue.js components are needed:
1. Add Vue.js back to compilation
2. Create component structure in `resources/js/components/`
3. Register components in appropriate entry points
4. Follow Vue.js 2 patterns for compatibility

## 2. Layout Structures

### Admin Layout (`layouts/app.blade.php`)
**Primary Structure:**
```html
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    @include('common.sidebar')
    
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Topbar -->
        @include('common.header')
        
        <!-- Main Content -->
        @yield('content')
        
        <!-- Footer -->
        @include('common.footer')
    </div>
</div>
```

**Key Features:**
- Responsive sidebar with collapsible sub-menus
- Sticky header with user profile dropdown
- Centralized modal management
- Global AJAX utilities
- Form validation integration

### Customer Layout (`layouts/customer.blade.php`)
**Simplified Structure:**
```html
<!-- Customer Layout - No Sidebar -->
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Customer Header -->
        @include('customer.partials.header')
        
        <!-- Content -->
        <div class="container-fluid">
            @yield('content')
        </div>
        
        <!-- Footer -->
        @include('customer.partials.footer')
    </div>
</div>
```

**Key Features:**
- Clean, minimal navigation
- Navbar-based navigation instead of sidebar
- Family head status indicators
- Simplified user experience

## 3. Blade Component Patterns

### Reusable Components Library

#### List Header Component
```blade
<x-list-header 
    title="Customers Management"
    subtitle="Manage all customer records"
    addRoute="customers.create"
    addPermission="customer-create"
    exportRoute="customers.export"
/>
```

**Component Features:**
- Responsive title/subtitle display
- Permission-based button visibility
- Consistent styling across modules

#### Pagination Component
```blade
<x-pagination-with-info :paginator="$customers" :request="$request" />
```

**Features:**
- Record count display
- Filter preservation across pages
- Bootstrap 5 pagination styling

#### Action Buttons
```blade
<x-add-button route="customers.create" permission="customer-create" />
<x-export-button route="customers.export" />
```

**Component Benefits:**
- Consistent icon and text patterns
- Built-in permission checking
- Mobile-responsive (icons only on small screens)

### Custom Components Available

| Component | File | Purpose |
|-----------|------|---------|
| `x-list-header` | `list-header.blade.php` | Page headers with actions |
| `x-pagination-with-info` | `pagination-with-info.blade.php` | Pagination with record counts |
| `x-add-button` | `add-button.blade.php` | Standardized add buttons |
| `x-export-button` | `export-button.blade.php` | Standardized export buttons |
| `x-alert` | `alert.blade.php` | System alerts and messages |
| `x-modal` | `modal.blade.php` | Reusable modal dialogs |

## 4. Bootstrap 5 Usage and Custom SCSS

### Architecture Strategy
The system uses **pure Bootstrap 5** with minimal custom overrides for maximum maintainability.

### SCSS Structure
```scss
// Admin Portal (resources/sass/admin/admin-clean.scss)
@use '~bootstrap/scss/bootstrap';
@use '~@fortawesome/fontawesome-free/scss/fontawesome';

// Custom variables
$primary: #4f46e5;
$sidebar-width: 280px;

// Minimal custom styles
.sidebar {
  width: $sidebar-width;
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}
```

### Customer Portal Styling
```scss
// Customer Portal (resources/sass/customer/customer.scss)  
$webmonks-primary: #2563eb;
$card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);

// Modern design system colors
```

### Custom Variables
```scss
// resources/sass/_variables.scss
$body-bg: #f8fafc;
$font-family-sans-serif: 'Nunito', sans-serif;
$font-size-base: 0.9rem;
```

## 5. Form Patterns and Validation

### Standard Form Structure
```blade
<form method="POST" action="{{ route('customers.store') }}">
    @csrf
    <div class="card-body py-3">
        <!-- Section 1: Basic Information -->
        <div class="mb-4">
            <h6 class="text-muted fw-bold mb-3">
                <i class="fas fa-user me-2"></i>Basic Information
            </h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <span class="text-danger">*</span> Name
                    </label>
                    <input type="text" 
                           class="form-control form-control-sm @error('name') is-invalid @enderror"
                           name="name" 
                           value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer py-2 bg-light">
        <div class="d-flex justify-content-end gap-2">
            <a class="btn btn-secondary btn-sm px-4" href="{{ route('customers.index') }}">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-success btn-sm px-4">
                <i class="fas fa-save me-1"></i>Save Customer
            </button>
        </div>
    </div>
</form>
```

### Validation Patterns
1. **Server-side validation** - Laravel Form Requests
2. **Client-side validation** - Custom FormValidator class
3. **Real-time feedback** - Bootstrap validation classes
4. **Error display** - Consistent invalid-feedback patterns

### Form Validation JavaScript
```javascript
// Custom FormValidator class in public/js/form-validation.js
const validator = new FormValidator('form');

validator.addRules({
    name: { 
        rules: { required: true, minLength: 2 },
        displayName: 'Name'
    },
    email: { 
        rules: { required: true, email: true },
        displayName: 'Email'
    }
});

validator.enableRealTimeValidation();
```

## 6. Table/Listing Patterns

### Standard Data Table Structure
```blade
<div class="table-responsive">
    <table class="table table-bordered" width="100%">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('customers.index', ['sort_field' => 'name', 'sort_order' => $sortOrder == 'asc' ? 'desc' : 'asc']) }}">
                        Name
                        @if ($sortField == 'name')
                            <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>
                        <!-- Action buttons with permission checks -->
                        <div class="d-flex flex-wrap" style="gap: 6px;">
                            @if (auth()->user()->hasPermissionTo('customer-edit'))
                                <a href="{{ route('customers.edit', $customer->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-pen"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No Record Found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <x-pagination-with-info :paginator="$customers" :request="$request" />
</div>
```

### Search and Filter Patterns
```blade
<form method="GET" action="{{ route('customers.index') }}" id="search_form">
    <div class="row">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select class="form-control" name="status">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>
</form>
```

## 7. Modal and Popup Patterns

### Global Modal Management
The system includes centralized modal utilities for consistent behavior:

```javascript
// Global modal functions (in layouts/app.blade.php)
window.showModal = function(modalId) {
    $('#' + modalId).css('display', 'block').addClass('show');
    $('body').addClass('modal-open');
    $('body').append('<div class="modal-backdrop fade show"></div>');
};

window.hideModal = function(modalId) {
    $('#' + modalId).css('display', 'none').removeClass('show');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
};
```

### Standard Modal Structure
```blade
<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark">
                    <i class="fas fa-sign-out-alt text-primary me-2"></i>
                    Ready to Leave?
                </h5>
                <button type="button" class="btn-close" onclick="hideLogoutModal()"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-door-open fa-3x text-primary mb-3"></i>
                </div>
                <h6 class="mb-2">Are you ready to end your current session?</h6>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary" onclick="hideLogoutModal()">
                    Cancel
                </button>
                <a class="btn btn-primary" href="#" onclick="document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>
```

## 8. Navigation and Menu Structures

### Admin Sidebar Navigation
```blade
<!-- Hierarchical Navigation with Sub-menus -->
<div class="nav-item">
    <a class="nav-link {{ request()->routeIs('customers.*') ? 'bg-light bg-opacity-10 text-white fw-semibold' : '' }}" 
       href="{{ route('customers.index') }}">
        <i class="fas fa-users me-3"></i>
        <span>Customers</span>
    </a>
</div>

<!-- Collapsible Sub-menu -->
<div class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#masterSubmenu">
        <div class="d-flex align-items-center">
            <i class="fas fa-database me-3"></i>
            <span>Master Data</span>
        </div>
        <i class="fas fa-chevron-down"></i>
    </a>
    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="masterSubmenu">
        <div class="ms-4">
            <a class="nav-link" href="{{ route('brokers.index') }}">
                <i class="fas fa-handshake me-3 fs-6"></i>
                <span>Brokers</span>
            </a>
        </div>
    </div>
</div>
```

### Customer Portal Navigation
```blade
<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4 sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
            <img src="{{ asset('images/parth_logo.png') }}" style="max-height: 40px;">
        </a>
        
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" 
                       href="{{ route('customer.dashboard') }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

## 9. Asset Compilation and Organization

### Webpack Mix Configuration
```javascript
// webpack.mix.js - Dual-portal asset compilation

// Admin Portal Assets (Clean Bootstrap 5 only)
mix.js('resources/js/admin/admin-clean.js', 'public/js/admin.js')
   .sass('resources/sass/admin/admin-clean.scss', 'public/css/admin.css');

// Customer Portal Assets  
mix.js('resources/js/customer/customer.js', 'public/js')
   .sass('resources/sass/customer/customer.scss', 'public/css');

// Shared Assets (Legacy compatibility)
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
```

### Asset Structure
```
resources/
├── js/
│   ├── admin/
│   │   └── admin-clean.js     # Admin portal JS
│   ├── customer/
│   │   └── customer.js        # Customer portal JS
│   ├── bootstrap.js           # Shared dependencies
│   └── app.js                 # Legacy entry point
├── sass/
│   ├── admin/
│   │   └── admin-clean.scss   # Admin styles
│   ├── customer/
│   │   └── customer.scss      # Customer styles
│   ├── _variables.scss        # Global variables
│   └── app.scss              # Base styles
```

### Production Optimization
```javascript
// Production builds include:
if (mix.inProduction()) {
    mix.version()
       .options({
           terser: {
               terserOptions: {
                   compress: { drop_console: true }
               }
           }
       });
}
```

## 10. JavaScript Patterns and Vue Component Usage

### Current JavaScript Architecture
The system uses **jQuery-based patterns** rather than Vue.js components:

```javascript
// Admin Portal JavaScript Pattern
$(document).ready(function() {
    // Initialize Bootstrap components
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation enhancement
    $('form').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        if (!$form[0].checkValidity()) {
            return; // Let HTML5 validation handle it
        }
        
        $submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
});
```

### Global AJAX Utilities
```javascript
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
```

### Customer Portal JavaScript Patterns
```javascript
// Customer portal specific functionality
(function($) {
    "use strict";
    
    // Enhanced form interactions
    $('.form-floating input').on('focus blur', function() {
        $(this).closest('.form-floating').toggleClass('focused');
    });
    
    // Loading states for downloads
    $('a[href*="download"]').on('click', function() {
        var $btn = $(this);
        $btn.addClass('disabled')
            .html('<span class="spinner-border spinner-border-sm"></span>Downloading...');
        
        setTimeout(function() {
            $btn.removeClass('disabled').html(originalHtml);
        }, 3000);
    });
})(jQuery);
```

## Maintenance Guidelines

### Adding New Modules
When creating new modules, follow these patterns:

1. **Use existing components:**
   ```blade
   <x-list-header title="New Module" addRoute="new.create" />
   ```

2. **Follow form sectioning:**
   ```blade
   <div class="mb-4">
       <h6 class="text-muted fw-bold mb-3">
           <i class="fas fa-icon me-2"></i>Section Title
       </h6>
       <!-- Form fields here -->
   </div>
   ```

3. **Implement standard table patterns:**
   - Sortable headers
   - Permission-based actions
   - Consistent button styling
   - Pagination component usage

4. **Use global JavaScript utilities:**
   - `performAjaxOperation()` for AJAX calls
   - `showModal()`/`hideModal()` for modals
   - `show_notification()` for user feedback

### Consistency Checklist
- [ ] Use Bootstrap 5 classes consistently
- [ ] Include proper permission checks
- [ ] Follow established color scheme
- [ ] Implement responsive design patterns
- [ ] Add proper error handling
- [ ] Include loading states
- [ ] Use established component patterns

### Performance Best Practices
1. Leverage compiled assets (admin.js, customer.js)
2. Use Bootstrap's built-in responsive utilities
3. Minimize custom CSS overrides
4. Implement lazy loading for large datasets
5. Use CDN for third-party libraries where appropriate

This architecture provides a solid foundation for maintaining consistency while allowing for extensibility as the application grows.