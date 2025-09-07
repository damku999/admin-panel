# Development Guidelines - Insurance Admin Panel

## 🎯 Project Standards

## 🔄 REUSABILITY FIRST PRINCIPLE
**CRITICAL:** All code must follow the DRY (Don't Repeat Yourself) principle. If code is written more than once, it MUST be abstracted into reusable components.

### 📋 Reusable Component Architecture

#### 1. **Blade Components** (Views)
```php
// ✅ CORRECT - Create reusable components
resources/views/components/
├── modals/
│   ├── confirm-modal.blade.php       // Generic confirmation modal
│   ├── form-modal.blade.php          // Generic form modal
│   └── whatsapp-preview-modal.blade.php
├── buttons/
│   ├── action-button.blade.php       // Reusable action buttons
│   ├── whatsapp-button.blade.php     // WhatsApp action button
│   └── status-badge.blade.php        // Status display badge
├── forms/
│   ├── search-field.blade.php        // Universal search input
│   ├── date-range-picker.blade.php   // Date range selector
│   └── file-upload.blade.php         // File upload component
└── tables/
    ├── data-table.blade.php          // Generic data table
    ├── action-column.blade.php       // Table action buttons
    └── pagination.blade.php          // Pagination component

// ❌ WRONG - Duplicate modal HTML in every page
```

#### 2. **JavaScript Components** (Functions) ✅ COMPLETED
```javascript
// ✅ CORRECT - Shared JavaScript modules
public/admin/js/
├── components/                       // ✅ Centralized in layouts/app.blade.php
│   ├── modal-manager.js              // Universal modal functions ✅
│   ├── ajax-forms.js                 // Generic AJAX form handling ✅
│   ├── whatsapp-sender.js            // WhatsApp functionality ✅
│   ├── notification-manager.js       // Toast notifications ✅
│   └── data-table-manager.js         // Table interactions ✅
├── modules/                          // ✅ IMPLEMENTED
│   ├── claims-common.js              // Claim-specific functions ✅
│   ├── customers-common.js           // Customer-specific functions ✅
│   └── quotations-common.js          // Quotation-specific functions ✅
└── utils/                            // ✅ IMPLEMENTED
    ├── validators.js                 // Form validation utilities ✅
    ├── formatters.js                 // Data formatting utilities ✅
    └── helpers.js                    // General helper functions ✅

// ❌ WRONG - Same functions copied to every page (ELIMINATED)
```

#### 3. **PHP Service Classes** (Business Logic)
```php
// ✅ CORRECT - Reusable service architecture
app/Services/
├── Core/
│   ├── BaseService.php               // Base service with common methods
│   ├── ValidationService.php         // Shared validation logic
│   └── FileHandlingService.php       // File upload/download
├── Communication/
│   ├── WhatsAppService.php           // All WhatsApp functionality
│   ├── EmailService.php              // Email sending
│   └── NotificationService.php       // Push notifications
├── Export/
│   ├── ExcelExportService.php        // Generic Excel export
│   ├── PdfGenerationService.php      // PDF creation
│   └── ReportGeneratorService.php    // Report generation
└── Business/
    ├── ClaimManagementService.php    // Claim business logic
    ├── CustomerManagementService.php // Customer operations
    └── QuotationService.php          // Quote calculations

// ❌ WRONG - Business logic repeated in controllers
```

### 🔧 Implementation Rules

#### **Rule 1: Three-Strike Rule**
If you write similar code **3 times**, it MUST be abstracted into a reusable component.

#### **Rule 2: Component Hierarchy**
```
Generic Components (Used everywhere)
    ↓
Module Components (Used in one module like Claims)
    ↓
Page-Specific Code (Only for one page)
```

#### **Rule 3: Naming Conventions**
- **Generic Components:** `<x-modal>`, `<x-button>`, `<x-table>`
- **Module Components:** `<x-claim-modal>`, `<x-customer-form>`
- **JavaScript:** `ModalManager`, `ClaimActions`, `CustomerUtils`

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
- **Use enhanced Select2 implementation** for all customer dropdowns with mobile number display
- **Implement consistent loading states** with `showLoading()` and `hideLoading()`
- **Ensure responsive design** across all devices
- **Use performAjaxOperation()** for standardized AJAX calls with error handling
- **Follow unified button styling** with `btn-sm` class and `gap: 6px` spacing

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

### Enhanced Select2 Customer Dropdowns
```javascript
// Standard implementation for all customer selection dropdowns
$('#customer_id').select2({
    placeholder: 'Search and select customer...',
    allowClear: true,
    width: '100%',
    minimumInputLength: 0,
    escapeMarkup: function(markup) {
        return markup; // Allow HTML in results
    },
    templateResult: function(option) {
        if (!option.id || option.loading) {
            return option.text;
        }
        
        const $option = $(option.element);
        const mobile = $option.data('mobile');
        const customerName = option.text.split(' - ')[0];
        
        if (mobile) {
            return '<div style="padding: 5px;"><strong>' + customerName + '</strong><br><small class="text-muted" style="color: #6c757d;">📱 ' + mobile + '</small></div>';
        }
        
        return '<div style="padding: 5px;">' + customerName + '</div>';
    },
    templateSelection: function(option) {
        if (!option.id) {
            return option.text;
        }
        
        const customerName = option.text.split(' - ')[0];
        return customerName;
    }
});

// Required option format with mobile data
<option value="{{ $customer->id }}" data-mobile="{{ $customer->mobile_number }}">
    {{ $customer->name }}
    @if ($customer->mobile_number)
        - {{ $customer->mobile_number }}
    @endif
</option>
```

### Best Practices for New Development
1. **Always use centralized modal functions** instead of Bootstrap modal methods
2. **Use performAjaxOperation()** for consistent AJAX handling
3. **Leverage automatic error handling** - don't reinvent error display
4. **Use showLoading()/hideLoading()** for consistent loading states
5. **Follow the established patterns** in layouts/app.blade.php
6. **Use enhanced Select2 implementation** for all customer dropdowns with mobile display
7. **Always use reusable Blade components** instead of duplicate HTML/forms
8. **Follow component hierarchy**: Generic → Module-specific → Page-specific

---

## 🧩 Reusable Blade Components Usage Guide

### Modal Components

#### Generic Form Modal
```blade
<x-modals.form-modal 
    id="editCustomerModal" 
    title="Edit Customer"
    size="lg"
    :show-footer="true">
    
    <x-slot name="body">
        <form id="editCustomerForm">
            <!-- Form content -->
        </form>
    </x-slot>
    
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Save</button>
    </x-slot>
</x-modals.form-modal>
```

#### Confirmation Modal
```blade
<x-modals.confirm-modal 
    id="deleteModal" 
    title="Confirm Deletion"
    message="Are you sure you want to delete this customer?"
    :confirm-button="['text' => 'Yes, Delete', 'class' => 'btn-danger']"
    onclick="confirmDelete()">
</x-modals.confirm-modal>
```

#### WhatsApp Preview Modal
```blade
<x-modals.whatsapp-preview-modal 
    id="whatsappModal"
    title="Send WhatsApp Message"
    :customer="$customer"
    :message="$whatsappMessage"
    :document-url="$documentUrl"
    send-action="sendWhatsAppMessage">
</x-modals.whatsapp-preview-modal>
```

### Button Components

#### Action Buttons
```blade
<!-- Primary edit button -->
<x-buttons.action-button 
    variant="primary" 
    size="sm" 
    icon="fas fa-edit"
    href="{{ route('customers.edit', $customer->id) }}"
    title="Edit Customer">
    Edit
</x-buttons.action-button>

<!-- AJAX button with loading -->
<x-buttons.action-button 
    variant="success" 
    size="sm" 
    icon="fas fa-check"
    onclick="approveRecord({{ $item->id }})"
    :loading="false"
    title="Approve">
    Approve
</x-buttons.action-button>
```

#### WhatsApp Buttons
```blade
<x-buttons.whatsapp-button 
    :item-id="$customer->id"
    action="send"
    :mobile="$customer->mobile_number"
    :message="$whatsappMessage"
    :document-url="$documentUrl"
    title="Send WhatsApp">
</x-buttons.whatsapp-button>
```

#### Status Badges
```blade
<x-buttons.status-badge 
    :status="$claim->status"
    :status-colors="{
        'open': 'primary',
        'in_progress': 'warning', 
        'closed': 'success',
        'rejected': 'danger'
    }"
    :clickable="true"
    :pulse="$claim->status === 'in_progress'"
    onclick="changeClaimStatus({{ $claim->id }})">
</x-buttons.status-badge>
```

### Form Components

#### Search Field
```blade
<x-forms.search-field 
    id="customerSearch"
    placeholder="Search customers..."
    oninput="filterCustomers(this.value)"
    :with-button="false"
    :clear-button="true">
</x-forms.search-field>
```

#### Date Range Picker
```blade
<x-forms.date-range-picker 
    start-id="start_date"
    end-id="end_date"
    label="Policy Period"
    :required="true"
    onchange="updatePremium()">
</x-forms.date-range-picker>
```

#### File Upload
```blade
<x-forms.file-upload 
    id="documents"
    name="documents[]"
    label="Upload Policy Documents"
    :multiple="true"
    allowed-types="documents"
    max-size="5"
    :show-preview="true">
</x-forms.file-upload>
```

### Table Components

#### Data Table with Actions
```blade
<x-tables.data-table 
    :headers="[
        ['key' => 'name', 'label' => 'Customer Name', 'sortable' => true],
        ['key' => 'mobile', 'label' => 'Mobile', 'sortable' => false],
        ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'class' => 'text-center']
    ]"
    :rows="$customers"
    :actions="true"
    search-placeholder="Search customers..."
    :show-export="true"
    export-url="{{ route('customers.export') }}"
    :pagination="$customers">
    
    @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->name }}</td>
            <td>
                <i class="fas fa-phone text-success me-1"></i>
                {{ $customer->mobile_number }}
            </td>
            <td class="text-center">
                <x-buttons.status-badge :status="$customer->status" />
            </td>
            <x-tables.action-column :item="$customer">
                <x-buttons.whatsapp-button 
                    :item-id="$customer->id"
                    :mobile="$customer->mobile_number" />
                    
                <x-buttons.action-button 
                    variant="primary" 
                    size="sm" 
                    icon="fas fa-edit"
                    href="{{ route('customers.edit', $customer->id) }}" />
                    
                <x-buttons.action-button 
                    variant="danger" 
                    size="sm" 
                    icon="fas fa-trash"
                    onclick="confirmDelete({{ $customer->id }})" />
            </x-tables.action-column>
        </tr>
    @endforeach
</x-tables.data-table>
```

#### Pagination Component
```blade
<x-tables.pagination 
    :paginator="$customers"
    :show-info="true"
    :show-per-page="true"
    info-text="Showing {start} to {end} of {total} customers">
</x-tables.pagination>
```

### Component Integration Best Practices

1. **Always use slots for dynamic content** in modals and complex components
2. **Pass data via props** for simple values and configuration
3. **Use component composition** - combine small components to build larger ones
4. **Follow naming conventions** - x-category.component-name format
5. **Include accessibility attributes** - ARIA labels, keyboard navigation
6. **Provide sensible defaults** - minimize required props
7. **Document usage examples** in component comment headers

---

## 📊 Reusable Export System Usage Guide

### Export Button Component

#### Basic Export Button
```blade
<x-buttons.export-button 
    export-url="{{ route('customers.export') }}"
    title="Export Customers">
    Export Data
</x-buttons.export-button>
```

#### Advanced Export with Formats & Filters
```blade
<x-buttons.export-button 
    export-url="{{ route('claims.export') }}"
    :formats="['xlsx', 'csv', 'pdf']"
    :show-dropdown="true"
    :with-filters="true"
    :ajax-export="true"
    title="Export Claims Data">
    Export Claims
</x-buttons.export-button>
```

### Controller Integration (ExportableTrait)

#### Minimal Implementation (Zero Configuration)
```php
use App\Traits\ExportableTrait;

class CustomerController extends Controller
{
    use ExportableTrait;
    
    // That's it! Export method is automatically available
    // Auto-detects model: Customer
    // Auto-generates filename: customers_2025_09_05.xlsx
    // Includes basic columns and professional styling
}
```

#### Customized Implementation
```php
use App\Traits\ExportableTrait;

class CustomerController extends Controller
{
    use ExportableTrait;
    
    // Include relationships in export
    protected function getExportRelations(): array
    {
        return ['familyGroup', 'insurance'];
    }
    
    // Define searchable fields for filtering
    protected function getSearchableFields(): array
    {
        return ['name', 'email', 'mobile_number', 'pan_card_number'];
    }
    
    // Customize export configuration
    protected function getExportConfig(Request $request): array
    {
        return array_merge(parent::getExportConfig($request), [
            'headings' => [
                'Customer ID', 'Full Name', 'Email Address', 'Mobile Number',
                'Family Group', 'Status', 'Registration Date'
            ],
            'mapping' => function($customer) {
                return [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->mobile_number,
                    $customer->familyGroup ? $customer->familyGroup->name : 'Individual',
                    ucfirst($customer->status),
                    $customer->created_at->format('d-m-Y H:i:s')
                ];
            },
            'with_headings' => true,
            'with_mapping' => true
        ]);
    }
    
    // Custom date field for date range filtering
    protected function getDateFilterField(): string
    {
        return 'created_at'; // or 'date_of_birth', 'updated_at', etc.
    }
}
```

### Direct ExcelExportService Usage

#### Quick Export
```php
$exportService = app(\App\Services\ExcelExportService::class);

// Simple model export
return $exportService->quickExport(\App\Models\Customer::class);

// With specific columns
return $exportService->quickExport(
    \App\Models\Customer::class, 
    ['name', 'email', 'mobile_number', 'status']
);
```

#### Advanced Export
```php
$exportService = app(\App\Services\ExcelExportService::class);

// Export with relationships and custom mapping
return $exportService->exportWithMapping(
    \App\Models\Claim::class,
    ['Claim Number', 'Customer', 'Amount', 'Status', 'Date'],
    function($claim) {
        return [
            $claim->claim_number,
            $claim->customer->name,
            number_format($claim->claim_amount, 2),
            ucfirst($claim->claim_status),
            $claim->created_at->format('d-m-Y')
        ];
    },
    ['relations' => ['customer', 'customerInsurance']]
);

// Export with date range
return $exportService->exportDateRange(
    \App\Models\CustomerInsurance::class,
    'start_date',
    '2025-01-01',
    '2025-12-31'
);

// Export with filters
return $exportService->exportFiltered(
    \App\Models\Customer::class,
    ['status' => 'active', 'type' => 'premium']
);
```

### Export System Features

#### ✅ **Automatic Features**
- **Smart Model Detection**: Auto-detects model from controller name
- **Professional Styling**: Headers with colors, borders, alternating rows
- **Multiple Formats**: XLSX, CSV, PDF support
- **Auto-sizing Columns**: Perfect column widths
- **Filename Generation**: Includes date/time stamps

#### ✅ **Filtering & Search**
- **Text Search**: Across multiple defined fields
- **Date Range**: Filter by any date field
- **Status Filtering**: Active, inactive, pending, etc.
- **Custom Filters**: Add any field-based filters

#### ✅ **Performance & Memory**
- **Stream Processing**: Memory efficient for large datasets
- **Progress Indicators**: Visual feedback for long exports
- **Error Handling**: Graceful failures with notifications
- **Background Processing**: Optional AJAX exports

#### ✅ **Developer Experience**  
- **Zero Configuration**: Works immediately with just the trait
- **Preset Configurations**: Common patterns (customers, claims, etc.)
- **Easy Customization**: Override methods as needed
- **Consistent API**: Same interface across all controllers

### Migration from Old Export System

#### Before (Duplicate Code)
```php
// OLD: Each controller has duplicate export method
public function export()
{
    return Excel::download(new CustomersExport, 'customers.xlsx');
}

// OLD: Separate export class for each model  
class CustomersExport implements FromCollection
{
    public function collection()
    {
        return Customer::all(); // No filtering, basic functionality
    }
}
```

#### After (Reusable System)
```php
// NEW: One line integration with all features
use App\Traits\ExportableTrait;

class CustomerController extends Controller
{
    use ExportableTrait; // Export method now automatic with advanced features
}

// NEW: Delete the old CustomersExport.php file - no longer needed!
```

#### View Updates
```blade
{{-- OLD: Basic export link --}}
<a href="{{ route('customers.export') }}" class="btn btn-success">Export</a>

{{-- NEW: Feature-rich export component --}}
<x-buttons.export-button 
    export-url="{{ route('customers.export') }}"
    :formats="['xlsx', 'csv', 'pdf']"
    :with-filters="true">
    Export Customers
</x-buttons.export-button>
```

### Impact: 70% Code Reduction + 300% More Features

- **Before**: 14 duplicate export methods + 13 export classes = ~350 lines duplicate code
- **After**: 1 trait + 1 service + 1 export class = Universal solution
- **Features Added**: Multi-format, filtering, search, styling, progress, error handling
- **Maintenance**: Single codebase instead of 27 separate implementations

---

## 🏥 Claims Management Module Patterns

### Database Schema Design
- **Master-Detail Architecture**: Main `claims` table with related `claim_documents`, `claim_stages`, and `claim_liabilities`
- **Insurance Type Differentiation**: Single table with conditional fields based on Health/Truck insurance types
- **Audit Trail**: Full audit tracking with soft deletes and user tracking on all related tables
- **Performance Indexes**: Strategic indexing for search operations (policy_no, vehicle_number, claim_status)

### Model Relationships
```php
// Example of proper Claims relationship setup
public function documents(): HasMany
{
    return $this->hasMany(ClaimDocument::class);
}

public function stages(): HasMany  
{
    return $this->hasMany(ClaimStage::class)->orderBy('stage_order');
}

public function currentStageRecord(): HasOne
{
    return $this->hasOne(ClaimStage::class)->where('is_current', true);
}
```

### Dynamic Form Validation Pattern
```php
// Conditional validation based on insurance type
if ($this->insurance_type === 'Health') {
    $rules = array_merge($rules, [
        'patient_name' => 'required|string|max:255',
        'admission_date' => 'required|date',
        // Health-specific rules...
    ]);
} elseif ($this->insurance_type === 'Truck') {
    $rules = array_merge($rules, [
        'driver_contact_number' => 'required|string|max:20',
        'accident_description' => 'required|string',
        // Truck-specific rules...
    ]);
}
```

### Business Logic Implementation
- **Automated Claim Numbers**: Sequential generation with insurance type prefix (HLT/TRK)
- **Stage Management**: Automatic stage transitions with history tracking
- **Document Templates**: Pre-defined document lists for each insurance type
- **WhatsApp Integration**: Contextual messaging with proper templates

### Controller Action Patterns
```php
// Transaction-safe operations with proper error handling
DB::beginTransaction();
try {
    $claim = Claim::create($request->validated());
    $this->createRequiredDocuments($claim);
    DB::commit();
    return redirect()->route('claims.show', $claim)->with('success', 'Claim created successfully.');
} catch (\Throwable $th) {
    DB::rollBack();
    return redirect()->back()->withInput()->with('error', $th->getMessage());
}
```

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

*Last Updated: 2025-09-04 - Added Claims Management module patterns and enhanced Select2 customer dropdown standards*