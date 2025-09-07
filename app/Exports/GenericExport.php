<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenericExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStrictNullComparison, 
    ShouldAutoSize,
    WithStyles
{
    protected Collection $collection;
    protected array $config;
    
    public function __construct(Collection $collection, array $config = [])
    {
        $this->collection = $collection;
        $this->config = $config;
    }
    
    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        if (!$this->config['with_headings'] ?? false) {
            return [];
        }
        
        return $this->config['headings'] ?? [];
    }
    
    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        if (!$this->config['with_mapping'] ?? false) {
            return $this->defaultMapping($row);
        }
        
        $mapping = $this->config['mapping'] ?? null;
        
        if (is_callable($mapping)) {
            return $mapping($row);
        }
        
        return $this->defaultMapping($row);
    }
    
    /**
     * Default mapping when no custom mapping provided
     */
    private function defaultMapping($row): array
    {
        if (is_array($row)) {
            return $row;
        }
        
        if (is_object($row)) {
            // If it's a model, get its attributes
            if (method_exists($row, 'toArray')) {
                $data = $row->toArray();
                
                // If specific columns are defined, only include those
                if (!empty($this->config['columns'])) {
                    $filtered = [];
                    foreach ($this->config['columns'] as $column) {
                        $filtered[$column] = $data[$column] ?? '';
                    }
                    return array_values($filtered);
                }
                
                return array_values($data);
            }
            
            // Convert object to array
            return array_values((array) $row);
        }
        
        return [$row];
    }
    
    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $styles = [];
        
        // Header styles
        if ($this->config['with_headings'] ?? false) {
            $headerRow = 1;
            $lastColumn = $this->getLastColumn();
            
            $styles["A{$headerRow}:{$lastColumn}{$headerRow}"] = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
        }
        
        // Data rows styles
        $dataStartRow = ($this->config['with_headings'] ?? false) ? 2 : 1;
        $dataEndRow = $dataStartRow + $this->collection->count() - 1;
        $lastColumn = $this->getLastColumn();
        
        if ($dataEndRow >= $dataStartRow) {
            $styles["A{$dataStartRow}:{$lastColumn}{$dataEndRow}"] = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ];
            
            // Alternate row colors
            for ($row = $dataStartRow; $row <= $dataEndRow; $row += 2) {
                $styles["A{$row}:{$lastColumn}{$row}"] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ];
            }
        }
        
        return $styles;
    }
    
    /**
     * Get the last column letter based on headings count
     */
    private function getLastColumn(): string
    {
        $headingsCount = count($this->config['headings'] ?? []);
        
        if ($headingsCount === 0 && $this->collection->isNotEmpty()) {
            // Get column count from first row
            $firstRow = $this->collection->first();
            $mapped = $this->map($firstRow);
            $headingsCount = count($mapped);
        }
        
        if ($headingsCount <= 26) {
            return chr(64 + $headingsCount); // A-Z
        }
        
        // For more than 26 columns, use AA, AB, etc.
        $firstLetter = chr(64 + floor(($headingsCount - 1) / 26));
        $secondLetter = chr(65 + (($headingsCount - 1) % 26));
        
        return $firstLetter . $secondLetter;
    }
}