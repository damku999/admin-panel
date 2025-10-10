<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\AppSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * App Setting Controller
 *
 * Handles AppSetting CRUD operations for managing application configuration.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class AppSettingController extends AbstractBaseCrudController
{
    protected array $categories = [
        'application' => 'Application',
        'company' => 'Company',
        'whatsapp' => 'WhatsApp',
        'mail' => 'Mail',
        'notifications' => 'Notifications',
        'general' => 'General',
    ];

    protected array $types = [
        'string' => 'String',
        'json' => 'JSON',
        'boolean' => 'Boolean',
        'numeric' => 'Numeric',
    ];

    public function __construct(protected AppSettingService $appSettingService)
    {
        $this->setupPermissionMiddleware('app-setting');
    }

    /**
     * Display a listing of the app settings
     *
     * @return View|RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $query = AppSetting::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(static function ($q) use ($search): void {
                    $q->where('key', 'LIKE', sprintf('%%%s%%', $search))
                        ->orWhere('description', 'LIKE', sprintf('%%%s%%', $search))
                        ->orWhere('category', 'LIKE', sprintf('%%%s%%', $search));
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

            // Sorting
            $sortBy = $request->get('sort_by', 'category');
            $sortOrder = $request->get('sort_order', 'asc');

            // Validate sort columns
            $allowedSorts = ['key', 'category', 'type', 'is_active', 'created_at', 'updated_at'];
            if (! in_array($sortBy, $allowedSorts)) {
                $sortBy = 'category';
            }

            // Validate sort order
            if (! in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'asc';
            }

            // Apply sorting with secondary sort by key
            if ($sortBy === 'category') {
                $settings = $query->orderBy($sortBy, $sortOrder)->orderBy('key', 'asc')->paginate(config('app.pagination_default', 15));
            } else {
                $settings = $query->orderBy($sortBy, $sortOrder)->paginate(config('app.pagination_default', 15));
            }

            $settings->appends($request->except('page'));

            return view('app_settings.index', [
                'settings' => $settings,
                'categories' => $this->categories,
            ]);
        } catch (\Throwable $throwable) {
            return $this->redirectWithError('Failed to load settings: '.$throwable->getMessage());
        }
    }

    /**
     * Show the form for creating a new app setting
     *
     * @return View
     */
    public function create()
    {
        return view('app_settings.create', [
            'categories' => $this->categories,
            'types' => $this->types,
        ]);
    }

    /**
     * Store a newly created app setting in storage
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key',
            'value' => 'required',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|in:application,company,whatsapp,mail,notifications,general',
            'description' => 'nullable|string|max:1000',
            'is_encrypted' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
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
                    'is_active' => $request->has('is_active') ? 1 : 0,
                ]
            );

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting', 'created')
            );
        } catch (\Throwable $throwable) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'create').': '.$throwable->getMessage()
            )->withInput();
        }
    }

    /**
     * Display the specified app setting
     *
     * @param  int  $id
     * @return View|RedirectResponse
     */
    public function show($id)
    {
        try {
            $setting = AppSetting::query()->findOrFail($id);

            return view('app_settings.show', [
                'setting' => $setting,
                'categories' => $this->categories,
                'types' => $this->types,
            ]);
        } catch (\Throwable $throwable) {
            return $this->redirectWithError('Setting not found: '.$throwable->getMessage());
        }
    }

    /**
     * Show the form for editing the specified app setting
     *
     * @param  int  $id
     * @return View|RedirectResponse
     */
    public function edit($id)
    {
        try {
            $setting = AppSetting::query()->findOrFail($id);

            return view('app_settings.edit', [
                'setting' => $setting,
                'categories' => $this->categories,
                'types' => $this->types,
            ]);
        } catch (\Throwable $throwable) {
            return $this->redirectWithError('Setting not found: '.$throwable->getMessage());
        }
    }

    /**
     * Update the specified app setting in storage
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key,'.$id,
            'value' => 'required',
            'type' => 'required|in:string,json,boolean,numeric',
            'category' => 'required|in:application,company,whatsapp,mail,notifications,general',
            'description' => 'nullable|string|max:1000',
            'is_encrypted' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $setting = AppSetting::query()->findOrFail($id);

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
        } catch (\Throwable $throwable) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'update').': '.$throwable->getMessage()
            )->withInput();
        }
    }

    /**
     * Remove the specified app setting from storage (soft delete / mark inactive)
     *
     * @param  int  $id
     */
    public function destroy($id): RedirectResponse
    {
        try {
            // Check if user has authorized email domain
            $userEmail = auth()->user()->email ?? '';
            $authorizedDomains = ['@webmonks.in', '@midastech.in'];
            $isAuthorized = false;

            foreach ($authorizedDomains as $authorizedDomain) {
                if (str_ends_with($userEmail, $authorizedDomain)) {
                    $isAuthorized = true;
                    break;
                }
            }

            if (! $isAuthorized) {
                return $this->redirectWithError(
                    'You do not have permission to delete app settings. Only @webmonks.in or @midastech.in users can delete settings.'
                );
            }

            $setting = AppSetting::query()->findOrFail($id);

            // Mark as inactive instead of deleting
            $setting->update(['is_active' => 0]);

            // Clear cache
            $this->appSettingService->clearCache();

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting', 'deactivated')
            );
        } catch (\Throwable $throwable) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting', 'deactivate').': '.$throwable->getMessage()
            );
        }
    }

    /**
     * Toggle setting status
     *
     * @param  int  $id
     * @param  int  $status
     */
    public function updateStatus($id, $status): RedirectResponse
    {
        try {
            $setting = AppSetting::query()->findOrFail($id);
            $setting->update(['is_active' => $status]);

            // Clear cache
            $this->appSettingService->clearCache();

            return $this->redirectWithSuccess(
                'app-settings.index',
                $this->getSuccessMessage('App Setting status', 'updated')
            );
        } catch (\Throwable $throwable) {
            return $this->redirectWithError(
                $this->getErrorMessage('App Setting status', 'update').': '.$throwable->getMessage()
            );
        }
    }

    /**
     * Get decrypted value for encrypted setting (AJAX)
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function getDecryptedValue($id)
    {
        try {
            $setting = AppSetting::query()->findOrFail($id);

            if (! $setting->is_encrypted) {
                return response()->json([
                    'success' => false,
                    'message' => 'This setting is not encrypted.',
                ], 400);
            }

            // Get decrypted value (accessor handles decryption)
            $decryptedValue = $setting->value;

            return response()->json([
                'success' => true,
                'value' => $decryptedValue,
            ]);

        } catch (\Throwable $throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Error decrypting value: '.$throwable->getMessage(),
            ], 500);
        }
    }
}
