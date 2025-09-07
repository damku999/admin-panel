/**
 * General Helper Utilities for Admin Panel
 * Provides common utility functions across all modules
 */

const Helpers = {
    
    /**
     * Initialize Helpers utility
     */
    init: function() {
        console.log('✅ Helpers utility initialized');
        return this;
    },
    
    /**
     * Generate random ID
     */
    generateId: function(prefix = 'id', length = 8) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return `${prefix}_${result}`;
    },
    
    /**
     * Debounce function calls
     */
    debounce: function(func, wait, immediate = false) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    },
    
    /**
     * Throttle function calls
     */
    throttle: function(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    /**
     * Deep clone object
     */
    deepClone: function(obj) {
        if (obj === null || typeof obj !== 'object') return obj;
        if (obj instanceof Date) return new Date(obj.getTime());
        if (obj instanceof Array) return obj.map(item => this.deepClone(item));
        
        const cloned = {};
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
                cloned[key] = this.deepClone(obj[key]);
            }
        }
        return cloned;
    },
    
    /**
     * Deep merge objects
     */
    deepMerge: function(target, ...sources) {
        if (!sources.length) return target;
        const source = sources.shift();
        
        if (this.isObject(target) && this.isObject(source)) {
            for (const key in source) {
                if (this.isObject(source[key])) {
                    if (!target[key]) Object.assign(target, { [key]: {} });
                    this.deepMerge(target[key], source[key]);
                } else {
                    Object.assign(target, { [key]: source[key] });
                }
            }
        }
        
        return this.deepMerge(target, ...sources);
    },
    
    /**
     * Check if value is object
     */
    isObject: function(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    },
    
    /**
     * Get nested object value safely
     */
    getValue: function(obj, path, defaultValue = null) {
        const keys = path.split('.');
        let result = obj;
        
        for (let key of keys) {
            if (result == null || typeof result !== 'object') {
                return defaultValue;
            }
            result = result[key];
        }
        
        return result !== undefined ? result : defaultValue;
    },
    
    /**
     * Set nested object value
     */
    setValue: function(obj, path, value) {
        const keys = path.split('.');
        const lastKey = keys.pop();
        let current = obj;
        
        for (let key of keys) {
            if (!(key in current) || typeof current[key] !== 'object') {
                current[key] = {};
            }
            current = current[key];
        }
        
        current[lastKey] = value;
        return obj;
    },
    
    /**
     * Convert form data to object
     */
    formToObject: function(form) {
        const formData = new FormData(form);
        const obj = {};
        
        for (let [key, value] of formData.entries()) {
            if (obj[key]) {
                if (Array.isArray(obj[key])) {
                    obj[key].push(value);
                } else {
                    obj[key] = [obj[key], value];
                }
            } else {
                obj[key] = value;
            }
        }
        
        return obj;
    },
    
    /**
     * Convert object to query string
     */
    objectToQueryString: function(obj) {
        const params = new URLSearchParams();
        
        Object.keys(obj).forEach(key => {
            if (Array.isArray(obj[key])) {
                obj[key].forEach(value => params.append(key, value));
            } else if (obj[key] !== null && obj[key] !== undefined) {
                params.append(key, obj[key]);
            }
        });
        
        return params.toString();
    },
    
    /**
     * Parse query string to object
     */
    queryStringToObject: function(queryString) {
        const params = new URLSearchParams(queryString);
        const obj = {};
        
        for (let [key, value] of params.entries()) {
            if (obj[key]) {
                if (Array.isArray(obj[key])) {
                    obj[key].push(value);
                } else {
                    obj[key] = [obj[key], value];
                }
            } else {
                obj[key] = value;
            }
        }
        
        return obj;
    },
    
    /**
     * Scroll to element smoothly
     */
    scrollToElement: function(element, options = {}) {
        const target = typeof element === 'string' ? document.querySelector(element) : element;
        if (!target) return;
        
        const offsetTop = options.offset || 0;
        const behavior = options.behavior || 'smooth';
        
        window.scrollTo({
            top: target.offsetTop - offsetTop,
            behavior: behavior
        });
    },
    
    /**
     * Copy text to clipboard
     */
    copyToClipboard: function(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            return new Promise((resolve, reject) => {
                try {
                    document.execCommand('copy');
                    textArea.remove();
                    resolve();
                } catch (err) {
                    textArea.remove();
                    reject(err);
                }
            });
        }
    },
    
    /**
     * Download data as file
     */
    downloadFile: function(data, filename, type = 'text/plain') {
        const blob = new Blob([data], { type });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    },
    
    /**
     * Check if device is mobile
     */
    isMobile: function() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },
    
    /**
     * Get viewport dimensions
     */
    getViewport: function() {
        return {
            width: Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0),
            height: Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0)
        };
    },
    
    /**
     * Check if element is in viewport
     */
    isInViewport: function(element) {
        const rect = element.getBoundingClientRect();
        const viewport = this.getViewport();
        
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= viewport.height &&
            rect.right <= viewport.width
        );
    },
    
    /**
     * Local storage with expiration
     */
    storage: {
        set: function(key, value, expirationMinutes = null) {
            const item = {
                value: value,
                timestamp: new Date().getTime()
            };
            
            if (expirationMinutes) {
                item.expiration = new Date().getTime() + (expirationMinutes * 60 * 1000);
            }
            
            localStorage.setItem(key, JSON.stringify(item));
        },
        
        get: function(key) {
            const itemStr = localStorage.getItem(key);
            if (!itemStr) return null;
            
            const item = JSON.parse(itemStr);
            const now = new Date().getTime();
            
            if (item.expiration && now > item.expiration) {
                localStorage.removeItem(key);
                return null;
            }
            
            return item.value;
        },
        
        remove: function(key) {
            localStorage.removeItem(key);
        },
        
        clear: function() {
            localStorage.clear();
        }
    },
    
    /**
     * Simple event emitter
     */
    EventEmitter: function() {
        this.events = {};
        
        this.on = function(event, callback) {
            if (!this.events[event]) {
                this.events[event] = [];
            }
            this.events[event].push(callback);
        };
        
        this.emit = function(event, ...args) {
            if (this.events[event]) {
                this.events[event].forEach(callback => callback(...args));
            }
        };
        
        this.off = function(event, callback) {
            if (this.events[event]) {
                this.events[event] = this.events[event].filter(cb => cb !== callback);
            }
        };
    },
    
    /**
     * Simple cache implementation
     */
    Cache: function(maxSize = 100, ttl = 300000) { // 5 minutes default TTL
        this.cache = new Map();
        this.maxSize = maxSize;
        this.ttl = ttl;
        
        this.set = function(key, value) {
            const now = Date.now();
            
            // Remove expired entries
            this.cleanup();
            
            // Remove oldest entry if cache is full
            if (this.cache.size >= this.maxSize) {
                const firstKey = this.cache.keys().next().value;
                this.cache.delete(firstKey);
            }
            
            this.cache.set(key, {
                value: value,
                timestamp: now,
                expiry: now + this.ttl
            });
        };
        
        this.get = function(key) {
            const entry = this.cache.get(key);
            if (!entry) return null;
            
            if (Date.now() > entry.expiry) {
                this.cache.delete(key);
                return null;
            }
            
            return entry.value;
        };
        
        this.has = function(key) {
            return this.get(key) !== null;
        };
        
        this.delete = function(key) {
            this.cache.delete(key);
        };
        
        this.clear = function() {
            this.cache.clear();
        };
        
        this.cleanup = function() {
            const now = Date.now();
            for (let [key, entry] of this.cache.entries()) {
                if (now > entry.expiry) {
                    this.cache.delete(key);
                }
            }
        };
    },
    
    /**
     * Format bytes to human readable format
     */
    formatBytes: function(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },
    
    /**
     * Simple template engine
     */
    template: function(str, data) {
        return str.replace(/\{\{(\w+)\}\}/g, (match, key) => {
            return data[key] !== undefined ? data[key] : match;
        });
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Helpers;
}

// Make available globally
window.Helpers = Helpers;