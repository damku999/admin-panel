<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-device-list')->only(['index']);
        $this->middleware('permission:customer-device-view')->only(['show']);
        $this->middleware('permission:customer-device-deactivate')->only(['deactivate']);
        $this->middleware('permission:customer-device-cleanup')->only(['cleanupInvalid']);
    }

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
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                    ->orWhere('device_token', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
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
        $notifications = DB::table('notification_logs')
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

        return response()->json([
            'success' => true,
            'message' => "Deactivated {$count} inactive devices",
        ]);
    }
}
