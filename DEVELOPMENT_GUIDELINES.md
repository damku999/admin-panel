# Insurance Management System - Development Guidelines

## Table of Contents
- [Project Overview](#project-overview)
- [Architecture Assessment](#architecture-assessment)
- [System Architecture](#system-architecture)
- [Development Standards](#development-standards)
- [Module Development Patterns](#module-development-patterns)
- [Frontend Guidelines](#frontend-guidelines)
- [Database Guidelines](#database-guidelines)
- [Security Guidelines](#security-guidelines)
- [Testing Guidelines](#testing-guidelines)
- [Deployment Guidelines](#deployment-guidelines)
- [Architecture Improvements](#architecture-improvements)
- [Code Review Checklist](#code-review-checklist)

---

## Project Overview

**Project Type**: Laravel 10 + jQuery Insurance Management System  
**Architecture**: Dual Portal System (Admin + Customer)  
**Frontend**: jQuery 3.7.1 + Mixed Bootstrap 4/5 (Vue.js loaded but unused)  
**Database**: MySQL 8+  
**Authentication**: Multi-level (Admin + Customer separate systems)  
**Key Features**: Insurance quotations, policy management, family groups, WhatsApp integration

### Portal Structure
1. **Admin Portal** - `/` (20 modules)
2. **Customer Portal** - `/customer/*` (4 modules)

---

## Architecture Assessment

### 📊 **Overall Architecture Rating**: **B+ (85/100)**

**Strengths**: Well-structured dual portals, comprehensive audit trails, proper security layers  
**Areas for Improvement**: Service layer expansion, interface abstractions, caching strategy

### Architecture Component Ratings

| Component | Rating | Score | Status |
|-----------|---------|--------|---------|
| **Dual Portal Architecture** | ⭐⭐⭐⭐⭐ | 9/10 | Excellent |
| **Database Architecture** | ⭐⭐⭐⭐ | 8/10 | Very Good |
| **Security Architecture** | ⭐⭐⭐⭐⭐ | 9/10 | Excellent |
| **Service Layer Architecture** | ⭐⭐⭐ | 7/10 | Good |
| **Frontend Architecture** | ⭐⭐⭐ | 7/10 | Good |
| **Business Logic Organization** | ⭐⭐⭐⭐ | 8/10 | Very Good |

### Critical Architecture Issues

#### ⚠️ **High Priority Issues**
1. **Limited Service Layer Coverage** - Business logic scattered in controllers
2. **Frontend Technology Debt** - Vue.js 2 EOL, jQuery dependency, Bootstrap version inconsistency

#### 🔧 **Medium Priority Issues**
3. **Missing Interface Abstractions** - No interfaces/contracts for services
4. **Database Compatibility** - Enum type issues with MySQL 8.4

#### 🚀 **Low Priority Issues**
5. **Missing Caching Strategy** - No performance optimization caching
6. **Limited API Layer** - No dedicated API endpoints for mobile/integrations

---

## System Architecture

### Dual Authentication System
- **Admin Authentication**: Laravel's default auth system with Spatie roles/permissions
- **Customer Authentication**: Separate system (`CustomerAuthController`) with family access sharing

### Core Components
```
app/
├── Http/Controllers/
│   ├── [AdminControllers]           # Admin portal controllers
│   └── Auth/CustomerAuthController  # Customer portal controller
├── Models/                         # Eloquent models with relationships
├── Services/                       # Business logic services
├── Exports/                        # Excel export classes
├── Http/Requests/                  # Form validation requests
└── Traits/                         # Reusable traits
```

---

## Development Standards

### Naming Conventions

#### Files & Classes
```php
// Controllers: PascalCase + Controller suffix
class CustomerController extends Controller {}
class InsuranceCompanyController extends Controller {}

// Models: PascalCase (singular)
class Customer extends Model {}
class CustomerInsurance extends Model {}

// Requests: PascalCase + Request suffix
class StoreCustomerRequest extends FormRequest {}
class UpdateCustomerRequest extends FormRequest {}

// Services: PascalCase + Service suffix
class QuotationService {}
class PdfGenerationService {}

// Exports: PascalCase + Export suffix
class CustomersExport implements FromCollection {}
```

#### Variables & Methods
```php
// Variables: camelCase
$customerData = [];
$insuranceCompanies = [];

// Methods: camelCase with descriptive names
public function updateStatus() {}
public function downloadPdf() {}
public function sendToWhatsApp() {}
```

#### Database
```sql
-- Tables: snake_case (plural)
customers, customer_insurances, quotation_companies

-- Columns: snake_case
customer_id, mobile_number, date_of_birth, created_at
```

### File Organization Patterns

#### Controllers
```php
<?php

namespace App\Http\Controllers;

use App\Exports\[Entity]Export;
use App\Http\Requests\Store[Entity]Request;
use App\Http\Requests\Update[Entity]Request;
use App\Models\[Entity];
use App\Services\FileUploadService;
use App\Traits\WhatsAppApiTrait; // If WhatsApp functionality needed

class [Entity]Controller extends Controller
{
    use WhatsAppApiTrait; // If needed
    
    public function __construct(private FileUploadService $fileUploadService)
    {
        $this->middleware('auth');
        $this->middleware('permission:[entity]-list|[entity]-create|[entity]-edit|[entity]-delete', ['only' => ['index']]);
        $this->middleware('permission:[entity]-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:[entity]-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:[entity]-delete', ['only' => ['delete']]);
    }
    
    // Required methods: index, create, store, edit, update, updateStatus, export
}
```

#### Models
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class [Entity] extends Model
{
    use SoftDeletes, LogsActivity;
    
    protected $fillable = []; // Define fillable attributes
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
    
    // Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    // Relationships
    // Scopes
    // Accessors/Mutators
}
```

---

## Module Development Patterns

### Standard Module Structure
Every new module should follow this exact pattern:

#### 1. Database Migration
```php
// Pattern: [timestamp]_create_[entities]_table.php
Schema::create('[entities]', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('status', ['Active', 'Inactive'])->default('Active');
    
    // Audit fields (REQUIRED)
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->unsignedBigInteger('deleted_by')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
    
    // Foreign key constraints
    $table->foreign('created_by')->references('id')->on('users');
    $table->foreign('updated_by')->references('id')->on('users');
    $table->foreign('deleted_by')->references('id')->on('users');
});
```

#### 2. Model with Standard Patterns
```php
class [Entity] extends Model
{
    use SoftDeletes, LogsActivity;
    
    protected $fillable = ['name', 'status']; // Define based on business logic
    
    // REQUIRED: Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
    
    // REQUIRED: Audit relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    
    // REQUIRED: Status scope
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
```

#### 3. Controller with Standard Methods
```php
class [Entity]Controller extends Controller
{
    // REQUIRED methods for every module:
    
    public function index(Request $request): View
    {
        // Sorting, filtering, pagination logic
        // Return list view
    }
    
    public function create(): View
    {
        // Return create form
    }
    
    public function store(Store[Entity]Request $request): RedirectResponse
    {
        // Create new record with audit trail
    }
    
    public function edit([Entity] $[entity]): View
    {
        // Return edit form
    }
    
    public function update(Update[Entity]Request $request, [Entity] $[entity]): RedirectResponse
    {
        // Update record with audit trail
    }
    
    public function updateStatus(int $[entity]_id, string $status): RedirectResponse
    {
        // Status update with audit trail
    }
    
    public function export(Request $request)
    {
        // Excel export functionality
        return Excel::download(new [Entity]Export($request), '[entities].xlsx');
    }
}
```

#### 4. Form Requests
```php
// Store[Entity]Request.php
class Store[Entity]Request extends FormRequest
{
    public function authorize(): bool { return true; }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}

// Update[Entity]Request.php - Similar with different validation rules
```

#### 5. Export Class
```php
class [Entity]Export implements FromCollection
{
    private $request;
    
    public function __construct($request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        // Apply same filters as index method
        return [Entity]::select('id', 'name', 'status', 'created_at')->get();
    }
}
```

#### 6. Routes (web.php)
```php
// Standard resource pattern
Route::group(['middleware' => 'auth'], function () {
    Route::get('[entities]', [[Entity]Controller::class, 'index'])->name('[entities].index');
    Route::get('[entities]/create', [[Entity]Controller::class, 'create'])->name('[entities].create');
    Route::post('[entities]/store', [[Entity]Controller::class, 'store'])->name('[entities].store');
    Route::get('[entities]/edit/{[entity]}', [[Entity]Controller::class, 'edit'])->name('[entities].edit');
    Route::put('[entities]/update/{[entity]}', [[Entity]Controller::class, 'update'])->name('[entities].update');
    Route::get('[entities]/update/status/{[entity]_id}/{status}', [[Entity]Controller::class, 'updateStatus'])->name('[entities].status');
    Route::get('[entities]/export', [[Entity]Controller::class, 'export'])->name('[entities].export');
});
```

#### 7. Blade Views Structure
```
resources/views/[entities]/
├── index.blade.php      # List view with filtering, sorting, export
├── create.blade.php     # Create form
├── edit.blade.php       # Edit form
└── delete-modal.blade.php  # Delete confirmation modal
```

---

## Frontend Guidelines

### Portal-Specific Frontend Architecture

#### Admin Portal (`layouts/app.blade.php`)
- **Theme**: SB Admin 2 v4.1.3 (Bootstrap 4 based)
- **Head**: `common/head.blade.php`
- **JavaScript Stack**: jQuery 3.7.1 + SB Admin 2 JS + Toastr + Select2 + DatePicker
- **Features**: Centralized modal system, AJAX operations, form validations
- **Navigation**: Full sidebar with role-based permissions
- **⚠️ Note**: Uses Bootstrap 4 (via SB Admin 2), while customer portal uses Bootstrap 5

#### Customer Portal (`layouts/customer.blade.php`)
- **Theme**: WebMonks Brand (custom CSS)
- **Head**: `common/customer-head.blade.php` (371 lines custom styling)
- **JavaScript Stack**: jQuery 3.7.1 + Bootstrap 5 CDN + Toastr only
- **Features**: Simplified UI, branded styling, mobile-responsive
- **Navigation**: Clean header-only layout without sidebar

### JavaScript Framework Status
- **Vue.js**: Loaded but NOT actively used (mounts to non-existent `#wrapper` element)
- **jQuery**: Primary frontend framework for all interactions
- **Bootstrap**: Mixed versions - Admin uses Bootstrap 4 (via SB Admin 2), Customer uses Bootstrap 5

### Modal System
**CRITICAL**: Use centralized modal functions (already implemented):
```javascript
// Show modal
showModal('modalId');

// Hide modal  
hideModal('modalId');

// WhatsApp modals
showSendWhatsAppModal(quotationId);
hideWhatsAppModal(modalId);
```

**NEVER use Bootstrap native attributes:**
```html
<!-- ❌ WRONG (Bootstrap 4/5 native) -->
<button data-dismiss="modal">Close</button>
<button data-bs-dismiss="modal">Close</button>

<!-- ✅ CORRECT (Use centralized functions) -->
<button onclick="hideModal('modalId')">Close</button>
```

### AJAX Patterns
Use the centralized `performAjaxOperation` function:
```javascript
performAjaxOperation({
    type: "POST",
    url: route_url,
    data: form_data,
    dataType: "json",
    loaderMessage: 'Processing...',
    showSuccessNotification: true,
    success: function(response) {
        // Handle success
    }
});
```

### Blade Layout Structure
```
layouts/
├── app.blade.php           # Admin portal layout (with sidebar)
└── customer.blade.php      # Customer portal layout (no sidebar)

common/
├── head.blade.php          # Admin portal head (SB Admin 2 theme)
├── customer-head.blade.php # Customer portal head (WebMonks branding)
├── header.blade.php        # Admin header with user menu
├── sidebar.blade.php       # Admin navigation menu
├── footer.blade.php        # Footer with common modals
└── alert.blade.php         # Flash message alerts

customer/partials/
├── header.blade.php        # Customer portal header
├── footer.blade.php        # Customer portal footer  
└── logout-modal.blade.php  # Customer logout modal
```

### CSS/JS Organization by Portal
**Admin Portal (Bootstrap 4):**
- `resources/sass/app.scss` → compiled to `css/app.css`
- `admin/css/sb-admin-2.min.css` (Bootstrap 4 based theme)
- `admin/js/sb-admin-2.min.js` (theme JavaScript)
- `admin/toastr/toastr.css` (notifications)
- External: Font Awesome, Google Fonts, Select2, DatePicker

**Customer Portal (Bootstrap 5):**
- Bootstrap 5 CDN
- Custom WebMonks branding (inline CSS in customer-head.blade.php)
- `css/customer-portal.css` (dedicated customer styles)
- Minimal dependencies: only Toastr for notifications

**⚠️ Bootstrap Version Inconsistency:**
- Admin: Bootstrap 4 (via SB Admin 2)
- Customer: Bootstrap 5 (CDN)
- **Recommendation**: Upgrade admin to Bootstrap 5 or standardize on Bootstrap 4

---

## Database Guidelines

### Required Table Structure
Every table MUST include:
```sql
-- Primary key
id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT

-- Business fields
-- (entity-specific columns)

-- Status (REQUIRED)
status ENUM('Active', 'Inactive') DEFAULT 'Active'

-- Audit trail (REQUIRED)
created_by BIGINT UNSIGNED NULL
updated_by BIGINT UNSIGNED NULL  
deleted_by BIGINT UNSIGNED NULL

-- Timestamps (REQUIRED)
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL
deleted_at TIMESTAMP NULL (for soft deletes)

-- Foreign Keys (REQUIRED)
FOREIGN KEY (created_by) REFERENCES users(id)
FOREIGN KEY (updated_by) REFERENCES users(id)
FOREIGN KEY (deleted_by) REFERENCES users(id)
```

### Naming Conventions
- Tables: `snake_case` (plural) - `customers`, `customer_insurances`
- Columns: `snake_case` - `mobile_number`, `date_of_birth`
- Foreign Keys: `[table_singular]_id` - `customer_id`, `insurance_company_id`
- Indexes: `[table]_[column]_index`
- Unique Keys: `[table]_[column]_unique`

### Relationships
```php
// One-to-Many
public function quotations(): HasMany
{
    return $this->hasMany(Quotation::class);
}

// Many-to-One  
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}

// Many-to-Many (with pivot)
public function insuranceCompanies(): BelongsToMany
{
    return $this->belongsToMany(InsuranceCompany::class, 'quotation_companies')
                ->withPivot('premium_amount', 'coverage_details')
                ->withTimestamps();
}
```

---

## Security Guidelines

### Authentication & Authorization

#### Admin Portal
```php
// Controller constructor pattern
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('permission:[entity]-list|[entity]-create|[entity]-edit|[entity]-delete', ['only' => ['index']]);
    $this->middleware('permission:[entity]-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:[entity]-edit', ['only' => ['edit', 'update']]);
    $this->middleware('permission:[entity]-delete', ['only' => ['delete']]);
}
```

#### Customer Portal
```php
// Use VerifyFamilyAccess middleware for family-shared resources
Route::middleware(['auth:customer', 'verify.family.access'])->group(function() {
    // Family-accessible routes
});
```

### Frontend Security (jQuery Usage)

#### Current Status: ACCEPTABLE with Security Measures
- **jQuery Version**: 3.7.1 (latest, receives security patches)
- **CSRF Protection**: Implemented via `meta[name="csrf-token"]`
- **Session Management**: Global AJAX error handling with timeout detection

#### Security Best Practices
```javascript
// ✅ CORRECT: Proper CSRF token handling
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
    }
});

// ✅ CORRECT: Safe HTML insertion using Blade escaping
$('#container').html('{{ $safeData }}');

// ❌ AVOID: Direct HTML construction with user data
$('#container').html('<div>' + userInput + '</div>'); // XSS risk

// ✅ CORRECT: Use text() for user content
$('#container').text(userInput);
```

#### XSS Prevention Guidelines
1. **Always escape user data** in Blade templates with `{{ }}` not `{!! !!}`
2. **Use `.text()` over `.html()`** when inserting user-provided content
3. **Validate all inputs** server-side with Form Requests
4. **Consider CSP headers** for additional protection
5. **Audit dynamic HTML construction** for potential XSS vectors

### Data Validation
- **ALWAYS** use Form Requests for validation
- **NEVER** trust user input without validation
- Use Laravel's built-in validation rules when possible

### SQL Security
- **ALWAYS** use Eloquent ORM or Query Builder
- **NEVER** use raw SQL with user input
- Use parameter binding for any raw queries

### File Uploads
```php
// Use centralized FileUploadService
public function __construct(private FileUploadService $fileUploadService) {}

// In controller methods
$filePath = $this->fileUploadService->upload($request->file('document'), 'customers');
```

---

## Testing Guidelines

### Test Structure
```
tests/
├── Feature/            # Integration tests
│   ├── Admin/         # Admin portal tests
│   └── Customer/      # Customer portal tests
└── Unit/              # Unit tests
    ├── Models/        # Model tests
    └── Services/      # Service tests
```

### Test Patterns
```php
class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }
    
    public function test_can_list_customers()
    {
        Customer::factory()->count(3)->create();
        
        $response = $this->get(route('customers.index'));
        
        $response->assertStatus(200)
                 ->assertViewIs('customers.index')
                 ->assertViewHas('customers');
    }
}
```

### Required Test Coverage
- All controller methods
- Model relationships and scopes
- Form request validation
- Service classes business logic
- Authentication and authorization

---

## Deployment Guidelines

### Environment Configuration
```bash
# Required environment variables
APP_NAME="Insurance Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=insurance_db
DB_USERNAME=username
DB_PASSWORD=password

# WhatsApp API Configuration
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=
```

### Build Process
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production

# Build assets
npm run production

# Setup application
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database setup
php artisan migrate --force
php artisan db:seed --class=UserSeeder
```

### Performance Optimization
- Enable OPcache in production
- Use Redis for sessions and cache
- Enable Gzip compression
- Optimize images and assets

---

## Architecture Improvements

### 🎯 **Phase 1: Service Layer Enhancement (High Priority)**

#### 1.1 Create Service Interfaces
```php
interface QuotationServiceInterface
{
    public function createQuotation(array $data): Quotation;
    public function generateCompanyQuotes(Quotation $quotation): void;
    public function calculatePremium(array $data): float;
}

interface CustomerRepositoryInterface
{
    public function findByFamily(int $familyGroupId): Collection;
    public function findWithPolicies(int $customerId): Customer;
}
```

#### 1.2 Recommended Service Expansion
```
Services/
├── Core/
│   ├── AuthenticationService
│   ├── NotificationService
│   └── AuditService
├── Business/
│   ├── CustomerService
│   ├── PolicyService
│   └── QuotationService ✓
├── Integration/
│   ├── WhatsAppService
│   ├── EmailService
│   └── PaymentService
└── Infrastructure/
    ├── FileUploadService ✓
    ├── PdfGenerationService ✓
    └── CacheService
```

### 🔧 **Phase 2: Architecture Modernization (Medium Priority)**

#### 2.1 Frontend Upgrade Strategy
- **Option A**: Migrate to Vue.js 3 (recommended)
- **Option B**: Migrate to React 18 with TypeScript
- **Option C**: Implement Inertia.js for seamless Laravel-Vue integration

#### 2.2 API Layer Development
```php
// Add API versioning and proper REST endpoints
Route::prefix('api/v1')->group(function () {
    Route::apiResource('quotations', QuotationApiController::class);
    Route::apiResource('customers', CustomerApiController::class);
});
```

#### 2.3 Event-Driven Architecture
```php
// Implement domain events
class QuotationCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}

class SendQuotationNotification implements ShouldQueue
{
    public function handle(QuotationCreated $event): void
    {
        // Handle notification sending
    }
}
```

### 🚀 **Phase 3: Performance & Scalability (Low Priority)**

#### 3.1 Caching Strategy
```php
// Implement multi-layer caching
- Application Cache (Redis/Memcached)
- Database Query Caching
- HTTP Response Caching
- CDN for static assets
```

#### 3.2 Database Optimization
- Add missing foreign key constraints
- Optimize database indices
- Implement read/write database splitting
- Add database connection pooling

#### 3.3 Monitoring & Observability
```php
// Add comprehensive logging and monitoring
- Application Performance Monitoring (APM)
- Database query monitoring
- Error tracking (Sentry/Bugsnag)
- Business metrics dashboard
```

### Implementation Roadmap

#### **Quarter 1: Service Layer Foundation**
- ✅ Create service interfaces and contracts
- ✅ Extract business logic from controllers
- ✅ Implement repository pattern
- ✅ Add comprehensive unit testing

#### **Quarter 2: Architecture Modernization**
- 🔄 Frontend framework upgrade
- 🔄 API layer implementation
- 🔄 Event-driven architecture
- 🔄 Database constraints addition

#### **Quarter 3: Performance & Scalability**
- 🔄 Implement caching strategy
- 🔄 Database optimization
- 🔄 Monitoring and observability
- 🔄 Load testing and optimization

#### **Quarter 4: Advanced Features**
- 🔄 Microservices evaluation
- 🔄 Mobile app API preparation
- 🔄 Advanced security features
- 🔄 Business intelligence integration

---

## Code Review Checklist

### ✅ Architecture Compliance
- [ ] Follows dual portal pattern (admin/customer separation)
- [ ] Uses appropriate authentication system
- [ ] Implements proper middleware and permissions
- [ ] Follows established naming conventions

### ✅ Database Standards  
- [ ] Migration includes all required fields (audit trail, soft deletes)
- [ ] Model uses SoftDeletes and LogsActivity traits
- [ ] Relationships are properly defined
- [ ] Foreign key constraints are in place

### ✅ Controller Standards
- [ ] Extends base Controller class
- [ ] Uses dependency injection properly
- [ ] Has all required methods (CRUD + export + status update)
- [ ] Implements proper error handling
- [ ] Uses Form Requests for validation

### ✅ Frontend Standards
- [ ] Uses centralized modal functions (not Bootstrap native)
- [ ] Follows Blade template structure
- [ ] Uses performAjaxOperation for AJAX calls
- [ ] Proper error handling and user feedback

### ✅ Security Standards
- [ ] Input validation via Form Requests
- [ ] Proper authorization middleware
- [ ] CSRF protection enabled
- [ ] SQL injection prevention (ORM usage)
- [ ] File upload security (if applicable)

### ✅ Code Quality
- [ ] Follows PSR standards
- [ ] Proper PHPDoc comments
- [ ] No hardcoded values
- [ ] Error handling implemented
- [ ] Testing coverage adequate

### ✅ Business Logic
- [ ] Audit trail implementation
- [ ] Status management
- [ ] Soft deletes where required
- [ ] Export functionality
- [ ] WhatsApp integration (if needed)

---

## Quick Reference Commands

### Development Commands
```bash
# Laravel
php artisan serve
php artisan migrate --seed
php artisan route:list
php artisan permission:cache-reset

# Frontend  
npm run dev
npm run watch
npm run prod

# Testing
php artisan test
./vendor/bin/phpunit --coverage-html reports/

# Cache Management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Code Generation Templates
```bash
# Create new module (manual creation following patterns above)
# 1. Create migration: php artisan make:migration create_[entities]_table
# 2. Create model: php artisan make:model [Entity] 
# 3. Create controller: php artisan make:controller [Entity]Controller
# 4. Create requests: php artisan make:request Store[Entity]Request
# 5. Create export: php artisan make:export [Entity]Export
# 6. Add routes, views, sidebar navigation
```

---

## Troubleshooting Common Issues

### Modal Close Buttons Not Working
- **Issue**: Using Bootstrap 4 `data-dismiss="modal"` with Bootstrap 5
- **Solution**: Use centralized `hideModal('modalId')` function

### Permission Errors  
- **Issue**: 403 Unauthorized errors
- **Solution**: Check middleware permissions and role assignments
```bash
php artisan permission:cache-reset
```

### Database Migration Issues
- **Issue**: Foreign key constraint errors
- **Solution**: Ensure proper migration order and foreign key definitions

### WhatsApp API Integration
- **Issue**: WhatsApp messages not sending
- **Solution**: Check API configuration and network connectivity

### Session Issues
- **Issue**: Users getting logged out unexpectedly  
- **Solution**: Check session configuration and middleware setup

---

**Last Updated**: September 2024  
**Version**: 1.0  
**Maintainer**: Development Team

> This document should be referenced for ALL development work on this project. Any deviations from these guidelines must be discussed and approved by the team lead.