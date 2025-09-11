# List Page Components Documentation

This document describes the reusable components created for list pages in the admin panel.

## Components Overview

### 1. Add Button Component (`<x-add-button>`)
Displays a conditional "Add New" button with permission checking.

**Props:**
- `route` (required): The route name for the add page
- `permission` (optional): Permission name to check
- `text` (default: "Add New"): Button text  
- `icon` (default: "fas fa-plus"): Button icon
- `class` (default: "btn btn-primary"): Button CSS classes

**Example:**
```php
<x-add-button 
    route="users.create" 
    permission="user-create" 
    text="Add User" 
/>
```

### 2. Export Button Component (`<x-export-button>`)
Displays a conditional "Export" button with permission checking.

**Props:**
- `route` (required): The route name for the export endpoint
- `permission` (optional): Permission name to check
- `text` (default: "Export"): Button text
- `icon` (default: "fas fa-file-excel"): Button icon
- `class` (default: "btn btn-success"): Button CSS classes

**Example:**
```php
<x-export-button 
    route="users.export" 
    permission="user-export" 
/>
```

### 3. List Header Component (`<x-list-header>`)
Combines title, subtitle, add button, export button, and extra buttons.

**Props:**
- `title` (required): Page title
- `subtitle` (optional): Page subtitle
- `addRoute` (optional): Route for add button
- `addPermission` (optional): Permission for add button
- `addText` (default: "Add New"): Add button text
- `exportRoute` (optional): Route for export button  
- `exportPermission` (optional): Permission for export button
- `exportText` (default: "Export"): Export button text
- `extraButtons` (optional): HTML string for additional buttons

**Example:**
```php
<x-list-header 
    title="Users Management"
    subtitle="Manage all system users"
    addRoute="users.create"
    addPermission="user-create"
    exportRoute="users.export"
    :extraButtons="'<a href=\"#\" class=\"btn btn-info\">Import</a>'"
/>
```

### 4. Complete List Page Component (`<x-list-page>`)
Full page layout with header, search, and content slot.

**Props:**
- `title` (required): Page title
- `subtitle` (optional): Page subtitle
- `addRoute` (optional): Route for add button
- `addPermission` (optional): Permission for add button
- `exportRoute` (optional): Route for export button
- `exportPermission` (optional): Permission for export button
- `searchRoute` (optional): Route for search form
- `searchValue` (optional): Current search value
- `extraFilters` (optional): HTML for additional filters
- `extraButtons` (optional): HTML for extra buttons

**Example:**
```php
<x-list-page
    title="Users Management"
    subtitle="Manage all system users"
    addRoute="users.create"
    addPermission="user-create"
    exportRoute="users.export"
    searchRoute="users.index"
    :searchValue="request('search')"
>
    <!-- Table content here -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <!-- Table rows -->
        </table>
    </div>
</x-list-page>
```

## Common Export Functionality

### ExportableTrait
Controllers can use the `ExportableTrait` for standardized export functionality.

**Usage:**
```php
<?php

namespace App\Http\Controllers;

use App\Traits\ExportableTrait;

class UserController extends Controller
{
    use ExportableTrait;

    // The trait provides an export() method automatically
    // It will look for App\Exports\UsersExport class
}
```

**Features:**
- Auto-detects export class based on controller name
- Handles filename generation
- Provides consistent export behavior
- Supports custom export classes and filenames

## Migration Guide

### Before (Old Way):
```php
<div class="card-header py-3">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h4 mb-0 text-primary font-weight-bold">Users Management</h1>
            <small class="text-muted">Manage all system users</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if (auth()->user()->hasPermissionTo('user-create'))
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            @endif
            <a href="{{ route('users.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export To Excel
            </a>
        </div>
    </div>
</div>
```

### After (New Way):
```php
<x-list-header 
    title="Users Management"
    subtitle="Manage all system users"
    addRoute="users.create"
    addPermission="user-create"
    exportRoute="users.export"
/>
```

## Benefits

1. **Consistency**: All list pages have the same look and feel
2. **Maintainability**: Changes to button styles apply everywhere
3. **Permission Handling**: Built-in permission checking
4. **Responsive**: Mobile-friendly button layouts
5. **Reusability**: Easy to use across all list pages
6. **Flexibility**: Supports custom buttons and filters

## Next Steps

1. Update existing list pages to use these components
2. Ensure all controllers implement the export functionality
3. Add any missing Export classes for modules
4. Consider creating similar components for form pages