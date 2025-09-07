/**
 * Data Formatting Utilities for Admin Panel
 * Provides common formatting functions across all modules
 */

const Formatters = {
    
    /**
     * Initialize Formatters utility
     */
    init: function() {
        console.log('✅ Formatters utility initialized');
        return this;
    },
    
    /**
     * Format currency (Indian Rupees)
     */
    currency: function(amount, options = {}) {
        const num = parseFloat(amount) || 0;
        const locale = options.locale || 'en-IN';
        const currency = options.currency || 'INR';
        
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: options.decimals || 2,
            maximumFractionDigits: options.decimals || 2
        }).format(num);
    },
    
    /**
     * Format numbers with Indian numbering system
     */
    number: function(number, options = {}) {
        const num = parseFloat(number) || 0;
        const locale = options.locale || 'en-IN';
        
        return new Intl.NumberFormat(locale, {
            minimumFractionDigits: options.decimals || 0,
            maximumFractionDigits: options.decimals || 2,
            useGrouping: options.grouping !== false
        }).format(num);
    },
    
    /**
     * Format date in various Indian formats
     */
    date: function(date, format = 'dd-mm-yyyy') {
        if (!date) return '';
        
        const dateObj = new Date(date);
        if (isNaN(dateObj.getTime())) return '';
        
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        
        const formats = {
            'dd-mm-yyyy': `${day}-${month}-${year}`,
            'dd/mm/yyyy': `${day}/${month}/${year}`,
            'mm-dd-yyyy': `${month}-${day}-${year}`,
            'yyyy-mm-dd': `${year}-${month}-${day}`,
            'readable': dateObj.toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }),
            'short': dateObj.toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            })
        };
        
        return formats[format] || formats['dd-mm-yyyy'];
    },
    
    /**
     * Format time
     */
    time: function(time, format = '12h') {
        if (!time) return '';
        
        const timeObj = new Date(`2000-01-01T${time}`);
        if (isNaN(timeObj.getTime())) return '';
        
        if (format === '12h') {
            return timeObj.toLocaleTimeString('en-IN', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        } else {
            return timeObj.toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }
    },
    
    /**
     * Format datetime
     */
    datetime: function(datetime, options = {}) {
        if (!datetime) return '';
        
        const dateObj = new Date(datetime);
        if (isNaN(dateObj.getTime())) return '';
        
        const dateFormat = options.dateFormat || 'dd-mm-yyyy';
        const timeFormat = options.timeFormat || '12h';
        
        return `${this.date(dateObj, dateFormat)} ${this.time(dateObj.toTimeString().split(' ')[0], timeFormat)}`;
    },
    
    /**
     * Format mobile number with Indian format
     */
    mobileNumber: function(mobile, options = {}) {
        if (!mobile) return '';
        
        const cleaned = mobile.replace(/\D/g, '');
        if (cleaned.length !== 10) return mobile;
        
        if (options.withCountryCode) {
            return `+91 ${cleaned.substr(0, 5)} ${cleaned.substr(5)}`;
        }
        
        if (options.spaced) {
            return `${cleaned.substr(0, 5)} ${cleaned.substr(5)}`;
        }
        
        return cleaned;
    },
    
    /**
     * Format PAN card number
     */
    panCard: function(pan) {
        if (!pan) return '';
        return pan.toUpperCase();
    },
    
    /**
     * Format Aadhaar number with masking or spacing
     */
    aadhaarNumber: function(aadhaar, options = {}) {
        if (!aadhaar) return '';
        
        const cleaned = aadhaar.replace(/\D/g, '');
        if (cleaned.length !== 12) return aadhaar;
        
        if (options.masked) {
            return `XXXX XXXX ${cleaned.substr(8)}`;
        }
        
        if (options.spaced) {
            return `${cleaned.substr(0, 4)} ${cleaned.substr(4, 4)} ${cleaned.substr(8)}`;
        }
        
        return cleaned;
    },
    
    /**
     * Format vehicle registration number
     */
    vehicleNumber: function(vehicle) {
        if (!vehicle) return '';
        
        const cleaned = vehicle.toUpperCase().replace(/\s/g, '');
        if (cleaned.length !== 10) return vehicle;
        
        return `${cleaned.substr(0, 2)} ${cleaned.substr(2, 2)} ${cleaned.substr(4, 2)} ${cleaned.substr(6)}`;
    },
    
    /**
     * Format file size in human readable format
     */
    fileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    /**
     * Format percentage
     */
    percentage: function(value, options = {}) {
        const num = parseFloat(value) || 0;
        const decimals = options.decimals || 2;
        
        if (options.multiply) {
            return `${(num * 100).toFixed(decimals)}%`;
        }
        
        return `${num.toFixed(decimals)}%`;
    },
    
    /**
     * Capitalize first letter of each word
     */
    titleCase: function(text) {
        if (!text) return '';
        
        return text.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    },
    
    /**
     * Format text to sentence case
     */
    sentenceCase: function(text) {
        if (!text) return '';
        
        return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
    },
    
    /**
     * Truncate text with ellipsis
     */
    truncate: function(text, length = 50, suffix = '...') {
        if (!text || text.length <= length) return text;
        
        return text.substr(0, length).trim() + suffix;
    },
    
    /**
     * Format address with proper line breaks
     */
    address: function(addressObj, options = {}) {
        if (!addressObj) return '';
        
        const parts = [];
        
        if (addressObj.line1) parts.push(addressObj.line1);
        if (addressObj.line2) parts.push(addressObj.line2);
        if (addressObj.city) parts.push(addressObj.city);
        if (addressObj.state) parts.push(addressObj.state);
        if (addressObj.pincode) parts.push(addressObj.pincode);
        
        const separator = options.html ? '<br>' : '\n';
        return parts.join(separator);
    },
    
    /**
     * Format status with proper styling
     */
    status: function(status, options = {}) {
        if (!status) return '';
        
        const statusFormatted = this.titleCase(status.toString().replace(/[_-]/g, ' '));
        
        if (options.badge) {
            const statusClass = this.getStatusClass(status);
            return `<span class="badge badge-${statusClass}">${statusFormatted}</span>`;
        }
        
        return statusFormatted;
    },
    
    /**
     * Get Bootstrap class for status
     */
    getStatusClass: function(status) {
        const statusMap = {
            'active': 'success',
            'inactive': 'secondary',
            'pending': 'warning',
            'approved': 'success',
            'rejected': 'danger',
            'draft': 'info',
            'published': 'success',
            'archived': 'dark',
            'processing': 'warning',
            'completed': 'success',
            'failed': 'danger',
            'cancelled': 'secondary'
        };
        
        const normalizedStatus = status.toString().toLowerCase().replace(/[_-]/g, '');
        return statusMap[normalizedStatus] || 'secondary';
    },
    
    /**
     * Format duration in human readable format
     */
    duration: function(minutes) {
        if (!minutes || minutes === 0) return '0 minutes';
        
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        
        if (hours === 0) {
            return `${remainingMinutes} minute${remainingMinutes !== 1 ? 's' : ''}`;
        }
        
        if (remainingMinutes === 0) {
            return `${hours} hour${hours !== 1 ? 's' : ''}`;
        }
        
        return `${hours} hour${hours !== 1 ? 's' : ''} ${remainingMinutes} minute${remainingMinutes !== 1 ? 's' : ''}`;
    },
    
    /**
     * Format age from birthdate
     */
    age: function(birthdate) {
        if (!birthdate) return '';
        
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return `${age} year${age !== 1 ? 's' : ''}`;
    },
    
    /**
     * Format list with proper conjunction
     */
    list: function(items, conjunction = 'and') {
        if (!Array.isArray(items) || items.length === 0) return '';
        
        if (items.length === 1) return items[0];
        if (items.length === 2) return `${items[0]} ${conjunction} ${items[1]}`;
        
        const lastItem = items[items.length - 1];
        const otherItems = items.slice(0, -1);
        
        return `${otherItems.join(', ')}, ${conjunction} ${lastItem}`;
    }
};

