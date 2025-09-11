/**
 * Form Validation Utility Library
 * Reusable client-side validation for admin panel forms
 */

class FormValidator {
    constructor(formSelector = 'form') {
        this.form = document.querySelector(formSelector);
        this.errorClass = 'is-invalid';
        this.errorMessageClass = 'invalid-feedback';
        this.validationRules = {};
        this.customMessages = {};
        this.isValid = true;
        this.firstErrorField = null;
        
        if (this.form) {
            this.init();
        }
    }

    init() {
        // Bind form submit event
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    /**
     * Add validation rule for a field
     * @param {string} fieldName - Name attribute of the field
     * @param {object} rules - Validation rules
     * @param {string} displayName - Human readable field name
     */
    addRule(fieldName, rules, displayName = null) {
        this.validationRules[fieldName] = {
            ...rules,
            displayName: displayName || this.formatFieldName(fieldName)
        };
        return this;
    }

    /**
     * Add multiple validation rules at once
     * @param {object} rules - Object containing field rules
     */
    addRules(rules) {
        Object.keys(rules).forEach(fieldName => {
            const rule = rules[fieldName];
            this.addRule(fieldName, rule.rules || rule, rule.displayName);
        });
        return this;
    }

    /**
     * Set custom error message for a field
     * @param {string} fieldName - Name attribute of the field
     * @param {string} message - Custom error message
     */
    setCustomMessage(fieldName, message) {
        this.customMessages[fieldName] = message;
        return this;
    }

    /**
     * Validate the entire form
     * @returns {boolean} - Whether form is valid
     */
    validateForm() {
        this.clearValidationErrors();
        this.isValid = true;
        this.firstErrorField = null;

        // Validate each field with rules
        Object.keys(this.validationRules).forEach(fieldName => {
            this.validateField(fieldName);
        });

        // Show error summary and focus first error field
        if (!this.isValid) {
            this.handleValidationErrors();
        }

        return this.isValid;
    }

    /**
     * Validate a specific field
     * @param {string} fieldName - Name attribute of the field
     * @returns {boolean} - Whether field is valid
     */
    validateField(fieldName) {
        const field = this.getField(fieldName);
        const rules = this.validationRules[fieldName];
        
        if (!field || !rules) return true;

        const value = this.getFieldValue(field);
        let fieldValid = true;

        // Required validation
        if (rules.required && (!value || value.trim() === '')) {
            this.addFieldError(field, `The ${rules.displayName} field is required.`);
            fieldValid = false;
        }

        // Skip other validations if field is empty and not required
        if (!rules.required && (!value || value.trim() === '')) {
            return fieldValid;
        }

        // Numeric validation
        if (rules.numeric && value) {
            const numValue = parseFloat(value);
            if (isNaN(numValue)) {
                this.addFieldError(field, `The ${rules.displayName} must be a valid number.`);
                fieldValid = false;
            } else {
                // Min value validation
                if (rules.min !== undefined && numValue < rules.min) {
                    this.addFieldError(field, `The ${rules.displayName} must be at least ${rules.min}.`);
                    fieldValid = false;
                }
                // Max value validation
                if (rules.max !== undefined && numValue > rules.max) {
                    this.addFieldError(field, `The ${rules.displayName} may not be greater than ${rules.max}.`);
                    fieldValid = false;
                }
            }
        }

        // Email validation
        if (rules.email && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.addFieldError(field, `The ${rules.displayName} must be a valid email address.`);
                fieldValid = false;
            }
        }

        // Phone validation
        if (rules.phone && value) {
            const phoneRegex = /^[0-9+\-\s()]{10,15}$/;
            if (!phoneRegex.test(value)) {
                this.addFieldError(field, `The ${rules.displayName} must be a valid phone number.`);
                fieldValid = false;
            }
        }

        // Date validation
        if (rules.date && value) {
            const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
            if (!dateRegex.test(value)) {
                this.addFieldError(field, `The ${rules.displayName} must be a valid date (dd/mm/yyyy).`);
                fieldValid = false;
            }
        }

        // Custom regex pattern validation
        if (rules.pattern && value) {
            const regex = new RegExp(rules.pattern);
            if (!regex.test(value)) {
                const message = rules.patternMessage || `The ${rules.displayName} format is invalid.`;
                this.addFieldError(field, message);
                fieldValid = false;
            }
        }

        // Min length validation
        if (rules.minLength && value && value.length < rules.minLength) {
            this.addFieldError(field, `The ${rules.displayName} must be at least ${rules.minLength} characters.`);
            fieldValid = false;
        }

        // Max length validation
        if (rules.maxLength && value && value.length > rules.maxLength) {
            this.addFieldError(field, `The ${rules.displayName} may not be greater than ${rules.maxLength} characters.`);
            fieldValid = false;
        }

        return fieldValid;
    }

