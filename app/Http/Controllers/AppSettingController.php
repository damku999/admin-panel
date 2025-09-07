<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Services\AppSettingService;
use App\Traits\ExportableTrait;
use Illuminate\Support\Facades\Validator;

class AppSettingController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        
        $query = AppSetting::query();
        
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        
        $settings = $query->orderBy('category')->orderBy('key')->paginate(20);
        $categories = AppSetting::distinct('category')->pluck('category');
        
        return view('app_settings.index', compact('settings', 'categories', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AppSetting::distinct('category')->pluck('category')->prepend('general');
        return view('app_settings.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:app_settings,key|max:255',
            'value' => 'nullable',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_encrypted' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['key', 'value', 'type', 'category', 'description', 'is_encrypted', 'is_active']);
        $data['is_encrypted'] = $request->has('is_encrypted');
        $data['is_active'] = $request->has('is_active');

        AppSetting::create($data);
        
        // Clear cache
        AppSettingService::clearCache();

        return redirect()->route('app-settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppSetting $appSetting)
    {
        return view('app_settings.show', compact('appSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppSetting $appSetting)
    {
        $categories = AppSetting::distinct('category')->pluck('category')->prepend('general');
        return view('app_settings.edit', compact('appSetting', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppSetting $appSetting)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:app_settings,key,' . $appSetting->id,
            'value' => 'nullable',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_encrypted' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['key', 'value', 'type', 'category', 'description', 'is_encrypted', 'is_active']);
        $data['is_encrypted'] = $request->has('is_encrypted');
        $data['is_active'] = $request->has('is_active');

        $appSetting->update($data);
        
        // Clear cache
        AppSettingService::clearCache();

        return redirect()->route('app-settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppSetting $appSetting)
    {
        $appSetting->delete();
        
        // Clear cache
        AppSettingService::clearCache();

        return redirect()->route('app-settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    /**
     * Toggle setting status
     */
    public function toggle(AppSetting $app_setting)
    {
        $app_setting->is_active = !$app_setting->is_active;
        $app_setting->save();
        
        // Clear cache
        AppSettingService::clearCache();

        return response()->json([
            'success' => true,
            'is_active' => $app_setting->is_active,
            'message' => 'Setting status updated successfully.'
        ]);
    }

    /**
     * Clear all settings cache
     */
    public function clearCache()
    {
        AppSettingService::clearCache();
        
        return redirect()->route('app-settings.index')
            ->with('success', 'Settings cache cleared successfully.');
    }

    /**
     * View encrypted value (admin only)
     */
    public function viewEncrypted(AppSetting $app_setting)
    {
        // Only allow for encrypted settings
        if (!$app_setting->is_encrypted) {
            return response()->json([
                'success' => false,
                'message' => 'This setting is not encrypted.'
            ], 400);
        }

        // Get the decrypted value using the model's accessor
        $decryptedValue = $app_setting->value;
        
        return response()->json([
            'success' => true,
            'value' => $decryptedValue,
            'message' => 'Encrypted value retrieved successfully.'
        ]);
    }

    // Export method is now provided by ExportableTrait with advanced features
    
    protected function getSearchableFields(): array
    {
        return ['key', 'category'];
    }
    
    protected function getExportConfig(Request $request): array
    {
        return array_merge($this->getBaseExportConfig($request), [
            'headings' => [
                'ID', 'Setting Key', 'Category', 'Value', 'Is Encrypted', 'Description', 'Created Date'
            ],
            'mapping' => function($setting) {
                return [
                    $setting->id,
                    $setting->key,
                    $setting->category,
                    $setting->is_encrypted ? '[ENCRYPTED]' : $setting->value,
                    $setting->is_encrypted ? 'Yes' : 'No',
                    $setting->description,
                    $setting->created_at->format('Y-m-d H:i:s')
                ];
            },
            'with_mapping' => true
        ]);
    }
    
    protected function getExportFilename(): string
    {
        return 'app_settings';
    }
}
