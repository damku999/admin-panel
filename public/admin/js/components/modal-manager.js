/**
 * Advanced Modal Management System
 * Provides enhanced modal functionality with stacking, animation, and dynamic content
 */

const ModalManager = {
    
    // Modal instances registry
    modals: new Map(),
    modalStack: [],
    
    // Default configuration
    defaultConfig: {
        backdrop: true,
        keyboard: true,
        focus: true,
        show: true,
        animation: true,
        size: 'default', // sm, default, lg, xl
        position: 'center', // center, top
        scrollable: false,
        closeOnBackdrop: true,
        closeOnEscape: true,
        destroyOnClose: false,
        zIndex: 1050
    },
    
    // Modal size classes
    sizeClasses: {
        sm: 'modal-sm',
        default: '',
        lg: 'modal-lg',
        xl: 'modal-xl'
    },
    
    // Initialize the modal manager
    init: function(options = {}) {
        this.config = { ...this.defaultConfig, ...options };
        
        // Set up global event handlers
        this.setupGlobalEvents();
        
        // Auto-discover existing modals
        this.discoverModals();
        
        // Inject styles
        this.injectStyles();
        
        console.log('🪟 ModalManager initialized');
    },
    
    /**
     * Discover existing modals
     */
    discoverModals: function() {
        document.querySelectorAll('.modal').forEach(modalElement => {
            this.registerModal(modalElement);
        });
    },
    
    /**
     * Register a modal
     */
    registerModal: function(modalElement, config = {}) {
        const modal = typeof modalElement === 'string' 
            ? document.getElementById(modalElement) 
            : modalElement;
            
        if (!modal) {
            console.error('Modal element not found');
            return null;
        }
        
        const modalId = modal.id || this.generateId();
        if (!modal.id) modal.id = modalId;
        
        // Skip if already registered
        if (this.modals.has(modalId)) {
            return this.modals.get(modalId);
        }
        
        const modalConfig = { ...this.defaultConfig, ...config };
        
        const modalInstance = {
            id: modalId,
            element: modal,
            config: modalConfig,
            isVisible: false,
            zIndex: modalConfig.zIndex,
            backdrop: null,
            isAnimating: false
        };
        
        // Set up modal
        this.setupModal(modalInstance);
        
        // Store instance
        this.modals.set(modalId, modalInstance);
        
        return modalInstance;
    },
    
    /**
     * Set up modal
     */
    setupModal: function(modalInstance) {
        const { element, config } = modalInstance;
        
        // Add modal classes
        element.classList.add('modal');
        if (!element.classList.contains('fade') && config.animation) {
            element.classList.add('fade');
        }
        
        // Set up modal dialog
        this.setupModalDialog(modalInstance);
        
        // Set up event handlers
        this.setupModalEvents(modalInstance);
    },
    
    /**
     * Set up modal dialog
     */
    setupModalDialog: function(modalInstance) {
        const { element, config } = modalInstance;
        
        let modalDialog = element.querySelector('.modal-dialog');
        if (!modalDialog) {
            // Create modal dialog if it doesn't exist
            modalDialog = document.createElement('div');
            modalDialog.className = 'modal-dialog';
            
            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';
            modalContent.innerHTML = element.innerHTML;
            
            modalDialog.appendChild(modalContent);
            element.innerHTML = '';
            element.appendChild(modalDialog);
        }
        
        // Apply size class
        const sizeClass = this.sizeClasses[config.size] || '';
        if (sizeClass) {
            modalDialog.classList.add(sizeClass);
        }
        
        // Apply position
        if (config.position === 'top') {
            modalDialog.classList.add('modal-dialog-centered');
        } else if (config.position === 'center') {
            modalDialog.classList.add('modal-dialog-centered');
        }
        
        // Apply scrollable
        if (config.scrollable) {
            modalDialog.classList.add('modal-dialog-scrollable');
        }
    },
    
    /**
     * Set up modal event handlers
     */
    setupModalEvents: function(modalInstance) {
        const { element, config } = modalInstance;
        
        // Close button events
        element.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.hide(modalInstance.id);
            });
        });
        
        // Backdrop click
        if (config.closeOnBackdrop) {
            element.addEventListener('click', (e) => {
                if (e.target === element) {
                    this.hide(modalInstance.id);
                }
            });
        }
        
        // Keyboard events
        if (config.keyboard) {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modalInstance.isVisible && config.closeOnEscape) {
                    this.hide(modalInstance.id);
                }
            });
        }
    },
    
    /**
     * Show modal
     */
    show: function(modalId, options = {}) {
        let modalInstance = this.modals.get(modalId);
        
        // Create modal if it doesn't exist
        if (!modalInstance) {
            modalInstance = this.createModal(modalId, options);
            if (!modalInstance) {
                console.error(`Failed to create modal: ${modalId}`);
                return false;
            }
        }
        
        // Don't show if already visible or animating
        if (modalInstance.isVisible || modalInstance.isAnimating) {
            return false;
        }
        
        modalInstance.isAnimating = true;
        
        // Merge options
        const showOptions = { ...modalInstance.config, ...options };
        
        // Set z-index for stacking
        modalInstance.zIndex = this.getNextZIndex();
        modalInstance.element.style.zIndex = modalInstance.zIndex;
        
        // Add to stack
        this.modalStack.push(modalInstance.id);
        
        // Create backdrop
        if (showOptions.backdrop) {
            this.createBackdrop(modalInstance);
        }
        
        // Show modal
        modalInstance.element.style.display = 'block';
        document.body.classList.add('modal-open');
        
        // Apply focus
        if (showOptions.focus) {
            this.setFocus(modalInstance);
        }
        
        // Animation
        if (showOptions.animation) {
            // Force reflow
            modalInstance.element.offsetHeight;
            
            modalInstance.element.classList.add('show');
            
            // Wait for animation
            setTimeout(() => {
                modalInstance.isVisible = true;
                modalInstance.isAnimating = false;
                this.emit('modal:shown', modalInstance);
            }, 300);
        } else {
            modalInstance.element.classList.add('show');
            modalInstance.isVisible = true;
            modalInstance.isAnimating = false;
            this.emit('modal:shown', modalInstance);
        }
        
        // Emit event
        this.emit('modal:show', modalInstance);
        
        return true;
    },
    
    /**
     * Hide modal
     */
    hide: function(modalId) {
        const modalInstance = this.modals.get(modalId);
        if (!modalInstance || !modalInstance.isVisible || modalInstance.isAnimating) {
            return false;
        }
        
        modalInstance.isAnimating = true;
        
        // Emit event
        this.emit('modal:hide', modalInstance);
        
        // Animation
        if (modalInstance.config.animation) {
            modalInstance.element.classList.remove('show');
            
            // Wait for animation
            setTimeout(() => {
                this.finishHide(modalInstance);
            }, 300);
        } else {
            modalInstance.element.classList.remove('show');
            this.finishHide(modalInstance);
        }
        
        return true;
    },
    
    /**
     * Finish hiding modal
     */
    finishHide: function(modalInstance) {
        modalInstance.element.style.display = 'none';
        modalInstance.isVisible = false;
        modalInstance.isAnimating = false;
        
        // Remove from stack
        const stackIndex = this.modalStack.indexOf(modalInstance.id);
        if (stackIndex > -1) {
            this.modalStack.splice(stackIndex, 1);
        }
        
        // Remove backdrop
        if (modalInstance.backdrop) {
            modalInstance.backdrop.remove();
            modalInstance.backdrop = null;
        }
        
        // Remove modal-open class if no more modals
        if (this.modalStack.length === 0) {
            document.body.classList.remove('modal-open');
        }
        
        // Destroy if configured
        if (modalInstance.config.destroyOnClose) {
            this.destroy(modalInstance.id);
        }
        
        // Emit event
        this.emit('modal:hidden', modalInstance);
    },
    
    /**
     * Toggle modal
     */
    toggle: function(modalId, options = {}) {
        const modalInstance = this.modals.get(modalId);
        if (modalInstance && modalInstance.isVisible) {
            return this.hide(modalId);
        } else {
            return this.show(modalId, options);
        }
    },
    
    /**
     * Create modal dynamically
     */
    createModal: function(modalId, options = {}) {
        const config = { ...this.defaultConfig, ...options };
        
        // Create modal element
        const modalElement = document.createElement('div');
        modalElement.id = modalId;
        modalElement.className = 'modal';
        modalElement.setAttribute('tabindex', '-1');
        modalElement.setAttribute('aria-hidden', 'true');
        
        // Create content structure
        const dialogClass = `modal-dialog ${this.sizeClasses[config.size] || ''}`;
        
        modalElement.innerHTML = `
            <div class="${dialogClass}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${options.title || 'Modal'}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ${options.body || ''}
                    </div>
                    ${options.footer !== false ? `
                    <div class="modal-footer">
                        ${options.footer || `
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        ${options.confirmButton ? options.confirmButton : ''}
                        `}
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Add to DOM
        document.body.appendChild(modalElement);
        
        // Register modal
        return this.registerModal(modalElement, config);
    },
    
    /**
     * Create backdrop
     */
    createBackdrop: function(modalInstance) {
        if (modalInstance.backdrop) return;
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade';
        backdrop.style.zIndex = modalInstance.zIndex - 1;
        
        document.body.appendChild(backdrop);
        modalInstance.backdrop = backdrop;
        
        // Force reflow and add show class
        backdrop.offsetHeight;
        backdrop.classList.add('show');
        
        // Backdrop click event
        if (modalInstance.config.closeOnBackdrop) {
            backdrop.addEventListener('click', () => {
                this.hide(modalInstance.id);
            });
        }
    },
    
    /**
     * Set focus to modal
     */
    setFocus: function(modalInstance) {
        // Focus on first focusable element
        const focusable = modalInstance.element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusable.length > 0) {
            focusable[0].focus();
        } else {
            modalInstance.element.focus();
        }
    },
    
    /**
     * Get next z-index
     */
    getNextZIndex: function() {
        let maxZ = this.defaultConfig.zIndex;
        
        this.modals.forEach(modal => {
            if (modal.isVisible && modal.zIndex >= maxZ) {
                maxZ = modal.zIndex + 10;
            }
        });
        
        return maxZ;
    },
    
    /**
     * Update modal content
     */
    updateContent: function(modalId, content) {
        const modalInstance = this.modals.get(modalId);
        if (!modalInstance) return false;
        
        const modalBody = modalInstance.element.querySelector('.modal-body');
        if (modalBody) {
            if (typeof content === 'string') {
                modalBody.innerHTML = content;
            } else if (content instanceof HTMLElement) {
                modalBody.innerHTML = '';
                modalBody.appendChild(content);
            }
        }
        
        return true;
    },
    
    /**
     * Update modal title
     */
    updateTitle: function(modalId, title) {
        const modalInstance = this.modals.get(modalId);
        if (!modalInstance) return false;
        
        const modalTitle = modalInstance.element.querySelector('.modal-title');
        if (modalTitle) {
            modalTitle.textContent = title;
        }
        
        return true;
    },
    
    /**
     * Confirmation modal
     */
    confirm: function(options = {}) {
        const modalId = 'confirm-modal-' + this.generateId();
        
        const defaultOptions = {
            title: options.title || 'Confirm Action',
            body: options.message || 'Are you sure?',
            size: 'sm',
            confirmButton: `<button type="button" class="btn btn-danger" id="${modalId}-confirm">${options.confirmText || 'Confirm'}</button>`,
            destroyOnClose: true
        };
        
        const modalInstance = this.createModal(modalId, { ...defaultOptions, ...options });
        
        return new Promise((resolve, reject) => {
            // Set up confirm button
            const confirmBtn = modalInstance.element.querySelector(`#${modalId}-confirm`);
            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    this.hide(modalId);
                    resolve(true);
                });
            }
            
            // Set up cancel handling
            this.on('modal:hidden', (modal) => {
                if (modal.id === modalId) {
                    resolve(false);
                }
            });
            
            // Show modal
            this.show(modalId);
        });
    },
    
    /**
     * Alert modal
     */
    alert: function(options = {}) {
        const modalId = 'alert-modal-' + this.generateId();
        
        const defaultOptions = {
            title: options.title || 'Alert',
            body: options.message || '',
            size: 'sm',
            footer: `<button type="button" class="btn btn-primary" data-dismiss="modal">${options.buttonText || 'OK'}</button>`,
            destroyOnClose: true
        };
        
        const modalInstance = this.createModal(modalId, { ...defaultOptions, ...options });
        
        return new Promise((resolve) => {
            this.on('modal:hidden', (modal) => {
                if (modal.id === modalId) {
                    resolve(true);
                }
            });
            
            this.show(modalId);
        });
    },
    
    /**
     * Loading modal
     */
    loading: function(options = {}) {
        const modalId = 'loading-modal-' + this.generateId();
        
        const defaultOptions = {
            title: options.title || 'Loading',
            body: `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3">${options.message || 'Please wait...'}</div>
                </div>
            `,
            size: 'sm',
            footer: false,
            keyboard: false,
            closeOnBackdrop: false,
            closeOnEscape: false
        };
        
        const modalInstance = this.createModal(modalId, { ...defaultOptions, ...options });
        this.show(modalId);
        
        return {
            modal: modalInstance,
            close: () => this.hide(modalId),
            updateMessage: (message) => {
                const messageEl = modalInstance.element.querySelector('.modal-body .mt-3');
                if (messageEl) messageEl.textContent = message;
            }
        };
    },
    
    /**
     * Set up global events
     */
    setupGlobalEvents: function() {
        // Handle browser back button
        window.addEventListener('popstate', () => {
            if (this.modalStack.length > 0) {
                const topModalId = this.modalStack[this.modalStack.length - 1];
                this.hide(topModalId);
            }
        });
        
        // Handle page unload
        window.addEventListener('beforeunload', () => {
            this.hideAll();
        });
    },
    
    /**
     * Hide all modals
     */
    hideAll: function() {
        // Hide modals in reverse order (top to bottom)
        const stackCopy = [...this.modalStack].reverse();
        stackCopy.forEach(modalId => {
            this.hide(modalId);
        });
    },
    
    /**
     * Destroy modal
     */
    destroy: function(modalId) {
        const modalInstance = this.modals.get(modalId);
        if (!modalInstance) return false;
        
        // Hide first if visible
        if (modalInstance.isVisible) {
            this.hide(modalId);
        }
        
        // Remove from DOM
        if (modalInstance.element.parentNode) {
            modalInstance.element.parentNode.removeChild(modalInstance.element);
        }
        
        // Remove backdrop if exists
        if (modalInstance.backdrop) {
            modalInstance.backdrop.remove();
        }
        
        // Remove from registry
        this.modals.delete(modalId);
        
        this.emit('modal:destroyed', modalInstance);
        return true;
    },
    
    /**
     * Inject modal styles
     */
    injectStyles: function() {
        if (document.getElementById('modal-manager-styles')) {
            return;
        }
        
        const styles = document.createElement('style');
        styles.id = 'modal-manager-styles';
        styles.textContent = `
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1040;
                width: 100vw;
                height: 100vh;
                background-color: #000;
            }
            
            .modal-backdrop.fade {
                opacity: 0;
            }
            
            .modal-backdrop.show {
                opacity: 0.5;
            }
            
            .modal {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1050;
                width: 100%;
                height: 100%;
                overflow-x: hidden;
                overflow-y: auto;
                outline: 0;
            }
            
            .modal.fade .modal-dialog {
                transition: transform 0.3s ease-out;
                transform: translate(0, -50px);
            }
            
            .modal.show .modal-dialog {
                transform: none;
            }
            
            .modal-open {
                overflow: hidden;
            }
            
            .modal-dialog {
                position: relative;
                width: auto;
                margin: 1.75rem;
                pointer-events: none;
            }
            
            .modal-dialog-centered {
                display: flex;
                align-items: center;
                min-height: calc(100% - 3.5rem);
            }
            
            @media (min-width: 576px) {
                .modal-dialog {
                    max-width: 500px;
                    margin: 1.75rem auto;
                }
                
                .modal-dialog-centered {
                    min-height: calc(100% - 3.5rem);
                }
                
                .modal-sm {
                    max-width: 300px;
                }
            }
            
            @media (min-width: 992px) {
                .modal-lg {
                    max-width: 800px;
                }
                
                .modal-xl {
                    max-width: 1140px;
                }
            }
            
            .modal-content {
                position: relative;
                display: flex;
                flex-direction: column;
                width: 100%;
                pointer-events: auto;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid rgba(0, 0, 0, 0.2);
                border-radius: 0.3rem;
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.5);
                outline: 0;
            }
            
            .modal-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 1rem 1rem;
                border-bottom: 1px solid #dee2e6;
                border-top-left-radius: calc(0.3rem - 1px);
                border-top-right-radius: calc(0.3rem - 1px);
            }
            
            .modal-title {
                margin-bottom: 0;
                line-height: 1.5;
                font-size: 1.25rem;
            }
            
            .modal-body {
                position: relative;
                flex: 1 1 auto;
                padding: 1rem;
            }
            
            .modal-footer {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                padding: 0.75rem;
                border-top: 1px solid #dee2e6;
                border-bottom-right-radius: calc(0.3rem - 1px);
                border-bottom-left-radius: calc(0.3rem - 1px);
            }
            
            .modal-footer > * {
                margin: 0.25rem;
            }
        `;
        
        document.head.appendChild(styles);
    },
    
    /**
     * Utility functions
     */
    generateId: function() {
        return 'modal_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
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
                    console.error(`Error in modal event callback for '${event}':`, error);
                }
            });
        }
    },
    
    /**
     * Get modal instance
     */
    getModal: function(modalId) {
        return this.modals.get(modalId);
    },
    
    /**
     * Get all modals
     */
    getAllModals: function() {
        return Array.from(this.modals.values());
    },
    
    /**
     * Check if modal is visible
     */
    isVisible: function(modalId) {
        const modal = this.modals.get(modalId);
        return modal ? modal.isVisible : false;
    }
};

// Backward compatibility with existing modal functions
if (typeof showModal === 'undefined') {
    window.showModal = function(modalId, options) {
        return ModalManager.show(modalId, options);
    };
}

if (typeof hideModal === 'undefined') {
    window.hideModal = function(modalId) {
        return ModalManager.hide(modalId);
    };
}

// Export for use with CoreManager
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalManager;
}

// Make available globally
window.ModalManager = ModalManager;

// CoreManager will auto-detect and register this component