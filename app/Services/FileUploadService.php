<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadCustomerDocument(UploadedFile $file, int $customerId, string $documentType, string $customerName): string
    {
        $timestamp = time();
        $extension = $file->getClientOriginalExtension();
        $filename = $this->sanitizeFilename($customerName) . '_' . $documentType . '_' . $timestamp . '.' . $extension;
        
        return $file->storeAs(
            'customers/' . $customerId . '/' . $documentType . '_path',
            $filename,
            'public'
        );
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    public function getFileUrl(string $path): string
    {
        return Storage::url($path);
    }

    private function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
    }

    public function validateFileType(UploadedFile $file, array $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png']): bool
    {
        return in_array($file->getMimeType(), $allowedMimes);
    }

    public function validateFileSize(UploadedFile $file, int $maxSizeKB = 1024): bool
    {
        return $file->getSize() <= ($maxSizeKB * 1024);
    }
}