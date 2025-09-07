<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport;

class ExcelExportService
{
    /**
     * Export data to Excel using generic export class
     *
     * @param string|Builder|Collection $data Model class name, query builder, or collection
     * @param array $config Export configuration
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($data, array $config = [])
    {
        $config = array_merge($this->getDefaultConfig(), $config);
        
        // Resolve data source
        $collection = $this->resolveDataSource($data, $config);
        
        // Generate filename
        $filename = $this->generateFilename($config);
        
        // Create and download export
        $export = new GenericExport($collection, $config);
        
        return Excel::download($export, $filename);
    }
    
    /**
     * Export with custom mapping and headers
     */
    public function exportWithMapping($data, array $headings, callable $mapping, array $config = [])
    {
        $config = array_merge($config, [
            'headings' => $headings,
            'mapping' => $mapping,
            'with_headings' => true,
            'with_mapping' => true
        ]);
        
        return $this->export($data, $config);
    }
    
    /**
     * Quick export for simple models (just basic columns)
     */
    public function quickExport(string $modelClass, array $columns = [], array $config = [])
    {
        $model = app($modelClass);
        $columns = $columns ?: $this->getDefaultColumns($model);
        
        $config = array_merge($config, [
            'columns' => $columns,
            'headings' => $this->generateHeadingsFromColumns($columns),
            'with_headings' => true
        ]);
        
        return $this->export($modelClass, $config);
    }
    
    /**
     * Export with relationships
     */
    public function exportWithRelations($modelClass, array $relations, array $config = [])
    {
        $config = array_merge($config, [
            'relations' => $relations,
            'with_relations' => true
        ]);
        
        return $this->export($modelClass, $config);
    }
    
    /**
     * Export filtered data
     */
    public function exportFiltered($modelClass, array $filters, array $config = [])
    {
        $query = app($modelClass)->newQuery();
        
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (!empty($value)) {
                $query->where($field, 'like', "%{$value}%");
            }
        }
        
