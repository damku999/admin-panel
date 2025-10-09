# 📍 COMPLETE SIDEBAR NAVIGATION - Notification System

This document shows all sidebar links needed for the complete notification system.

---

## 🎯 REQUIRED SIDEBAR LINKS

### Notification Management Section

Add this section to `resources/views/common/sidebar.blade.php`:

```blade
<!-- ==================== NOTIFICATION MANAGEMENT ==================== -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseNotifications"
       aria-expanded="false" aria-controls="collapseNotifications">
        <i class="fas fa-bell"></i>
        <span>Notifications</span>
    </a>
    <div id="collapseNotifications" class="collapse" aria-labelledby="headingNotifications" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">

            <!-- Notification Templates -->
            <a class="collapse-item {{ request()->is('admin/notification-templates*') ? 'active' : '' }}"
               href="{{ route('admin.notification-templates.index') }}">
                <i class="fas fa-file-alt"></i> Templates
            </a>

            <!-- Notification Logs -->
            <a class="collapse-item {{ request()->is('admin/notification-logs') && !request()->is('admin/notification-logs/analytics') ? 'active' : '' }}"
               href="{{ route('admin.notification-logs.index') }}">
                <i class="fas fa-list"></i> Notification Logs
            </a>

            <!-- Analytics Dashboard -->
            <a class="collapse-item {{ request()->is('admin/notification-logs/analytics') ? 'active' : '' }}"
               href="{{ route('admin.notification-logs.analytics') }}">
                <i class="fas fa-chart-line"></i> Analytics
            </a>

            <!-- Customer Devices (Push) -->
            <a class="collapse-item {{ request()->is('admin/customer-devices*') ? 'active' : '' }}"
               href="{{ route('admin.customer-devices.index') }}">
                <i class="fas fa-mobile-alt"></i> Customer Devices
            </a>

            <!-- Failed Notifications (Quick Access) -->
            <a class="collapse-item {{ request()->is('admin/notification-logs') && request('status') == 'failed' ? 'active' : '' }}"
               href="{{ route('admin.notification-logs.index', ['status' => 'failed']) }}">
                <i class="fas fa-exclamation-triangle text-danger"></i> Failed Notifications
            </a>

        </div>
    </div>
</li>
<!-- ==================== END NOTIFICATION MANAGEMENT ==================== -->
```

---

## 📋 COMPLETE NAVIGATION STRUCTURE

### Full Notification Section Breakdown:

```
📧 Notifications (Collapsible)
   ├─ 📄 Templates ......................... /admin/notification-templates
   ├─ 📋 Notification Logs ................. /admin/notification-logs
   ├─ 📊 Analytics ......................... /admin/notification-logs/analytics
   ├─ 📱 Customer Devices .................. /admin/customer-devices
   └─ ⚠️  Failed Notifications ............. /admin/notification-logs?status=failed
```

---

## 🔧 ADDITIONAL ROUTES NEEDED

You need to add the Customer Devices routes:

### Add to `routes/web.php`:

```php
// Customer Devices Management (for Push Notifications)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/customer-devices', [CustomerDeviceController::class, 'index'])
        ->name('admin.customer-devices.index');
    Route::get('/customer-devices/{device}', [CustomerDeviceController::class, 'show'])
        ->name('admin.customer-devices.show');
    Route::post('/customer-devices/{device}/deactivate', [CustomerDeviceController::class, 'deactivate'])
        ->name('admin.customer-devices.deactivate');
    Route::post('/customer-devices/cleanup-invalid', [CustomerDeviceController::class, 'cleanupInvalid'])
        ->name('admin.customer-devices.cleanup-invalid');
});
```

---

## 📄 MISSING CONTROLLER & VIEWS

