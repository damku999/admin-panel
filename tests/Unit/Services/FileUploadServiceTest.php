<?php

namespace Tests\Unit\Services;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class FileUploadServiceTest extends TestCase
{
    protected FileUploadService $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_upload_file_successfully()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $result = $this->fileUploadService->uploadFile($file, 'customers');
        
        $this->assertIsString($result);
        $this->assertStringContains('customers/', $result);
        Storage::disk('public')->assertExists($result);
    }

    public function test_upload_multiple_files_successfully()
    {
        Storage::fake('public');
        
        $files = [
            UploadedFile::fake()->create('document1.pdf', 100),
            UploadedFile::fake()->create('document2.jpg', 50)
        ];
        
        $result = $this->fileUploadService->uploadMultipleFiles($files, 'customers');
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        foreach ($result as $filePath) {
            $this->assertStringContains('customers/', $filePath);
            Storage::disk('public')->assertExists($filePath);
        }
    }

    public function test_delete_file_successfully()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $filePath = $this->fileUploadService->uploadFile($file, 'customers');
        
        Storage::disk('public')->assertExists($filePath);
        
        $result = $this->fileUploadService->deleteFile($filePath);
        
        $this->assertTrue($result);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_delete_file_returns_false_for_non_existent_file()
    {
        Storage::fake('public');
        
        $result = $this->fileUploadService->deleteFile('non-existent/file.pdf');
        
        $this->assertFalse($result);
    }

    public function test_get_file_url_returns_correct_url()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $filePath = $this->fileUploadService->uploadFile($file, 'customers');
        
        $url = $this->fileUploadService->getFileUrl($filePath);
        
        $this->assertIsString($url);
        $this->assertStringContains($filePath, $url);
    }

    public function test_validate_file_type_allows_valid_extensions()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $result = $this->fileUploadService->validateFileType($file, ['pdf', 'jpg', 'png']);
        
        $this->assertTrue($result);
    }

    public function test_validate_file_type_rejects_invalid_extensions()
    {
        $file = UploadedFile::fake()->create('document.txt', 100);
        
        $result = $this->fileUploadService->validateFileType($file, ['pdf', 'jpg', 'png']);
        
        $this->assertFalse($result);
    }

    public function test_validate_file_size_allows_valid_size()
    {
        $file = UploadedFile::fake()->create('document.pdf', 500); // 500KB
        
        $result = $this->fileUploadService->validateFileSize($file, 1024); // 1MB limit
        
        $this->assertTrue($result);
    }

    public function test_validate_file_size_rejects_oversized_file()
    {
        $file = UploadedFile::fake()->create('document.pdf', 2048); // 2MB
        
        $result = $this->fileUploadService->validateFileSize($file, 1024); // 1MB limit
        
        $this->assertFalse($result);
    }

    public function test_generate_unique_filename_creates_unique_name()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $filename1 = $this->fileUploadService->generateUniqueFilename($file);
        $filename2 = $this->fileUploadService->generateUniqueFilename($file);
        
        $this->assertNotEquals($filename1, $filename2);
        $this->assertStringEndsWith('.pdf', $filename1);
        $this->assertStringEndsWith('.pdf', $filename2);
    }

    public function test_get_file_size_returns_human_readable_size()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 1024); // 1MB
        $filePath = $this->fileUploadService->uploadFile($file, 'customers');
        
        $size = $this->fileUploadService->getFileSize($filePath);
        
        $this->assertIsString($size);
        $this->assertStringContains('KB', $size);
    }
}