        return $this->export($query, $config);
    }
    
    /**
     * Export date range data
     */
    public function exportDateRange($modelClass, string $dateField, $startDate, $endDate, array $config = [])
    {
        $query = app($modelClass)->newQuery()
            ->whereBetween($dateField, [$startDate, $endDate])
            ->orderBy($dateField, 'desc');
            
        $config = array_merge($config, [
            'filename_suffix' => date('Y_m_d', strtotime($startDate)) . '_to_' . date('Y_m_d', strtotime($endDate))
        ]);
        
        return $this->export($query, $config);
    }
    
    /**
     * Resolve data source to collection
     */
    private function resolveDataSource($data, array $config)
    {
        if ($data instanceof Collection || $data instanceof SupportCollection) {
            return $data;
        }
        
        if ($data instanceof Builder) {
            if (!empty($config['relations'])) {
                $data->with($config['relations']);
            }
            
            if (!empty($config['order_by'])) {
                $data->orderBy($config['order_by']['column'], $config['order_by']['direction'] ?? 'asc');
            } else {
                $data->orderBy('created_at', 'desc');
            }
            
            if (!empty($config['limit'])) {
                $data->limit($config['limit']);
            }
            
            return $data->get();
        }
        
        if (is_string($data) && class_exists($data)) {
            $query = app($data)->newQuery();
            
            if (!empty($config['relations'])) {
                $query->with($config['relations']);
            }
            
            if (!empty($config['columns'])) {
                $query->select($config['columns']);
            }
            
            if (!empty($config['order_by'])) {
                $query->orderBy($config['order_by']['column'], $config['order_by']['direction'] ?? 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            if (!empty($config['limit'])) {
                $query->limit($config['limit']);
            }
            
            return $query->get();
        }
        
        throw new \InvalidArgumentException('Invalid data source provided for export');
    }
    
    /**
     * Generate filename
     */
    private function generateFilename(array $config): string
    {
        $base = $config['filename'] ?? 'export';
        $suffix = $config['filename_suffix'] ?? date('Y_m_d_H_i_s');
        $extension = $config['format'] ?? 'xlsx';
        
        return "{$base}_{$suffix}.{$extension}";
    }
    
    /**
     * Get default configuration
     */
    private function getDefaultConfig(): array
    {
        return [
            'format' => 'xlsx',
            'with_headings' => false,
            'with_mapping' => false,
            'with_relations' => false,
            'strict_null_comparison' => true,
            'auto_size' => true,
            'order_by' => ['column' => 'created_at', 'direction' => 'desc'],
            'limit' => null,
            'relations' => [],
            'columns' => [],
            'headings' => [],
            'mapping' => null,
            'filename' => 'export',
            'filename_suffix' => date('Y_m_d_H_i_s')
        ];
    }
    
    /**
     * Get default columns for a model
     */
    private function getDefaultColumns(Model $model): array
    {
        $fillable = $model->getFillable();
        $basic = ['id', 'created_at', 'updated_at'];
        
        return array_merge($fillable, $basic);
    }
    
    /**
     * Generate headings from column names
     */
    private function generateHeadingsFromColumns(array $columns): array
    {
        return array_map(function($column) {
            return ucwords(str_replace(['_', 'id'], [' ', 'ID'], $column));
        }, $columns);
    }
    
    /**
     * Get predefined export configs for common models
     */
    public function getPresetConfig(string $modelType): array
    {
        $presets = [
            'customers' => [
                'filename' => 'customers',
                'relations' => ['familyGroup'],
                'headings' => [
                    'ID', 'Name', 'Email', 'Mobile Number', 'Status', 
                    'Date of Birth', 'PAN Number', 'Aadhar Number', 
                    'Family Group', 'Created Date'
                ],
                'mapping' => function($customer) {
                    return [
                        $customer->id,
                        $customer->name,
                        $customer->email,
                        $customer->mobile_number,
                        ucfirst($customer->status),
                        $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '',
                        $customer->pan_card_number,
                        $customer->aadhar_card_number,
                        $customer->familyGroup ? $customer->familyGroup->name : 'Individual',
                        $customer->created_at->format('Y-m-d H:i:s')
                    ];
                },
                'with_headings' => true,
                'with_mapping' => true
            ],
            
            'claims' => [
                'filename' => 'claims',
                'relations' => ['customer', 'customerInsurance.insuranceCompany'],
                'headings' => [
                    'Claim Number', 'Customer Name', 'Customer Mobile', 'Insurance Type',
                    'Policy Number', 'Vehicle Number', 'Incident Date', 'Claim Amount',
                    'Claim Status', 'Insurance Company', 'Patient Name', 'Hospital Name',
                    'Driver Name', 'Accident Location', 'Intimation Date', 'Created Date'
                ],
                'mapping' => function($claim) {
                    return [
                        $claim->claim_number,
                        $claim->customer ? $claim->customer->name : '',
                        $claim->customer ? $claim->customer->mobile_number : '',
                        $claim->insurance_type,
                        $claim->policy_no,
                        $claim->vehicle_number,
                        $claim->incident_date ? $claim->incident_date->format('Y-m-d') : '',
                        $claim->claim_amount,
                        $claim->claim_status,
                        $claim->customerInsurance && $claim->customerInsurance->insuranceCompany 
                            ? $claim->customerInsurance->insuranceCompany->name : '',
                        $claim->patient_name,
                        $claim->hospital_name,
                        $claim->driver_name,
                        $claim->accident_location,
                        $claim->intimation_date ? $claim->intimation_date->format('Y-m-d') : '',
                        $claim->created_at->format('Y-m-d H:i:s')
                    ];
                },
                'with_headings' => true,
                'with_mapping' => true
            ],
            
            'customer_insurances' => [
                'filename' => 'customer_insurances',
                'relations' => ['customer', 'insuranceCompany'],
                'headings' => [
                    'Policy Number', 'Customer Name', 'Insurance Company', 'Policy Type',
                    'Premium Amount', 'Start Date', 'End Date', 'Status', 'Vehicle Number',
                    'Created Date'
                ],
                'mapping' => function($insurance) {
                    return [
                        $insurance->policy_number,
                        $insurance->customer ? $insurance->customer->name : '',
                        $insurance->insuranceCompany ? $insurance->insuranceCompany->name : '',
                        $insurance->insurance_type,
                        $insurance->premium_amount,
                        $insurance->start_date ? $insurance->start_date->format('Y-m-d') : '',
                        $insurance->end_date ? $insurance->end_date->format('Y-m-d') : '',
                        ucfirst($insurance->status),
                        $insurance->vehicle_registration_no,
                        $insurance->created_at->format('Y-m-d H:i:s')
                    ];
                },
                'with_headings' => true,
                'with_mapping' => true
            ]
        ];
        
        return $presets[$modelType] ?? [];
    }
}