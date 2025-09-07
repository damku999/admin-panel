/**
 * Validation Utilities for Admin Panel
 * Provides common validation functions across all modules
 */

const Validators = {
    
    /**
     * Initialize Validators utility
     */
    init: function() {
        console.log('✅ Validators utility initialized');
        return this;
    },
    
    /**
     * Validate email format
     */
    email: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    /**
     * Validate Indian mobile number
     */
    mobileNumber: function(mobile) {
        const mobileRegex = /^[6-9]\d{9}$/;
        return mobileRegex.test(mobile.replace(/\D/g, ''));
    },
    
    /**
     * Validate PAN card format
     */
    panCard: function(pan) {
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        return panRegex.test(pan.toUpperCase());
    },
    
    /**
     * Validate Aadhaar number format
     */
    aadhaarNumber: function(aadhaar) {
        const aadhaarRegex = /^\d{12}$/;
        return aadhaarRegex.test(aadhaar.replace(/\s/g, ''));
    },
    
    /**
     * Validate GST number format
     */
    gstNumber: function(gst) {
        const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        return gstRegex.test(gst.toUpperCase());
    },
    
    /**
     * Validate vehicle registration number
     */
    vehicleNumber: function(vehicle) {
        const vehicleRegex = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
        return vehicleRegex.test(vehicle.toUpperCase().replace(/\s/g, ''));
    },
    
    /**
     * Validate Indian postal code (PIN)
     */
    postalCode: function(pin) {
        const pinRegex = /^[1-9][0-9]{5}$/;
        return pinRegex.test(pin);
    },
    
    /**
     * Validate driving license number
     */
    drivingLicense: function(dl) {
        const dlRegex = /^[A-Z]{2}\d{13}$/;
        return dlRegex.test(dl.toUpperCase().replace(/\s/g, ''));
    },
    
    /**
     * Validate policy number format
     */
    policyNumber: function(policy) {
        // Generic policy number validation (alphanumeric, 6-20 characters)
        const policyRegex = /^[A-Z0-9]{6,20}$/;
        return policyRegex.test(policy.toUpperCase().replace(/\s/g, ''));
    },
    
    /**
     * Validate date format and range
     */
    date: function(dateString, options = {}) {
        if (!dateString) return false;
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return false;
        
        const now = new Date();
        
        if (options.future && date <= now) {
            return false;
        }
        
        if (options.past && date >= now) {
            return false;
        }
        
        if (options.minAge) {
            const minDate = new Date();
            minDate.setFullYear(minDate.getFullYear() - options.minAge);
            if (date > minDate) return false;
        }
        
        if (options.maxAge) {
            const maxDate = new Date();
            maxDate.setFullYear(maxDate.getFullYear() - options.maxAge);
            if (date < maxDate) return false;
        }
        
        return true;
    },
    
    /**
     * Validate numeric value with range
     */
    numeric: function(value, options = {}) {
        const num = parseFloat(value);
        if (isNaN(num)) return false;
        
        if (options.min !== undefined && num < options.min) return false;
        if (options.max !== undefined && num > options.max) return false;
        if (options.integer && !Number.isInteger(num)) return false;
        if (options.positive && num <= 0) return false;
        
        return true;
    },
    
    /**
     * Validate text length and content
     */
    text: function(text, options = {}) {
        if (typeof text !== 'string') return false;
        
        const length = text.trim().length;
        
        if (options.minLength && length < options.minLength) return false;
        if (options.maxLength && length > options.maxLength) return false;
        if (options.required && length === 0) return false;
        if (options.alphaOnly && !/^[A-Za-z\s]+$/.test(text)) return false;
        if (options.alphanumeric && !/^[A-Za-z0-9\s]+$/.test(text)) return false;
        
        return true;
    },
    
    /**
     * Validate password strength
     */
    password: function(password, options = {}) {
        if (!password) return false;
        
        const minLength = options.minLength || 8;
        const requireUppercase = options.uppercase !== false;
        const requireLowercase = options.lowercase !== false;
        const requireNumbers = options.numbers !== false;
        const requireSpecial = options.special !== false;
        
        if (password.length < minLength) return false;
        
        if (requireUppercase && !/[A-Z]/.test(password)) return false;
        if (requireLowercase && !/[a-z]/.test(password)) return false;
        if (requireNumbers && !/\d/.test(password)) return false;
        if (requireSpecial && !/[!@#$%^&*(),.?":{}|<>]/.test(password)) return false;
        
        return true;
    },
    
    /**
     * Validate file upload
     */
    file: function(file, options = {}) {
        if (!file) return options.required === false;
        
        const allowedTypes = options.types || [];
        const maxSize = options.maxSize || 10; // MB
        const minSize = options.minSize || 0;
        
        if (allowedTypes.length > 0) {
            const fileType = file.type.toLowerCase();
            const fileName = file.name.toLowerCase();
            
            const isAllowed = allowedTypes.some(type => {
                if (type.startsWith('.')) {
                    return fileName.endsWith(type);
                }
                return fileType.includes(type);
            });
            
            if (!isAllowed) return false;
        }
        
        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > maxSize || fileSizeMB < minSize) return false;
        
        return true;
    },
    
    /**
     * Validate form data against rules
     */
    validateForm: function(formData, rules) {
        const errors = {};
        
        Object.keys(rules).forEach(field => {
            const rule = rules[field];
            const value = formData[field];
            
            if (rule.required && (!value || (typeof value === 'string' && value.trim() === ''))) {
                errors[field] = rule.messages?.required || `${field} is required`;
                return;
            }
            
            if (!value && !rule.required) return;
            
            if (rule.type && this[rule.type]) {
                if (!this[rule.type](value, rule.options)) {
                    errors[field] = rule.messages?.invalid || `${field} is invalid`;
                }
            }
        });
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors: errors
        };
    },
    
    /**
     * Real-time validation for form fields
     */
    setupRealTimeValidation: function(form, rules) {
        const formElement = typeof form === 'string' ? document.getElementById(form) : form;
        if (!formElement) return;
        
        Object.keys(rules).forEach(field => {
            const fieldElement = formElement.querySelector(`[name="${field}"]`);
            if (!fieldElement) return;
            
            const rule = rules[field];
            
            fieldElement.addEventListener('blur', () => {
                this.validateField(field, fieldElement.value, rule, fieldElement);
            });
            
            if (rule.realTime) {
                fieldElement.addEventListener('input', () => {
                    this.validateField(field, fieldElement.value, rule, fieldElement);
                });
            }
        });
    },
    
    /**
     * Validate single field and show feedback
     */
    validateField: function(fieldName, value, rule, fieldElement) {
        const isValid = this.validateForm({ [fieldName]: value }, { [fieldName]: rule });
        
        fieldElement.classList.remove('is-valid', 'is-invalid');
        
        const feedback = fieldElement.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (feedback) feedback.remove();
        
        if (!isValid.isValid) {
            fieldElement.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = isValid.errors[fieldName];
            fieldElement.parentNode.appendChild(errorDiv);
        } else if (rule.showValid !== false) {
            fieldElement.classList.add('is-valid');
        }
        
        return isValid.isValid;
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Validators;
}

// Make available globally
window.Validators = Validators;