// Auto-format elements on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format currency elements
    document.querySelectorAll('[data-format="currency"]').forEach(element => {
        const value = element.textContent || element.value;
        if (value) {
            const formatted = Formatters.currency(value);
            if (element.tagName === 'INPUT') {
                element.value = formatted;
            } else {
                element.textContent = formatted;
            }
        }
    });
    
    // Auto-format date elements
    document.querySelectorAll('[data-format="date"]').forEach(element => {
        const value = element.textContent || element.value;
        const format = element.dataset.dateFormat || 'dd-mm-yyyy';
        if (value) {
            const formatted = Formatters.date(value, format);
            if (element.tagName === 'INPUT') {
                element.value = formatted;
            } else {
                element.textContent = formatted;
            }
        }
    });
    
    // Auto-format mobile number elements
    document.querySelectorAll('[data-format="mobile"]').forEach(element => {
        const value = element.textContent || element.value;
        if (value) {
            const options = {
                spaced: element.dataset.spaced === 'true',
                withCountryCode: element.dataset.countryCode === 'true'
            };
            const formatted = Formatters.mobileNumber(value, options);
            if (element.tagName === 'INPUT') {
                element.value = formatted;
            } else {
                element.textContent = formatted;
            }
        }
    });
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Formatters;
}

// Make available globally
window.Formatters = Formatters;