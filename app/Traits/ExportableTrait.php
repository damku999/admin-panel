<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Response;

trait ExportableTrait
{
    /**
     * Export data to Excel
     *
     * @param string $exportClass The export class to use
     * @param string $filename The filename for the export
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(string $exportClass, string $filename = null): Response
    {
        if (!$filename) {
            $filename = strtolower(class_basename($this)) . 's.xlsx';
        }

        if (!str_ends_with($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return Excel::download(new $exportClass, $filename);
    }

    /**
     * Common export method that controllers can call
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(): Response
    {
        // Get the export class name based on controller name
        $controllerName = class_basename($this);
        $modelName = str_replace('Controller', '', $controllerName);
        $exportClass = "App\\Exports\\{$modelName}Export";

        // If plural, try singular
        if (!class_exists($exportClass)) {
            $modelName = rtrim($modelName, 's');
            $exportClass = "App\\Exports\\{$modelName}sExport";
        }

        // If still doesn't exist, try with different naming convention
        if (!class_exists($exportClass)) {
            $exportClass = "App\\Exports\\{$modelName}Export";
        }

        if (!class_exists($exportClass)) {
            abort(404, "Export class not found: {$exportClass}");
        }

        $filename = strtolower(str_replace(['Controller', '_'], ['', '-'], class_basename($this))) . '.xlsx';
        
        return $this->exportToExcel($exportClass, $filename);
    }
}