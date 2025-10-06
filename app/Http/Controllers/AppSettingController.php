<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\AppSettingService;
use Illuminate\Http\Request;

/**
 * App Setting Controller
 *
 * Handles AppSetting CRUD operations for managing application configuration.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class AppSettingController extends AbstractBaseCrudController
{
    protected AppSettingService $appSettingService;

    protected array $categories = [
        'whatsapp' => 'WhatsApp',
        'mail' => 'Mail',
        'application' => 'Application',
        'notifications' => 'Notifications',
        'general' => 'General'
    ];

    protected array $types = [
        'string' => 'String',
        'json' => 'JSON',
        'boolean' => 'Boolean',
        'numeric' => 'Numeric'
    ];

    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
        $this->setupPermissionMiddleware('app-setting');
    }

    /**
     * Display a listing of the app settings
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = AppSetting::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('category', 'LIKE', "%{$search}%");
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('is_active', $request->status);
            }

            $settings = $query->orderBy('category')->orderBy('key')->paginate(config('app.pagination_default', 15));

            return view('app_settings.index', [
                'settings' => $settings,
                'categories' => $this->categories
            ]);
        } catch (\Throwable $th) {
            return $this->redirectWithError('Failed to load settings: ' . $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new app setting
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('app_settings.create', [
            'categories' => $this->categories,
            'types' => $this->types
        ]);
    }

    /**
     * Store a newly created app setting in storage
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key',
            'value' => 'required',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|in:whatsapp,mail,application,notifications,general',
            'description' => 'nullable|string|max:1000',
            'is_encrypted' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            $data = [
                'key' => $request->key,
                'value' => $request->value,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'is_encrypted' => $request->has('is_encrypted') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            // Create using service
            $this->appSettingService->set(
                $request->key,
                $request->value,
                [
                    'type' => $request->type,
                    'category' => $request->category,
                    'description' => $request->description,
                    'is_encrypted' => $request->has('is_encrypted'),
                    'is_active' => $request->has('is_active') ? 1 : 0
                ]
            );

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting', 'created')
            );
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'create') . ': ' . $th->getMessage()
            )->withInput();
        }
    }

    /**
     * Display the specified app setting
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $setting = AppSetting::findOrFail($id);

            return view('app_settings.show', [
                'setting' => $setting,
                'categories' => $this->categories,
                'types' => $this->types
            ]);
        } catch (\Throwable $th) {
            return $this->redirectWithError('Setting not found: ' . $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified app setting
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $setting = AppSetting::findOrFail($id);

            return view('app_settings.edit', [
                'setting' => $setting,
                'categories' => $this->categories,
                'types' => $this->types
            ]);
        } catch (\Throwable $th) {
            return $this->redirectWithError('Setting not found: ' . $th->getMessage());
        }
    }

    /**
     * Update the specified app setting in storage
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key,' . $id,
            'value' => 'required',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|in:whatsapp,mail,application,notifications,general',
            'description' => 'nullable|string|max:1000',
            'is_encrypted' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            $setting = AppSetting::findOrFail($id);

            // Update the setting
            $setting->update([
                'key' => $request->key,
                'value' => $request->value,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'is_encrypted' => $request->has('is_encrypted') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Clear cache
            $this->appSettingService->clearCache();

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting', 'updated')
            );
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'update') . ': ' . $th->getMessage()
            )->withInput();
        }
    }

    /**
     * Remove the specified app setting from storage (soft delete / mark inactive)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Check if user has authorized email domain
            $userEmail = auth()->user()->email ?? '';
            $authorizedDomains = ['@webmonks.in', '@midastech.in'];
            $isAuthorized = false;

            foreach ($authorizedDomains as $domain) {
                if (str_ends_with($userEmail, $domain)) {
                    $isAuthorized = true;
                    break;
                }
            }

            if (!$isAuthorized) {
                return $this->redirectWithError(
                    'You do not have permission to delete app settings. Only @webmonks.in or @midastech.in users can delete settings.'
                );
            }

            $setting = AppSetting::findOrFail($id);

            // Mark as inactive instead of deleting
            $setting->update(['is_active' => 0]);

            // Clear cache
            $this->appSettingService->clearCache();

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting', 'deactivated')
            );
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'deactivate') . ': ' . $th->getMessage()
            );
        }
    }

    /**
     * Toggle setting status
     *
     * @param int $id
     * @param int $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id, $status)
    {
        try {
            $setting = AppSetting::findOrFail($id);
            $setting->update(['is_active' => $status]);

            // Clear cache
            $this->appSettingService->clearCache();

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting status', 'updated')
            );
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting status', 'update') . ': ' . $th->getMessage()
            );
        }
    }

    /**
     * Get decrypted value for encrypted setting (AJAX)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDecryptedValue($id)
    {
        try {
            $setting = AppSetting::findOrFail($id);

            if (!$setting->is_encrypted) {
                return response()->json([
                    'success' => false,
                    'message' => 'This setting is not encrypted.'
                ], 400);
            }

            // Get decrypted value (accessor handles decryption)
            $decryptedValue = $setting->value;

            return response()->json([
                'success' => true,
                'value' => $decryptedValue
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error decrypting value: ' . $th->getMessage()
            ], 500);
        }
    }
}
