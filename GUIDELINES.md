# Development Guidelines - Insurance Admin Panel

## 🎯 Project Standards

### Code Quality
- **Follow PSR-12 coding standards** for PHP code
- **Use meaningful variable and function names** that describe their purpose
- **Write self-documenting code** with clear logic flow
- **Add comments only when necessary** to explain complex business logic
- **Keep functions small and focused** on single responsibilities

### Laravel Best Practices
- **Use Form Request classes** for validation (CreateQuotationRequest, UpdateQuotationRequest)
- **Leverage Eloquent relationships** instead of raw queries
- **Use resource controllers** for CRUD operations
- **Implement proper middleware** for authentication and authorization
- **Follow RESTful routing conventions**

### Frontend Development
- **Use Vue.js 2 components** for interactive elements
- **Follow Bootstrap 5 conventions** for consistent styling (Customer Portal) / jQuery-only for Admin Panel
- **Implement proper form validation** with server-side error display
- **Use centralized modal system** via `showModal()` and `hideModal()` functions
- **Use Select2 or similar libraries** for enhanced user experience
- **Implement consistent loading states** with `showLoading()` and `hideLoading()`
- **Ensure responsive design** across all devices
- **Use performAjaxOperation()** for standardized AJAX calls with error handling

---

## 🛡️ Security Guidelines

### Data Validation
- **Always validate on server-side** - never trust client-side validation alone
- **Use Laravel's built-in validation rules** whenever possible
- **Sanitize user input** before database operations
- **Implement CSRF protection** on all forms
- **Use proper SQL injection prevention** with Eloquent ORM