### Create: `app/Http/Controllers/CustomerDeviceController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\CustomerDevice;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerDeviceController extends Controller
{
    /**
     * Display listing of all customer devices
     */
    public function index(Request $request)
    {
        $query = CustomerDevice::with('customer')
            ->orderBy('last_active_at', 'desc');

        // Filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('device_token', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->paginate(50);

        // Statistics
        $stats = [
            'total' => CustomerDevice::count(),
            'active' => CustomerDevice::where('is_active', true)->count(),
            'inactive' => CustomerDevice::where('is_active', false)->count(),
            'android' => CustomerDevice::where('device_type', 'android')->where('is_active', true)->count(),
            'ios' => CustomerDevice::where('device_type', 'ios')->where('is_active', true)->count(),
            'web' => CustomerDevice::where('device_type', 'web')->where('is_active', true)->count(),
        ];

        return view('admin.customer_devices.index', compact('devices', 'stats'));
    }

    /**
     * Show device details
     */
    public function show(CustomerDevice $device)
    {
        $device->load('customer');

        // Get notification logs for this device
        $notifications = \DB::table('notification_logs')
            ->where('channel', 'push')
            ->where('recipient', $device->device_token)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.customer_devices.show', compact('device', 'notifications'));
    }

    /**
     * Deactivate a device
     */
    public function deactivate(CustomerDevice $device)
    {
        $device->update(['is_active' => false]);

        return back()->with('success', 'Device deactivated successfully');
    }

    /**
     * Cleanup invalid device tokens
     */
    public function cleanupInvalid(Request $request)
    {
        // Deactivate devices that haven't been active in 90 days
        $count = CustomerDevice::where('is_active', true)
            ->where('last_active_at', '<', now()->subDays(90))
            ->update(['is_active' => false]);

        return back()->with('success', "Deactivated {$count} inactive devices");
    }
}
```

### Create: `resources/views/admin/customer_devices/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-mobile-alt"></i> Customer Devices
        </h1>
        <button class="btn btn-sm btn-danger" onclick="cleanupInvalid()">
            <i class="fas fa-broom"></i> Cleanup Inactive
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Devices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Android</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['android'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-android fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">iOS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ios'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-apple fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Web</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['web'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customer-devices.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search customer/device..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="device_type" class="form-control">
                            <option value="">All Device Types</option>
                            <option value="android" {{ request('device_type') == 'android' ? 'selected' : '' }}>Android</option>
                            <option value="ios" {{ request('device_type') == 'ios' ? 'selected' : '' }}>iOS</option>
                            <option value="web" {{ request('device_type') == 'web' ? 'selected' : '' }}>Web</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.customer-devices.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registered Devices ({{ $devices->total() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Device Name</th>
                            <th>Type</th>
                            <th>OS Version</th>
                            <th>Last Active</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>
                                <strong>{{ $device->customer->name }}</strong><br>
                                <small class="text-muted">{{ $device->customer->mobile_number }}</small>
                            </td>
                            <td>
                                {{ $device->device_name ?? 'Unknown Device' }}<br>
                                <small class="text-muted">{{ Str::limit($device->device_token, 30) }}</small>
                            </td>
                            <td>
                                @if($device->device_type == 'android')
                                    <i class="fab fa-android text-success"></i> Android
                                @elseif($device->device_type == 'ios')
                                    <i class="fab fa-apple text-dark"></i> iOS
                                @else
                                    <i class="fas fa-globe text-info"></i> Web
                                @endif
                            </td>
                            <td>{{ $device->os_version ?? 'N/A' }}</td>
                            <td>
                                {{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}
                            </td>
                            <td>
                                @if($device->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.customer-devices.show', $device) }}"
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($device->is_active)
                                <form action="{{ route('admin.customer-devices.deactivate', $device) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning"
                                            onclick="return confirm('Deactivate this device?')"
                                            title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No devices found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $devices->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function cleanupInvalid() {
    if (confirm('This will deactivate all devices inactive for 90+ days. Continue?')) {
        fetch('{{ route("admin.customer-devices.cleanup-invalid") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Cleanup completed');
            location.reload();
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
</script>
@endsection
```

---

## ✅ COMPLETE CHECKLIST

To fully integrate the sidebar navigation:

- [ ] Update `resources/views/common/sidebar.blade.php` with notification section
- [ ] Create `CustomerDeviceController.php`
- [ ] Create view `resources/views/admin/customer_devices/index.blade.php`
- [ ] Create view `resources/views/admin/customer_devices/show.blade.php` (optional detail view)
- [ ] Add routes to `routes/web.php`
- [ ] Add permissions to role/permission seeder
- [ ] Test all navigation links work
- [ ] Verify icons display correctly
- [ ] Check active states highlight correctly

---

## 🎨 SIDEBAR ICONS REFERENCE

Icons used in navigation:

| Section | Icon | Class |
|---------|------|-------|
| Main Menu | Bell | `fas fa-bell` |
| Templates | File | `fas fa-file-alt` |
| Logs | List | `fas fa-list` |
| Analytics | Chart | `fas fa-chart-line` |
| Devices | Mobile | `fas fa-mobile-alt` |
| Failed | Warning | `fas fa-exclamation-triangle` |

---

## 📍 NAVIGATION FLOW

```
Sidebar
  └─ 📧 Notifications (Dropdown)
       ├─ 📄 Templates
       │    └─ List all notification templates
       │    └─ Create/Edit templates
       │    └─ View version history
       │    └─ Test send
       │
       ├─ 📋 Notification Logs
       │    └─ All sent notifications
       │    └─ Filter by channel/status/date
       │    └─ View details
       │    └─ Resend failed
       │
       ├─ 📊 Analytics
       │    └─ Success rates
       │    └─ Channel distribution
       │    └─ Volume charts
       │    └─ Top templates
       │
       ├─ 📱 Customer Devices
       │    └─ All registered devices
       │    └─ Filter by type/status
       │    └─ Deactivate devices
       │    └─ Cleanup inactive
       │
       └─ ⚠️  Failed Notifications
            └─ Quick access to failed
            └─ Bulk retry
            └─ View error details
```

---

## 🚀 DEPLOYMENT

After adding sidebar navigation:

```bash
# Clear views cache
php artisan view:clear

# Clear route cache
php artisan route:clear

# Test navigation
# Visit: /admin/notification-templates
# Visit: /admin/notification-logs
# Visit: /admin/notification-logs/analytics
# Visit: /admin/customer-devices
```

---

## ✅ VERIFICATION

After implementation, verify:

1. ✅ All menu items visible in sidebar
2. ✅ Dropdown expands/collapses correctly
3. ✅ Active states highlight current page
4. ✅ Icons display properly
5. ✅ All links navigate correctly
6. ✅ Permissions work (if role-based)
7. ✅ Mobile responsive
8. ✅ No console errors

---

**Status:** Complete sidebar navigation structure for notification system
**Missing:** Customer Devices controller, views, and routes (provided above)