    /**
     * Get field element by name
     * @param {string} fieldName - Name attribute
     * @returns {Element|null} - Field element
     */
    getField(fieldName) {
        return this.form.querySelector(`[name="${fieldName}"]`);
    }

    /**
     * Get field value handling different input types
     * @param {Element} field - Field element
     * @returns {string} - Field value
     */
    getFieldValue(field) {
        if (!field) return '';
        
        if (field.type === 'checkbox') {
            return field.checked ? field.value : '';
        } else if (field.type === 'radio') {
            const checkedRadio = this.form.querySelector(`[name="${field.name}"]:checked`);
            return checkedRadio ? checkedRadio.value : '';
        }
        
        return field.value || '';
    }

    /**
     * Add error styling and message to field
     * @param {Element} field - Field element
     * @param {string} message - Error message
     */
    addFieldError(field, message) {
        // Use custom message if available
        const customMessage = this.customMessages[field.name];
        const errorMessage = customMessage || message;

        field.classList.add(this.errorClass);
        
        // Create error message element
        const errorElement = document.createElement('div');
        errorElement.className = this.errorMessageClass;
        errorElement.textContent = errorMessage;
        
        // Insert error message after field
        field.parentNode.insertBefore(errorElement, field.nextSibling);
        
        this.isValid = false;
        if (!this.firstErrorField) {
            this.firstErrorField = field;
        }
    }

    /**
     * Clear all validation errors
     */
    clearValidationErrors() {
        // Remove error classes
        this.form.querySelectorAll(`.${this.errorClass}`).forEach(field => {
            field.classList.remove(this.errorClass);
        });
        
        // Remove error messages
        this.form.querySelectorAll(`.${this.errorMessageClass}`).forEach(element => {
            element.remove();
        });
    }

    /**
     * Handle validation errors (focus, scroll, notifications)
     */
    handleValidationErrors() {
        if (this.firstErrorField) {
            this.firstErrorField.focus();
            
            // Smooth scroll to first error
            this.firstErrorField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Show toastr error if available
        if (typeof toastr !== 'undefined') {
            toastr.error('Please correct the errors in the form before submitting.');
        }
    }

    /**
     * Format field name for display
     * @param {string} fieldName - Field name
     * @returns {string} - Formatted display name
     */
    formatFieldName(fieldName) {
        return fieldName
            .replace(/_/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
    }

    /**
     * Enable real-time validation
     */
    enableRealTimeValidation() {
        Object.keys(this.validationRules).forEach(fieldName => {
            const field = this.getField(fieldName);
            if (field) {
                field.addEventListener('blur', () => {
                    this.clearFieldErrors(field);
                    this.validateField(fieldName);
                });
                
                field.addEventListener('input', () => {
                    // Clear errors on input to provide immediate feedback
                    if (field.classList.contains(this.errorClass)) {
                        this.clearFieldErrors(field);
                    }
                });
            }
        });
        
        return this;
    }

    /**
     * Clear errors for a specific field
     * @param {Element} field - Field element
     */
    clearFieldErrors(field) {
        field.classList.remove(this.errorClass);
        const nextSibling = field.nextSibling;
        if (nextSibling && nextSibling.classList && nextSibling.classList.contains(this.errorMessageClass)) {
            nextSibling.remove();
        }
    }
}

// Export for use
window.FormValidator = FormValidator;

// Common validation rules presets
window.ValidationRules = {
    required: { required: true },
    email: { required: true, email: true },
    phone: { required: true, phone: true },
    numeric: { numeric: true },
    requiredNumeric: { required: true, numeric: true },
    positiveNumber: { required: true, numeric: true, min: 0 },
    percentage: { numeric: true, min: 0, max: 100 },
    date: { required: true, date: true },
    text: { required: true, minLength: 2 },
    
    // Insurance specific presets
    policyNumber: { 
        required: true, 
        pattern: '^[A-Z0-9/-]{5,20}$',
        patternMessage: 'Policy number must contain only letters, numbers, hyphens, and forward slashes (5-20 characters)'
    },
    vehicleNumber: {
        required: true,
        pattern: '^[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}$',
        patternMessage: 'Vehicle number must be in format: XX00XX0000'
    },
    gstNumber: {
        pattern: '^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$',
        patternMessage: 'GST number must be in valid format'
    }
};