### Authentication & Authorization
- **Implement role-based permissions** using Spatie Laravel Permission
- **Use secure password hashing** (Laravel's default bcrypt)
- **Implement session timeout** for security
- **Log security-sensitive actions** using ActivityLog
- **Validate user permissions** on every sensitive operation

### File Handling
- **Validate file types and sizes** before upload
- **Store uploads outside web root** when possible
- **Use proper file naming** to prevent conflicts
- **Implement virus scanning** for uploaded files
- **Set proper file permissions**

---

## 🗄️ Database Guidelines

### Schema Design
- **Use meaningful table and column names**
- **Implement proper foreign key constraints**
- **Add indexes for frequently queried columns**
- **Use appropriate data types** for each field
- **Document schema changes** in migration files

### Model Relationships
- **Define all relationships** in Eloquent models
- **Use appropriate relationship types** (hasMany, belongsTo, etc.)
- **Implement soft deletes** where data retention is important
- **Add model observers** for audit logging
- **Use accessors/mutators** for data formatting

### Migration Best Practices
- **Create specific migrations** for each schema change
- **Use descriptive migration names** with timestamps
- **Always test rollback functionality**
- **Add proper down() methods** for reversibility
- **Document breaking changes** in migration comments

---

## 🧪 Testing Guidelines

### Test Coverage
- **Write feature tests** for all major user workflows
- **Create unit tests** for service classes and business logic
- **Test validation rules** thoroughly
- **Mock external dependencies** (APIs, file systems)
- **Test edge cases and error conditions**

### Test Organization
- **Group related tests** in test classes
- **Use descriptive test method names** that explain the scenario
- **Create test data** using factories and seeders
- **Clean up test data** after each test
- **Use separate test database** configuration

---

## 🚀 Performance Guidelines

### Database Optimization
- **Use eager loading** to prevent N+1 queries
- **Add database indexes** for frequently searched columns
- **Paginate large result sets** instead of loading all records
- **Use database transactions** for multi-table operations
- **Monitor slow query log** regularly

### Caching Strategy
- **Cache frequently accessed data** using Laravel's cache system
- **Invalidate cache** when data changes
- **Use appropriate cache drivers** (Redis for production)
- **Cache expensive calculations** and API responses
- **Implement cache tags** for grouped invalidation

### Frontend Performance
- **Minify CSS and JavaScript** for production
- **Optimize images** for web display
- **Use lazy loading** for large datasets
- **Implement proper loading states**
- **Minimize HTTP requests** where possible

---

## 📱 User Experience Guidelines

### Form Design
- **Provide clear field labels** and helpful placeholders
- **Show validation errors** immediately below fields
- **Use appropriate input types** (email, tel, number)
- **Implement autocomplete** where beneficial
- **Provide clear success/error feedback**
- **Use dynamic field visibility** (show/hide fields based on conditions)
- **Make conditional fields required** when their conditions are met

### Navigation & Accessibility
- **Ensure keyboard navigation** works throughout the application
- **Use semantic HTML elements** for screen readers
- **Provide alt text** for images
- **Maintain consistent navigation** patterns
- **Test with accessibility tools**

### Mobile Experience
- **Design mobile-first** approach
- **Test on actual devices** not just browser emulation
- **Ensure touch targets** are appropriately sized
- **Optimize forms** for mobile input
- **Test offline functionality** where applicable

---

## 🔧 Development Workflow

### Version Control
- **Use descriptive commit messages** following conventional commits
- **Create feature branches** for all new development
- **Review code** before merging to main branch
- **Tag releases** with semantic versioning
- **Document breaking changes** in commit messages

### Code Review Process
- **Review for functionality** - does it work as intended?
- **Check for security issues** - any potential vulnerabilities?
- **Verify performance impact** - any new bottlenecks?
- **Ensure test coverage** - are new features tested?
- **Validate documentation** - are changes documented?

### Deployment
- **Use environment-specific configurations**
- **Run migrations** safely in production
- **Clear caches** after deployment
- **Monitor application** after releases
- **Have rollback plan** ready

---

## 🚨 Troubleshooting Guide

### Common Issues & Solutions

#### JavaScript Not Working
1. Check browser console for errors
2. Verify jQuery and other dependencies are loaded (Admin Panel uses jQuery-only, NO Bootstrap JS)
3. Use centralized modal functions (`showModal()`, `hideModal()`) instead of Bootstrap modal methods
4. Ensure proper event delegation for dynamic elements
5. Check for syntax errors in custom JavaScript
6. Use `performAjaxOperation()` for standardized AJAX calls with automatic error handling

#### Form Validation Issues
1. Verify Form Request validation rules
2. Check that error display is implemented in Blade templates
3. Ensure CSRF tokens are included
4. Test server-side validation independently

#### Database Issues
1. Check migration files for schema conflicts
2. Verify foreign key constraints
3. Look for N+1 query problems
4. Check database logs for slow queries

#### Permission Problems
1. Verify role assignments in database
2. Check middleware configuration
3. Ensure permission names match exactly
4. Test with different user roles

---

## 🎛️ Centralized Systems Guide

### Modal System Usage
```javascript
// Universal modal functions (available globally)
showModal('modalId');           // Show any modal by ID
hideModal('modalId');           // Hide any modal by ID

// Specialized functions
showSendWhatsAppModal(quotationId);    // WhatsApp send modal
showResendWhatsAppModal(quotationId);  // WhatsApp resend modal
showDeleteQuotationModal(quotationId); // Delete quotation modal
showLogoutModal();                     // Logout confirmation modal
```

### Loading State Management
```javascript
// Loading spinner functions
showLoading('Custom message...');  // Show with custom message
showLoading();                     // Show with default 'Loading...' message
hideLoading();                     // Hide loading spinner

// Enhanced AJAX with automatic loading
performAjaxOperation({
    type: 'POST',
    url: '/your-endpoint',
    data: formData,
    loaderMessage: 'Processing...',    // Custom loading message
    showSuccessNotification: true,     // Auto-show success notifications
    success: function(response) {
        // Your success handler
    }
});
```

### Error Handling System
```javascript
// Global error handling automatically handles:
// - 401 Unauthorized → Redirect to login
// - 403 Forbidden → Permission denied message
// - 419 CSRF expired → Session expired message
// - 422 Validation → Display validation errors
// - 500 Server error → Generic error message
// - Automatic loading spinner cleanup on errors
```

### Best Practices for New Development
1. **Always use centralized modal functions** instead of Bootstrap modal methods
2. **Use performAjaxOperation()** for consistent AJAX handling
3. **Leverage automatic error handling** - don't reinvent error display
4. **Use showLoading()/hideLoading()** for consistent loading states
5. **Follow the established patterns** in layouts/app.blade.php

---

## 📚 Resources & Documentation

### Laravel Resources
- [Laravel Documentation](https://laravel.com/docs)
- [Eloquent ORM Guide](https://laravel.com/docs/eloquent)
- [Validation Rules](https://laravel.com/docs/validation#available-validation-rules)

### Frontend Resources
- [Vue.js 2 Guide](https://v2.vuejs.org/v2/guide/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [Select2 Documentation](https://select2.org/)

### Security Resources
- [OWASP Web Security](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)

---

*Last Updated: 2025-09-04 - Added centralized systems guide and updated best practices*