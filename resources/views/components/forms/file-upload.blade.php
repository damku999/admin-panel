{{--
    File Upload Component
    
    Usage:
    <x-forms.file-upload 
        id="document_upload"
        name="documents[]"
        label="Upload Documents"
        :multiple="true"
        accept=".pdf,.jpg,.jpeg,.png"
        max-size="5"
        :required="true"
        :show-preview="true"
        upload-text="Choose Files"
        drop-text="Drop files here">
    </x-forms.file-upload>
--}}

@props([
    'id' => 'file_upload',
    'name' => 'file',
    'label' => 'Upload File',
    'multiple' => false,
    'accept' => '',
    'maxSize' => '2', // MB
    'required' => false,
    'disabled' => false,
    'showLabel' => true,
    'showPreview' => true,
    'uploadText' => 'Choose File',
    'dropText' => 'Drop files here or click to browse',
    'maxFiles' => 10,
    'allowedTypes' => '', // 'images', 'documents', 'all'
])

@php
    $acceptTypes = '';
    if ($allowedTypes === 'images') {
        $acceptTypes = '.jpg,.jpeg,.png,.gif,.webp';
    } elseif ($allowedTypes === 'documents') {
        $acceptTypes = '.pdf,.doc,.docx,.xls,.xlsx,.txt';
    } elseif ($accept) {
        $acceptTypes = $accept;
    }
    
    $inputName = $multiple ? (str_ends_with($name, '[]') ? $name : $name . '[]') : $name;
@endphp

<div class="file-upload-container" data-max-size="{{ $maxSize }}" data-max-files="{{ $maxFiles }}">
    @if($showLabel && $label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif
    
    <div class="file-upload-area border border-2 border-dashed rounded p-4 text-center position-relative"
         ondrop="handleFileDrop(event, '{{ $id }}')"
         ondragover="handleDragOver(event)"
         ondragleave="handleDragLeave(event)">
        
        <input type="file" 
               id="{{ $id }}" 
               name="{{ $inputName }}"
               class="file-input d-none"
               @if($acceptTypes) accept="{{ $acceptTypes }}" @endif
               @if($multiple) multiple @endif
               @if($disabled) disabled @endif
               @if($required) required @endif
               onchange="handleFileSelect(event)">
        
        <div class="upload-prompt">
            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
            <p class="mb-2">{{ $dropText }}</p>
            <button type="button" 
                    class="btn btn-primary btn-sm"
                    onclick="document.getElementById('{{ $id }}').click()"
                    @if($disabled) disabled @endif>
                <i class="fas fa-plus"></i> {{ $uploadText }}
            </button>
        </div>
        
        @if($showPreview)
            <div class="file-preview-container mt-3" style="display: none;">
                <div class="file-preview-list"></div>
            </div>
        @endif
    </div>
    
    <div class="file-upload-info mt-2">
        <small class="text-muted">
            @if($acceptTypes)
                Allowed types: {{ str_replace(['.', ','], ['', ', '], $acceptTypes) }}. 
            @endif
            Max size: {{ $maxSize }}MB per file.
            @if($multiple)
                Max {{ $maxFiles }} files.
            @endif
        </small>
    </div>
    
    @error(str_replace('[]', '', $name))
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
function handleFileDrop(event, inputId) {
    event.preventDefault();
    event.stopPropagation();
    
    const uploadArea = event.currentTarget;
    uploadArea.classList.remove('drag-over');
    
    const files = event.dataTransfer.files;
    const input = document.getElementById(inputId);
    
    // Update input files
    input.files = files;
    handleFileSelect({ target: input });
}

function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.add('drag-over');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.remove('drag-over');
}

function handleFileSelect(event) {
    const input = event.target;
    const container = input.closest('.file-upload-container');
    const previewContainer = container.querySelector('.file-preview-container');
    const previewList = container.querySelector('.file-preview-list');
    const maxSize = parseInt(container.dataset.maxSize) * 1024 * 1024; // Convert MB to bytes
    const maxFiles = parseInt(container.dataset.maxFiles);
    
    if (!input.files.length) return;
    
    const files = Array.from(input.files);
    
    // Validate file count
    if (files.length > maxFiles) {
        show_notification('error', `Maximum ${maxFiles} files allowed`);
        input.value = '';
        return;
    }
    
    // Validate each file
    let validFiles = [];
    for (let file of files) {
        if (file.size > maxSize) {
            show_notification('error', `File "${file.name}" is too large. Maximum size is ${container.dataset.maxSize}MB`);
            continue;
        }
        validFiles.push(file);
    }
    
    if (validFiles.length !== files.length) {
        // Update input with only valid files
        const dt = new DataTransfer();
        validFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }
    
    // Show preview
    if (previewContainer && validFiles.length > 0) {
        previewContainer.style.display = 'block';
        previewList.innerHTML = '';
        
        validFiles.forEach((file, index) => {
            const fileItem = createFilePreview(file, index, input.id);
            previewList.appendChild(fileItem);
        });
    }
}

function createFilePreview(file, index, inputId) {
    const item = document.createElement('div');
    item.className = 'file-preview-item d-flex align-items-center justify-content-between bg-light p-2 rounded mb-2';
    
    const isImage = file.type.startsWith('image/');
    const fileIcon = isImage ? 'fas fa-image' : 'fas fa-file';
    const fileSize = formatFileSize(file.size);
    
    item.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${fileIcon} text-primary me-2"></i>
            <div>
                <div class="fw-bold">${file.name}</div>
                <small class="text-muted">${fileSize}</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index}, '${inputId}')">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    return item;
}

function removeFile(index, inputId) {
    const input = document.getElementById(inputId);
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    handleFileSelect({ target: input });
    
    // Hide preview if no files
    if (!input.files.length) {
        const container = input.closest('.file-upload-container');
        const previewContainer = container.querySelector('.file-preview-container');
        if (previewContainer) {
            previewContainer.style.display = 'none';
        }
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<style>
.file-upload-area {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    background-color: #e9ecef;
    border-color: #0d6efd !important;
}

.file-upload-area.drag-over {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: #0d6efd !important;
}

.file-preview-item {
    border: 1px solid #dee2e6;
}

.file-upload-container .file-input:focus + .upload-prompt {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}
</style>