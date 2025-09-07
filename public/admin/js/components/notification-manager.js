/**
 * Advanced Notification and Alert Management System
 * Provides unified notification, toast, and alert functionality
 */

const NotificationManager = {
    
    // Notification queue and instances
    notifications: new Map(),
    queue: [],
    
    // Default configuration
    config: {
        position: 'top-right',
        autoClose: true,
        closeDelay: 5000,
        maxNotifications: 5,
        showProgress: true,
        enableSound: false,
        animations: true,
        rtl: false
    },
    
    // Notification types with default styling
    types: {
        success: {
            icon: 'fas fa-check-circle',
            bgColor: '#28a745',
            textColor: '#ffffff',
            sound: 'success.mp3'
        },
        error: {
            icon: 'fas fa-exclamation-circle',
            bgColor: '#dc3545',
            textColor: '#ffffff',
            sound: 'error.mp3'
        },
        warning: {
            icon: 'fas fa-exclamation-triangle',
            bgColor: '#ffc107',
            textColor: '#212529',
            sound: 'warning.mp3'
        },
        info: {
            icon: 'fas fa-info-circle',
            bgColor: '#17a2b8',
            textColor: '#ffffff',
            sound: 'info.mp3'
        }
    },
    
    // Initialize the notification system
    init: function(options = {}) {
        this.config = { ...this.config, ...options };
        
        // Create notification container
        this.createContainer();
        
        // Set up global styles
        this.injectStyles();
        
        // Set up keyboard shortcuts
        this.setupKeyboardShortcuts();
        
        // Set up service worker for persistent notifications (if available)
        if ('serviceWorker' in navigator && 'Notification' in window) {
            this.setupServiceWorkerNotifications();
        }
        
        console.log('🔔 NotificationManager initialized');
    },
    
    /**
     * Create notification container
     */
    createContainer: function() {
        if (document.getElementById('notification-container')) {
            return;
        }
        
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = `notification-container ${this.config.position}`;
        
        if (this.config.rtl) {
            container.classList.add('rtl');
        }
        
        document.body.appendChild(container);
    },
    
    /**
     * Show notification
     */
    show: function(type, message, options = {}) {
        const notificationOptions = { ...this.config, ...options };
        const typeConfig = this.types[type] || this.types.info;
        
        const notification = {
            id: this.generateId(),
            type: type,
            message: message,
            options: notificationOptions,
            typeConfig: typeConfig,
            createdAt: new Date(),
            element: null
        };
        
        // Check notification limits
        if (this.notifications.size >= this.config.maxNotifications) {
            this.removeOldest();
        }
        
        // Create notification element
        this.createNotificationElement(notification);
        
        // Store notification
        this.notifications.set(notification.id, notification);
        
        // Auto-close if enabled
        if (notificationOptions.autoClose && notificationOptions.closeDelay > 0) {
            setTimeout(() => {
                this.hide(notification.id);
            }, notificationOptions.closeDelay);
        }
        
        // Play sound if enabled
        if (this.config.enableSound && typeConfig.sound) {
            this.playSound(typeConfig.sound);
        }
        
        // Trigger event
        this.emit('notification:shown', notification);
        
        return notification.id;
    },
    
    /**
     * Create notification DOM element
     */
    createNotificationElement: function(notification) {
        const { typeConfig, message, options } = notification;
        
        const element = document.createElement('div');
        element.className = 'notification-item';
        element.dataset.id = notification.id;
        element.dataset.type = notification.type;
        
        // Build notification HTML
        let html = `
            <div class="notification-content" style="background-color: ${typeConfig.bgColor}; color: ${typeConfig.textColor};">
                <div class="notification-header">
                    <i class="${typeConfig.icon} notification-icon"></i>
                    <span class="notification-title">${this.getTypeTitle(notification.type)}</span>
                    <button type="button" class="notification-close" data-dismiss="notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="notification-body">
                    ${message}
                </div>
        `;
        
        // Add progress bar if enabled
        if (options.showProgress && options.autoClose) {
            html += `
                <div class="notification-progress">
                    <div class="notification-progress-bar" style="animation-duration: ${options.closeDelay}ms;"></div>
                </div>
            `;
        }
        
        // Add actions if provided
        if (options.actions && Array.isArray(options.actions)) {
            html += '<div class="notification-actions">';
            options.actions.forEach(action => {
                html += `
                    <button type="button" class="notification-action btn btn-sm" 
                            data-action="${action.name}">
                        ${action.icon ? `<i class="${action.icon}"></i> ` : ''}
                        ${action.label}
                    </button>
                `;
            });
            html += '</div>';
        }
        
        html += '</div>';
        element.innerHTML = html;
        
        // Set up event handlers
        this.setupNotificationEvents(element, notification);
        
        // Add to container with animation
        const container = document.getElementById('notification-container');
        if (this.config.animations) {
            element.style.opacity = '0';
            element.style.transform = 'translateX(100%)';
            container.appendChild(element);
            
            // Animate in
            setTimeout(() => {
                element.style.transition = 'all 0.3s ease-in-out';
                element.style.opacity = '1';
                element.style.transform = 'translateX(0)';
            }, 10);
        } else {
            container.appendChild(element);
        }
        
        notification.element = element;
    },
    
    /**
     * Set up notification event handlers
     */
    setupNotificationEvents: function(element, notification) {
        // Close button
        element.querySelector('.notification-close').addEventListener('click', () => {
            this.hide(notification.id);
        });
        
        // Action buttons
        element.querySelectorAll('.notification-action').forEach(button => {
            button.addEventListener('click', (e) => {
                const actionName = e.target.dataset.action;
                const action = notification.options.actions?.find(a => a.name === actionName);
                
                if (action && action.callback) {
                    action.callback(notification);
                }
                
                // Auto-close after action unless specified otherwise
                if (action && action.closeAfterClick !== false) {
                    this.hide(notification.id);
                }
            });
        });
        
        // Click to dismiss (if enabled)
        if (notification.options.clickToDismiss !== false) {
            element.addEventListener('click', (e) => {
                if (!e.target.closest('.notification-action') && !e.target.closest('.notification-close')) {
                    this.hide(notification.id);
                }
            });
        }
    },
    
    /**
     * Hide notification
     */
    hide: function(id) {
        const notification = this.notifications.get(id);
        if (!notification || !notification.element) {
            return false;
        }
        
        const element = notification.element;
        
        if (this.config.animations) {
            element.style.transition = 'all 0.3s ease-in-out';
            element.style.opacity = '0';
            element.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                if (element.parentNode) {
                    element.parentNode.removeChild(element);
                }
                this.notifications.delete(id);
                this.emit('notification:hidden', notification);
            }, 300);
        } else {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.notifications.delete(id);
            this.emit('notification:hidden', notification);
        }
        
        return true;
    },
    
    /**
     * Hide all notifications
     */
    hideAll: function() {
        this.notifications.forEach((notification, id) => {
            this.hide(id);
        });
    },
    
    /**
     * Update notification
     */
    update: function(id, message, options = {}) {
        const notification = this.notifications.get(id);
        if (!notification) {
            return false;
        }
        
        // Update message
        if (message) {
            const bodyElement = notification.element.querySelector('.notification-body');
            if (bodyElement) {
                bodyElement.innerHTML = message;
            }
        }
        
        // Update options
        Object.assign(notification.options, options);
        
        return true;
    },
    
    /**
     * Show success notification
     */
    success: function(message, options = {}) {
        return this.show('success', message, options);
    },
    
    /**
     * Show error notification
     */
    error: function(message, options = {}) {
        return this.show('error', message, { ...options, autoClose: false });
    },
    
    /**
     * Show warning notification
     */
    warning: function(message, options = {}) {
        return this.show('warning', message, options);
    },
    
    /**
     * Show info notification
     */
    info: function(message, options = {}) {
        return this.show('info', message, options);
    },
    
    /**
     * Show confirmation dialog
     */
    confirm: function(message, options = {}) {
        const defaultOptions = {
            autoClose: false,
            actions: [
                {
                    name: 'confirm',
                    label: 'Yes',
                    icon: 'fas fa-check',
                    callback: options.onConfirm || (() => {}),
                    closeAfterClick: true
                },
                {
                    name: 'cancel',
                    label: 'Cancel',
                    icon: 'fas fa-times',
                    callback: options.onCancel || (() => {}),
                    closeAfterClick: true
                }
            ]
        };
        
        return this.show('warning', message, { ...defaultOptions, ...options });
    },
    
    /**
     * Show loading notification
     */
    loading: function(message, options = {}) {
        const defaultOptions = {
            autoClose: false,
            showProgress: false,
            clickToDismiss: false
        };
        
        const loadingMessage = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm mr-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                ${message}
            </div>
        `;
        
        return this.show('info', loadingMessage, { ...defaultOptions, ...options });
    },
    
    /**
     * Show progress notification
     */
    progress: function(message, progress = 0, options = {}) {
        const progressMessage = `
            <div class="mb-2">${message}</div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar" role="progressbar" 
                     style="width: ${progress}%" 
                     aria-valuenow="${progress}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
        `;
        
        const defaultOptions = {
            autoClose: false,
            showProgress: false
        };
        
        return this.show('info', progressMessage, { ...defaultOptions, ...options });
    },
    
    /**
     * Update progress notification
     */
    updateProgress: function(id, progress, message = null) {
        const notification = this.notifications.get(id);
        if (!notification) return false;
        
        const progressBar = notification.element.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
        }
        
        if (message) {
            const messageElement = notification.element.querySelector('.notification-body > div:first-child');
            if (messageElement) {
                messageElement.textContent = message;
            }
        }
        
        return true;
    },
    
    /**
     * Inject notification styles
     */
    injectStyles: function() {
        if (document.getElementById('notification-styles')) {
            return;
        }
        
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification-container {
                position: fixed;
                z-index: 9999;
                pointer-events: none;
            }
            
            .notification-container.top-right {
                top: 20px;
                right: 20px;
            }
            
            .notification-container.top-left {
                top: 20px;
                left: 20px;
            }
            
            .notification-container.bottom-right {
                bottom: 20px;
                right: 20px;
            }
            
            .notification-container.bottom-left {
                bottom: 20px;
                left: 20px;
            }
            
            .notification-item {
                pointer-events: auto;
                margin-bottom: 10px;
                min-width: 300px;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                border-radius: 6px;
                overflow: hidden;
                position: relative;
            }
            
            .notification-content {
                padding: 15px;
                position: relative;
            }
            
            .notification-header {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
            }
            
            .notification-icon {
                margin-right: 8px;
                font-size: 16px;
            }
            
            .notification-title {
                font-weight: 600;
                flex-grow: 1;
            }
            
            .notification-close {
                background: none;
                border: none;
                color: inherit;
                cursor: pointer;
                padding: 0;
                font-size: 14px;
                opacity: 0.7;
                transition: opacity 0.2s;
            }
            
            .notification-close:hover {
                opacity: 1;
            }
            
            .notification-body {
                font-size: 14px;
                line-height: 1.4;
            }
            
            .notification-actions {
                margin-top: 10px;
                display: flex;
                gap: 8px;
            }
            
            .notification-action {
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: inherit;
                padding: 4px 8px;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            
            .notification-action:hover {
                background: rgba(255, 255, 255, 0.3);
            }
            
            .notification-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
                overflow: hidden;
            }
            
            .notification-progress-bar {
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                width: 100%;
                animation: notification-progress linear;
                transform-origin: left;
                transform: scaleX(0);
            }
            
            @keyframes notification-progress {
                from {
                    transform: scaleX(1);
                }
                to {
                    transform: scaleX(0);
                }
            }
            
            @media (max-width: 480px) {
                .notification-container {
                    left: 10px !important;
                    right: 10px !important;
                }
                
                .notification-item {
                    min-width: auto;
                    max-width: none;
                }
            }
        `;
        
        document.head.appendChild(styles);
    },
    
    /**
     * Set up keyboard shortcuts
     */
    setupKeyboardShortcuts: function() {
        document.addEventListener('keydown', (e) => {
            // Escape key to close all notifications
            if (e.key === 'Escape' && e.ctrlKey) {
                this.hideAll();
                e.preventDefault();
            }
        });
    },
    
    /**
     * Set up service worker notifications
     */
    setupServiceWorkerNotifications: function() {
        // Check if browser supports Service Workers and Notifications
        if (!('serviceWorker' in navigator) || !('Notification' in window)) {
            console.log('🔕 Service Worker notifications not supported');
            return;
        }
        
        // Request notification permission if not already granted
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('✅ Notification permission granted');
                } else {
                    console.log('❌ Notification permission denied');
                }
            });
        }
        
        console.log('🔔 Service Worker notifications setup complete');
    },
    
    /**
     * Utility functions
     */
    generateId: function() {
        return 'notification_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    },
    
    getTypeTitle: function(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || 'Notification';
    },
    
    removeOldest: function() {
        let oldest = null;
        let oldestTime = Date.now();
        
        this.notifications.forEach((notification) => {
            if (notification.createdAt.getTime() < oldestTime) {
                oldestTime = notification.createdAt.getTime();
                oldest = notification;
            }
        });
        
        if (oldest) {
            this.hide(oldest.id);
        }
    },
    
    playSound: function(soundFile) {
        if (this.config.enableSound && 'Audio' in window) {
            try {
                const audio = new Audio(`/admin/sounds/${soundFile}`);
                audio.volume = 0.3;
                audio.play().catch(() => {
                    // Ignore audio play errors
                });
            } catch (error) {
                console.warn('Failed to play notification sound:', error);
            }
        }
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
                    console.error(`Error in notification event callback for '${event}':`, error);
                }
            });
        }
    }
};

// Backward compatibility with existing show_notification function
if (typeof show_notification === 'undefined') {
    window.show_notification = function(type, message, options) {
        return NotificationManager.show(type, message, options);
    };
}

// Export for use with CoreManager
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationManager;
}

// Make available globally
window.NotificationManager = NotificationManager;

// CoreManager will auto-detect and register this component