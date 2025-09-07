<?php

namespace App\Traits;

use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ExportableTrait
{
    /**
     * Export data using the reusable export system
     */
    public function export(Request $request)
    {
        $exportService = app(ExcelExportService::class);
        
        // Get model class from controller
        $modelClass = $this->getExportModelClass();
        
        // Get export configuration
        $config = $this->getExportConfig($request);
        
        // Apply filters if provided
        if ($request->hasAny(['search', 'status', 'start_date', 'end_date'])) {
            return $this->exportFiltered($request, $exportService, $modelClass, $config);
        }
        
        // Use preset config if available
        $presetType = $this->getExportPresetType();
        if ($presetType) {
            $presetConfig = $exportService->getPresetConfig($presetType);
            $config = array_merge($presetConfig, $config);
            
            return $exportService->export($modelClass, $config);
        }
        
        // Fallback to basic export
        return $exportService->quickExport($modelClass, [], $config);
    }
    
    /**
     * Export with date range filter
     */
    public function exportDateRange(Request $request, string $startDate, string $endDate)
    {
        $exportService = app(ExcelExportService::class);
        $modelClass = $this->getExportModelClass();
        $dateField = $this->getDateFilterField();
        $config = $this->getExportConfig($request);
        
        return $exportService->exportDateRange($modelClass, $dateField, $startDate, $endDate, $config);
    }
    
    /**
     * Export filtered data
     */
    protected function exportFiltered(Request $request, ExcelExportService $exportService, string $modelClass, array $config)
    {
        $filters = [];
        
        // Search filter
        if ($request->filled('search')) {
            $searchFields = $this->getSearchableFields();
            $query = app($modelClass)->newQuery();
            
            foreach ($searchFields as $field) {
                $query->orWhere($field, 'like', '%' . $request->search . '%');
            }
            
            return $exportService->export($query, $config);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $filters['status'] = $request->status;
        }
        
        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            return $exportService->exportDateRange(
                $modelClass,
                $this->getDateFilterField(),
                $request->start_date,
                $request->end_date,
                $config
            );
        } elseif ($request->filled('start_date') || $request->filled('end_date')) {
            $dateField = $this->getDateFilterField();
            $query = app($modelClass)->newQuery();
            
            if ($request->filled('start_date')) {
                $query->where($dateField, '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->where($dateField, '<=', $request->end_date);
            }
            
            return $exportService->export($query, $config);
        }
        
        // Apply other filters
        if (!empty($filters)) {
            return $exportService->exportFiltered($modelClass, $filters, $config);
        }
        
        return $exportService->export($modelClass, $config);
    }
    
    /**
     * Get the model class for export - override in controllers
     */
    protected function getExportModelClass(): string
    {
        // Try to auto-detect from controller name
        $controllerName = class_basename($this);
        $modelName = str_replace('Controller', '', $controllerName);
        
        // Handle plural controller names
        if (Str::endsWith($modelName, 's')) {
            $modelName = Str::singular($modelName);
        }
        
        $modelClass = "App\\Models\\{$modelName}";
        
        if (!class_exists($modelClass)) {
            throw new \Exception("Model class {$modelClass} not found. Please override getExportModelClass() method.");
        }
        
        return $modelClass;
    }
    
    /**
     * Get export configuration - override in controllers for customization
     */
    protected function getExportConfig(Request $request): array
    {
        return $this->getBaseExportConfig($request);
    }
    
    /**
     * Get base export configuration that can be merged in controllers
     */
    protected function getBaseExportConfig(Request $request): array
    {
        return [
            'format' => $request->get('format', 'xlsx'),
            'filename' => $this->getExportFilename(),
            'with_headings' => true,
            'auto_size' => true,
            'relations' => $this->getExportRelations(),
            'order_by' => $this->getExportOrderBy()
        ];
    }
    
    /**
     * Get preset type for predefined export configs - override in controllers
     */
    protected function getExportPresetType(): ?string
    {
        $controllerName = class_basename($this);
        
        return match($controllerName) {
            'CustomerController' => 'customers',
            'ClaimController' => 'claims',
            'CustomerInsuranceController' => 'customer_insurances',
            default => null
        };
    }
    
    /**
     * Get export filename - override in controllers
     */
    protected function getExportFilename(): string
    {
        $controllerName = class_basename($this);
        $name = str_replace('Controller', '', $controllerName);
        
        return Str::snake(Str::plural($name));
    }
    
    /**
     * Get relationships to include in export - override in controllers
     */
    protected function getExportRelations(): array
    {
        return [];
    }
    
    /**
     * Get default order by for export - override in controllers
     */
    protected function getExportOrderBy(): array
    {
        return ['column' => 'created_at', 'direction' => 'desc'];
    }
    
    /**
     * Get searchable fields for filtering - override in controllers
     */
    protected function getSearchableFields(): array
    {
        return ['name']; // Default to name field
    }
    
    /**
     * Get date field for date range filtering - override in controllers
     */
    protected function getDateFilterField(): string
    {
        return 'created_at';
    }
    
    /**
     * Example method showing how to override in specific controllers
     */
    // Example for CustomerController:
    // protected function getExportRelations(): array
    // {
    //     return ['familyGroup'];
    // }
    //
    // protected function getSearchableFields(): array
    // {
    //     return ['name', 'email', 'mobile_number'];
    // }
    //
    // protected function getExportConfig(Request $request): array
    // {
    //     $config = parent::getExportConfig($request);
    //     
    //     return array_merge($config, [
    //         'headings' => ['ID', 'Name', 'Email', 'Mobile', 'Status', 'Created'],
    //         'mapping' => function($customer) {
    //             return [
    //                 $customer->id,
    //                 $customer->name,
    //                 $customer->email,
    //                 $customer->mobile_number,
    //                 ucfirst($customer->status),
    //                 $customer->created_at->format('Y-m-d H:i:s')
    //             ];
    //         }
    //     ]);
    // }
}