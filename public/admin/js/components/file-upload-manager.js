/**
 * Advanced File Upload Management System
 * Provides drag & drop, progress tracking, validation, and preview functionality
 */

const FileUploadManager = {
    
    // Upload instances registry
    uploaders: new Map(),
    
    // Default configuration
    defaultConfig: {
        maxFileSize: 10 * 1024 * 1024, // 10MB
        maxFiles: 10,
        allowedTypes: ['image/*', 'application/pdf', 'text/*'],
        dragDrop: true,
        previewImages: true,
        showProgress: true,
        validateOnSelect: true,
        autoUpload: false,
        uploadUrl: '/api/upload',
        deleteUrl: '/api/upload/delete',
        csrfToken: null,
        language: {
            dragText: 'Drag & drop files here or click to browse',
            browseText: 'Browse Files',
            dropText: 'Drop files here to upload',
            maxFilesError: 'Maximum {max} files allowed',
            maxSizeError: 'File size must be less than {size}',
            typeError: 'File type not allowed',
            uploadError: 'Upload failed',
            deleteConfirm: 'Are you sure you want to delete this file?'
        }
    },
    
    // File type icons
    fileIcons: {
        'image': 'fas fa-image',
        'pdf': 'fas fa-file-pdf',
        'word': 'fas fa-file-word',
        'excel': 'fas fa-file-excel',
        'powerpoint': 'fas fa-file-powerpoint',
        'text': 'fas fa-file-alt',
        'archive': 'fas fa-file-archive',
        'video': 'fas fa-file-video',
        'audio': 'fas fa-file-audio',
        'default': 'fas fa-file'
    },
    
    // Initialize the file upload manager
    init: function(options = {}) {
        this.config = { ...this.defaultConfig, ...options };
        
        // Get CSRF token if available
        if (!this.config.csrfToken) {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (tokenMeta) {
                this.config.csrfToken = tokenMeta.getAttribute('content');
            }
        }
        
        // Auto-discover and initialize upload areas
        this.discoverUploadAreas();
        
        // Inject styles
        this.injectStyles();
        
        console.log('📁 FileUploadManager initialized');
    },
    
    /**
     * Discover and initialize upload areas
     */
    discoverUploadAreas: function() {
        // Find elements with file-upload class
        document.querySelectorAll('.file-upload').forEach(element => {
            this.initializeUploader(element);
        });
        
        // Find elements with data-file-upload attribute
        document.querySelectorAll('[data-file-upload]').forEach(element => {
            this.initializeUploader(element);
        });
    },
    
    /**
     * Initialize a file uploader
     */
    initializeUploader: function(element, customConfig = {}) {
        const uploadElement = typeof element === 'string' 
            ? document.getElementById(element) 
            : element;
            
        if (!uploadElement) {
            console.error('Upload element not found');
            return null;
        }
        
        const uploaderId = uploadElement.id || this.generateId();
        if (!uploadElement.id) uploadElement.id = uploaderId;
        
        // Skip if already initialized
        if (this.uploaders.has(uploaderId)) {
            console.warn(`Uploader ${uploaderId} already initialized`);
            return this.uploaders.get(uploaderId);
        }
        
        try {
            // Parse configuration from data attributes
            const dataConfig = this.parseDataAttributes(uploadElement);
            
            // Merge configurations
            const config = { ...this.defaultConfig, ...dataConfig, ...customConfig };
            
            // Create uploader instance
            const uploader = {
                id: uploaderId,
                element: uploadElement,
                config: config,
                files: new Map(),
                isUploading: false,
                initialized: true
            };
            
            // Set up the upload area
            this.setupUploadArea(uploader);
            
            // Store instance
            this.uploaders.set(uploaderId, uploader);
            
            console.log(`📁 Initialized FileUploader: ${uploaderId}`);
            
            return uploader;
            
        } catch (error) {
            console.error(`Failed to initialize uploader ${uploaderId}:`, error);
            return null;
        }
    },
    
    /**
     * Parse configuration from data attributes
     */
    parseDataAttributes: function(element) {
        const config = {};
        const dataset = element.dataset;
        
        if (dataset.maxFileSize) config.maxFileSize = parseInt(dataset.maxFileSize);
        if (dataset.maxFiles) config.maxFiles = parseInt(dataset.maxFiles);
        if (dataset.allowedTypes) config.allowedTypes = dataset.allowedTypes.split(',');
        if (dataset.uploadUrl) config.uploadUrl = dataset.uploadUrl;
        if (dataset.deleteUrl) config.deleteUrl = dataset.deleteUrl;
        if (dataset.autoUpload) config.autoUpload = dataset.autoUpload === 'true';
        if (dataset.dragDrop) config.dragDrop = dataset.dragDrop === 'true';
        if (dataset.previewImages) config.previewImages = dataset.previewImages === 'true';
        
        return config;
    },
    
    /**
     * Set up upload area
     */
    setupUploadArea: function(uploader) {
        const { element, config } = uploader;
        
        // Add CSS classes
        element.classList.add('file-upload-area');
        if (config.dragDrop) {
            element.classList.add('drag-drop-enabled');
        }
        
        // Create upload area HTML
        this.createUploadAreaHTML(uploader);
        
        // Set up event handlers
        this.setupUploadEvents(uploader);
        
        // Load existing files if any
        this.loadExistingFiles(uploader);
    },
    
    /**
     * Create upload area HTML
     */
    createUploadAreaHTML: function(uploader) {
        const { element, config } = uploader;
        
        const html = `
            <div class="upload-zone">
                <div class="upload-message">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <div class="upload-text">${config.language.dragText}</div>
                    <button type="button" class="btn btn-primary btn-sm upload-browse">
                        ${config.language.browseText}
                    </button>
                </div>
                <div class="upload-info">
                    <small class="text-muted">
                        Max ${config.maxFiles} file(s), ${this.formatFileSize(config.maxFileSize)} each
                    </small>
                </div>
            </div>
            <input type="file" class="upload-input" style="display: none;" 
                   ${config.maxFiles > 1 ? 'multiple' : ''} 
                   accept="${config.allowedTypes.join(',')}">
            <div class="upload-files"></div>
        `;
        
        element.innerHTML = html;
    },
    
    /**
     * Set up upload event handlers
     */
    setupUploadEvents: function(uploader) {
        const { element, config } = uploader;
        const uploadZone = element.querySelector('.upload-zone');
        const uploadInput = element.querySelector('.upload-input');
        const browseButton = element.querySelector('.upload-browse');
        
        // File input change
        uploadInput.addEventListener('change', (e) => {
            this.handleFileSelection(uploader, e.target.files);
        });
        
        // Browse button click
        browseButton.addEventListener('click', (e) => {
            e.preventDefault();
            uploadInput.click();
        });
        
        if (config.dragDrop) {
            // Drag and drop events
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('drag-over');
            });
            
            uploadZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                if (!uploadZone.contains(e.relatedTarget)) {
                    uploadZone.classList.remove('drag-over');
                }
            });
            
            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('drag-over');
                
                const files = Array.from(e.dataTransfer.files);
                this.handleFileSelection(uploader, files);
            });
        }
    },
    
    /**
     * Handle file selection
     */
    handleFileSelection: function(uploader, fileList) {
        const files = Array.from(fileList);
        const { config } = uploader;
        
        // Validate file count
        const currentFileCount = uploader.files.size;
        if (currentFileCount + files.length > config.maxFiles) {
            this.showError(config.language.maxFilesError.replace('{max}', config.maxFiles));
            return;
        }
        
        // Process each file
        files.forEach(file => {
            if (config.validateOnSelect) {
                const validation = this.validateFile(file, config);
                if (!validation.valid) {
                    this.showError(validation.error);
                    return;
                }
            }
            
            // Add file to uploader
            this.addFile(uploader, file);
        });
        
        // Auto-upload if enabled
        if (config.autoUpload) {
            this.uploadAll(uploader.id);
        }
    },
    
    /**
     * Validate file
     */
    validateFile: function(file, config) {
        // Check file size
        if (file.size > config.maxFileSize) {
            return {
                valid: false,
                error: config.language.maxSizeError.replace('{size}', this.formatFileSize(config.maxFileSize))
            };
        }
        
        // Check file type
        const isAllowed = config.allowedTypes.some(type => {
            if (type.endsWith('/*')) {
                const category = type.replace('/*', '');
                return file.type.startsWith(category + '/');
            }
            return file.type === type;
        });
        
        if (!isAllowed) {
            return {
                valid: false,
                error: config.language.typeError
            };
        }
        
        return { valid: true };
    },
    
    /**
     * Add file to uploader
     */
    addFile: function(uploader, file) {
        const fileId = this.generateId();
        const fileObj = {
            id: fileId,
            file: file,
            status: 'pending',
            progress: 0,
            element: null,
            uploaded: false,
            url: null,
            error: null
        };
        
        uploader.files.set(fileId, fileObj);
        
        // Create file element
        this.createFileElement(uploader, fileObj);
        
        // Emit event
        this.emit('file:added', { uploader, file: fileObj });
    },
    
    /**
     * Create file element
     */
    createFileElement: function(uploader, fileObj) {
        const { element, config } = uploader;
        const filesContainer = element.querySelector('.upload-files');
        
        const fileElement = document.createElement('div');
        fileElement.className = 'upload-file-item';
        fileElement.dataset.fileId = fileObj.id;
        
        const fileIcon = this.getFileIcon(fileObj.file);
        const fileName = fileObj.file.name;
        const fileSize = this.formatFileSize(fileObj.file.size);
        
        let previewHtml = `<i class="${fileIcon} file-icon"></i>`;
        
        // Create image preview if enabled
        if (config.previewImages && fileObj.file.type.startsWith('image/')) {
            previewHtml = `<img class="file-preview" src="#" alt="Preview">`;
            
            // Load image preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = fileElement.querySelector('.file-preview');
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(fileObj.file);
        }
        
        fileElement.innerHTML = `
            <div class="file-preview-container">
                ${previewHtml}
            </div>
            <div class="file-info">
                <div class="file-name">${fileName}</div>
                <div class="file-size">${fileSize}</div>
                <div class="file-progress">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="file-status">Pending</div>
            </div>
            <div class="file-actions">
                <button type="button" class="btn btn-sm btn-success file-upload-btn" title="Upload">
                    <i class="fas fa-upload"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger file-remove-btn" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Set up file-specific events
        this.setupFileEvents(uploader, fileObj, fileElement);
        
        filesContainer.appendChild(fileElement);
        fileObj.element = fileElement;
    },
    
    /**
     * Set up file-specific event handlers
     */
    setupFileEvents: function(uploader, fileObj, fileElement) {
        const uploadBtn = fileElement.querySelector('.file-upload-btn');
        const removeBtn = fileElement.querySelector('.file-remove-btn');
        
        // Upload button
        uploadBtn.addEventListener('click', () => {
            this.uploadFile(uploader.id, fileObj.id);
        });
        
        // Remove button
        removeBtn.addEventListener('click', () => {
            if (fileObj.uploaded) {
                if (confirm(uploader.config.language.deleteConfirm)) {
                    this.deleteFile(uploader.id, fileObj.id);
                }
            } else {
                this.removeFile(uploader.id, fileObj.id);
            }
        });
    },
    
    /**
     * Upload file
     */
    uploadFile: function(uploaderId, fileId) {
        const uploader = this.uploaders.get(uploaderId);
        const fileObj = uploader.files.get(fileId);
        
        if (!uploader || !fileObj || fileObj.status === 'uploading') {
            return Promise.reject('File not found or already uploading');
        }
        
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', fileObj.file);
            
            if (uploader.config.csrfToken) {
                formData.append('_token', uploader.config.csrfToken);
            }
            
            const xhr = new XMLHttpRequest();
            
            // Update file status
            this.updateFileStatus(fileObj, 'uploading', 'Uploading...');
            
            // Progress handler
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const progress = (e.loaded / e.total) * 100;
                    this.updateFileProgress(fileObj, progress);
                }
            });
            
            // Success handler
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            fileObj.uploaded = true;
                            fileObj.url = response.url;
                            this.updateFileStatus(fileObj, 'completed', 'Uploaded');
                            this.updateFileProgress(fileObj, 100);
                            
                            this.emit('file:uploaded', { uploader, file: fileObj, response });
                            resolve(response);
                        } else {
                            throw new Error(response.message || 'Upload failed');
                        }
                    } catch (error) {
                        this.handleUploadError(fileObj, error.message);
                        reject(error);
                    }
                } else {
                    const error = new Error(`Upload failed with status ${xhr.status}`);
                    this.handleUploadError(fileObj, error.message);
                    reject(error);
                }
            });
            
            // Error handler
            xhr.addEventListener('error', () => {
                const error = new Error('Network error during upload');
                this.handleUploadError(fileObj, error.message);
                reject(error);
            });
            
            // Abort handler
            xhr.addEventListener('abort', () => {
                this.updateFileStatus(fileObj, 'cancelled', 'Cancelled');
                reject(new Error('Upload cancelled'));
            });
            
            // Send request
            xhr.open('POST', uploader.config.uploadUrl);
            xhr.send(formData);
            
            // Store XHR for potential cancellation
            fileObj.xhr = xhr;
        });
    },
    
    /**
     * Upload all files
     */
    uploadAll: function(uploaderId) {
        const uploader = this.uploaders.get(uploaderId);
        if (!uploader) return;
        
        const pendingFiles = Array.from(uploader.files.values())
            .filter(file => file.status === 'pending');
        
        const uploadPromises = pendingFiles.map(file => 
            this.uploadFile(uploaderId, file.id).catch(error => ({ error, file }))
        );
        
        return Promise.all(uploadPromises);
    },
    
    /**
     * Remove file
     */
    removeFile: function(uploaderId, fileId) {
        const uploader = this.uploaders.get(uploaderId);
        const fileObj = uploader.files.get(fileId);
        
        if (!uploader || !fileObj) return false;
        
        // Cancel upload if in progress
        if (fileObj.xhr) {
            fileObj.xhr.abort();
        }
        
        // Remove element
        if (fileObj.element) {
            fileObj.element.remove();
        }
        
        // Remove from files map
        uploader.files.delete(fileId);
        
        this.emit('file:removed', { uploader, file: fileObj });
        return true;
    },
    
    /**
     * Delete uploaded file
     */
    deleteFile: function(uploaderId, fileId) {
        const uploader = this.uploaders.get(uploaderId);
        const fileObj = uploader.files.get(fileId);
        
        if (!uploader || !fileObj || !fileObj.uploaded) {
            return Promise.reject('File not found or not uploaded');
        }
        
        return fetch(uploader.config.deleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': uploader.config.csrfToken
            },
            body: JSON.stringify({
                file_id: fileId,
                url: fileObj.url
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.removeFile(uploaderId, fileId);
                this.emit('file:deleted', { uploader, file: fileObj });
                return data;
            } else {
                throw new Error(data.message || 'Delete failed');
            }
        })
        .catch(error => {
            this.showError('Failed to delete file: ' + error.message);
            throw error;
        });
    },
    
    /**
     * Update file status
     */
    updateFileStatus: function(fileObj, status, message) {
        fileObj.status = status;
        
        if (fileObj.element) {
            const statusElement = fileObj.element.querySelector('.file-status');
            if (statusElement) {
                statusElement.textContent = message;
                statusElement.className = `file-status status-${status}`;
            }
            
            fileObj.element.className = `upload-file-item status-${status}`;
        }
    },
    
    /**
     * Update file progress
     */
    updateFileProgress: function(fileObj, progress) {
        fileObj.progress = progress;
        
        if (fileObj.element) {
            const progressBar = fileObj.element.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = progress + '%';
                progressBar.setAttribute('aria-valuenow', progress);
            }
        }
    },
    
    /**
     * Handle upload error
     */
    handleUploadError: function(fileObj, errorMessage) {
        fileObj.error = errorMessage;
        this.updateFileStatus(fileObj, 'error', 'Error');
        this.showError(errorMessage);
    },
    
    /**
     * Utility functions
     */
    generateId: function() {
        return 'file_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    },
    
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    getFileIcon: function(file) {
        const type = file.type.toLowerCase();
        
        if (type.startsWith('image/')) return this.fileIcons.image;
        if (type.includes('pdf')) return this.fileIcons.pdf;
        if (type.includes('word') || type.includes('document')) return this.fileIcons.word;
        if (type.includes('excel') || type.includes('spreadsheet')) return this.fileIcons.excel;
        if (type.includes('powerpoint') || type.includes('presentation')) return this.fileIcons.powerpoint;
        if (type.includes('text')) return this.fileIcons.text;
        if (type.includes('zip') || type.includes('rar')) return this.fileIcons.archive;
        if (type.startsWith('video/')) return this.fileIcons.video;
        if (type.startsWith('audio/')) return this.fileIcons.audio;
        
        return this.fileIcons.default;
    },
    
    showError: function(message) {
        if (typeof NotificationManager !== 'undefined') {
            NotificationManager.error(message);
        } else if (typeof show_notification === 'function') {
            show_notification('error', message);
        } else {
            alert(message);
        }
    },
    
    /**
     * Inject upload styles
     */
    injectStyles: function() {
        if (document.getElementById('file-upload-styles')) {
            return;
        }
        
        const styles = document.createElement('style');
        styles.id = 'file-upload-styles';
        styles.textContent = `
            .file-upload-area {
                border: 2px dashed #ddd;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                transition: all 0.3s ease;
            }
            
            .file-upload-area.drag-over {
                border-color: #007bff;
                background-color: #f8f9ff;
            }
            
            .upload-zone {
                padding: 40px 20px;
            }
            
            .upload-icon {
                font-size: 48px;
                color: #6c757d;
                margin-bottom: 16px;
            }
            
            .upload-text {
                font-size: 16px;
                color: #6c757d;
                margin-bottom: 16px;
            }
            
            .upload-info {
                margin-top: 16px;
            }
            
            .upload-files {
                margin-top: 20px;
                border-top: 1px solid #eee;
                padding-top: 20px;
            }
            
            .upload-file-item {
                display: flex;
                align-items: center;
                padding: 12px;
                margin-bottom: 8px;
                border: 1px solid #ddd;
                border-radius: 6px;
                background: #f8f9fa;
                transition: all 0.2s ease;
            }
            
            .upload-file-item.status-uploading {
                border-color: #007bff;
                background: #e7f3ff;
            }
            
            .upload-file-item.status-completed {
                border-color: #28a745;
                background: #d4edda;
            }
            
            .upload-file-item.status-error {
                border-color: #dc3545;
                background: #f8d7da;
            }
            
            .file-preview-container {
                flex-shrink: 0;
                margin-right: 12px;
            }
            
            .file-icon {
                font-size: 24px;
                color: #6c757d;
                width: 40px;
                text-align: center;
            }
            
            .file-preview {
                width: 40px;
                height: 40px;
                object-fit: cover;
                border-radius: 4px;
            }
            
            .file-info {
                flex-grow: 1;
                text-align: left;
                min-width: 0;
            }
            
            .file-name {
                font-weight: 600;
                margin-bottom: 4px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .file-size {
                font-size: 12px;
                color: #6c757d;
                margin-bottom: 4px;
            }
            
            .file-progress {
                margin-bottom: 4px;
            }
            
            .file-progress .progress {
                height: 4px;
                background: #e9ecef;
            }
            
            .file-status {
                font-size: 12px;
                font-weight: 600;
            }
            
            .file-status.status-pending {
                color: #6c757d;
            }
            
            .file-status.status-uploading {
                color: #007bff;
            }
            
            .file-status.status-completed {
                color: #28a745;
            }
            
            .file-status.status-error {
                color: #dc3545;
            }
            
            .file-actions {
                flex-shrink: 0;
                display: flex;
                gap: 4px;
            }
            
            .file-actions .btn {
                padding: 4px 8px;
            }
            
            @media (max-width: 576px) {
                .upload-file-item {
                    flex-direction: column;
                    text-align: center;
                }
                
                .file-preview-container {
                    margin-right: 0;
                    margin-bottom: 8px;
                }
                
                .file-actions {
                    margin-top: 8px;
                }
            }
        `;
        
        document.head.appendChild(styles);
    },
    
    /**
     * Load existing files
     */
    loadExistingFiles: function(uploader) {
        const existingFiles = uploader.element.dataset.existingFiles;
        if (existingFiles) {
            try {
                const files = JSON.parse(existingFiles);
                files.forEach(fileData => {
                    // Create file object for existing file
                    const fileObj = {
                        id: fileData.id || this.generateId(),
                        file: null, // No File object for existing files
                        status: 'completed',
                        progress: 100,
                        element: null,
                        uploaded: true,
                        url: fileData.url,
                        name: fileData.name,
                        size: fileData.size,
                        type: fileData.type
                    };
                    
                    uploader.files.set(fileObj.id, fileObj);
                    this.createExistingFileElement(uploader, fileObj);
                });
            } catch (error) {
                console.error('Failed to load existing files:', error);
            }
        }
    },
    
    /**
     * Create element for existing file
     */
    createExistingFileElement: function(uploader, fileObj) {
        // Similar to createFileElement but for existing files
        const { element } = uploader;
        const filesContainer = element.querySelector('.upload-files');
        
        const fileElement = document.createElement('div');
        fileElement.className = 'upload-file-item status-completed';
        fileElement.dataset.fileId = fileObj.id;
        
        const fileIcon = this.getFileIconByName(fileObj.name);
        
        fileElement.innerHTML = `
            <div class="file-preview-container">
                <i class="${fileIcon} file-icon"></i>
            </div>
            <div class="file-info">
                <div class="file-name">${fileObj.name}</div>
                <div class="file-size">${this.formatFileSize(fileObj.size || 0)}</div>
                <div class="file-status status-completed">Uploaded</div>
            </div>
            <div class="file-actions">
                <a href="${fileObj.url}" target="_blank" class="btn btn-sm btn-info" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger file-remove-btn" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Set up remove button
        const removeBtn = fileElement.querySelector('.file-remove-btn');
        removeBtn.addEventListener('click', () => {
            if (confirm(uploader.config.language.deleteConfirm)) {
                this.deleteFile(uploader.id, fileObj.id);
            }
        });
        
        filesContainer.appendChild(fileElement);
        fileObj.element = fileElement;
    },
    
    getFileIconByName: function(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const extensionMap = {
            pdf: this.fileIcons.pdf,
            doc: this.fileIcons.word,
            docx: this.fileIcons.word,
            xls: this.fileIcons.excel,
            xlsx: this.fileIcons.excel,
            ppt: this.fileIcons.powerpoint,
            pptx: this.fileIcons.powerpoint,
            txt: this.fileIcons.text,
            zip: this.fileIcons.archive,
            rar: this.fileIcons.archive,
            jpg: this.fileIcons.image,
            jpeg: this.fileIcons.image,
            png: this.fileIcons.image,
            gif: this.fileIcons.image,
            mp4: this.fileIcons.video,
            mp3: this.fileIcons.audio
        };
        
        return extensionMap[ext] || this.fileIcons.default;
    },
    
    /**
     * Event system
     */
    events: {},
    
    on: function(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    },
    
    off: function(event, callback) {
        if (this.events[event]) {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
        }
    },
    
    emit: function(event, data) {
        if (this.events[event]) {
            this.events[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in file upload event callback for '${event}':`, error);
                }
            });
        }
    },
    
    /**
     * Get uploader instance
     */
    getUploader: function(uploaderId) {
        return this.uploaders.get(uploaderId);
    },
    
    /**
     * Destroy uploader
     */
    destroy: function(uploaderId) {
        const uploader = this.uploaders.get(uploaderId);
        if (uploader) {
            // Cancel any ongoing uploads
            uploader.files.forEach(fileObj => {
                if (fileObj.xhr) {
                    fileObj.xhr.abort();
                }
            });
            
            this.uploaders.delete(uploaderId);
            console.log(`🗑️ Destroyed FileUploader: ${uploaderId}`);
        }
    }
};

// Export for use with CoreManager
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FileUploadManager;
}

// Make available globally
window.FileUploadManager = FileUploadManager;

// CoreManager will auto-detect and register this component