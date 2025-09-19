<?php

namespace App\Http\Controllers;

/**
 * Abstract Base CRUD Controller
 *
 * Provides common CRUD controller functionality including standardized middleware setup.
 * Eliminates duplicate permission middleware code across all CRUD controllers.
 */
abstract class AbstractBaseCrudController extends Controller
{
    /**
     * Setup permission middleware for CRUD operations
     *
     * This method provides standardized permission middleware setup for all CRUD controllers,
     * ensuring consistent security patterns across the application.
     *
     * @param string $entityName The entity name for permission checks (e.g., 'broker', 'addon-cover')
     * @return void
     */
    protected function setupPermissionMiddleware(string $entityName): void
    {
        $this->middleware('auth');
        $this->middleware("permission:{$entityName}-list|{$entityName}-create|{$entityName}-edit|{$entityName}-delete", ['only' => ['index']]);
        $this->middleware("permission:{$entityName}-create", ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware("permission:{$entityName}-edit", ['only' => ['edit', 'update']]);
        $this->middleware("permission:{$entityName}-delete", ['only' => ['delete']]);
    }

    /**
     * Setup custom permission middleware
     *
     * For controllers that need custom permission patterns beyond standard CRUD.
     *
     * @param array $permissions Array of permission configurations
     * @return void
     *
     * Example usage:
     * $this->setupCustomPermissionMiddleware([
     *     ['permission' => 'user-list', 'only' => ['index']],
     *     ['permission' => 'user-create', 'only' => ['create', 'store']],
     * ]);
     */
    protected function setupCustomPermissionMiddleware(array $permissions): void
    {
        $this->middleware('auth');

        foreach ($permissions as $config) {
            $this->middleware("permission:{$config['permission']}", $config['only'] ?? []);
        }
    }

    /**
     * Setup authentication middleware only
     *
     * For controllers that need authentication but no specific permissions.
     *
     * @return void
     */
    protected function setupAuthMiddleware(): void
    {
        $this->middleware('auth');
    }

    /**
     * Setup guest middleware
     *
     * For controllers that should only be accessible to guests (not authenticated users).
     *
     * @return void
     */
    protected function setupGuestMiddleware(): void
    {
        $this->middleware('guest');
    }

    /**
     * Get standardized success message for CRUD operations
     *
     * @param string $entityName The entity name (e.g., 'Broker', 'Addon Cover')
     * @param string $operation The operation performed ('created', 'updated', 'deleted')
     * @return string
     */
    protected function getSuccessMessage(string $entityName, string $operation): string
    {
        return "{$entityName} {$operation} successfully!";
    }

    /**
     * Get standardized error message for CRUD operations
     *
     * @param string $entityName The entity name (e.g., 'Broker', 'Addon Cover')
     * @param string $operation The operation attempted ('create', 'update', 'delete')
     * @return string
     */
    protected function getErrorMessage(string $entityName, string $operation): string
    {
        return "Failed to {$operation} {$entityName}. Please try again.";
    }

    /**
     * Get redirect response with success message
     *
     * @param string|null $route The route to redirect to (null for back)
     * @param string $message The success message
     * @param array $routeParameters Optional route parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithSuccess(?string $route, string $message, array $routeParameters = []): \Illuminate\Http\RedirectResponse
    {
        if ($route === null) {
            return redirect()->back()->with('success', $message);
        }

        if (!empty($routeParameters)) {
            return redirect()->route($route, $routeParameters)->with('success', $message);
        }

        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Get redirect response with error message
     *
     * @param string $message The error message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithError(string $message): \Illuminate\Http\RedirectResponse
    {
        return redirect()->back()->with('error', $message);
    }

    /**
     * Get redirect response with validation errors
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator The validator instance
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithValidationErrors(\Illuminate\Contracts\Validation\Validator $validator): \Illuminate\Http\RedirectResponse
    {
        return redirect()->back()->withErrors($validator)->withInput();
    }
}