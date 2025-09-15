# Developer Onboarding Guide - Laravel Insurance Management System

## Table of Contents
1. [Project Overview & Technology Stack](#project-overview--technology-stack)
2. [Development Environment Setup](#development-environment-setup)
3. [Database Schema Overview](#database-schema-overview)
4. [Development Workflows](#development-workflows)
5. [Feature Development Guide](#feature-development-guide)
6. [Testing Strategies](#testing-strategies)
7. [Deployment & Configuration](#deployment--configuration)
8. [Troubleshooting & Maintenance](#troubleshooting--maintenance)
9. [Security Guidelines](#security-guidelines)
10. [Performance Optimization](#performance-optimization)
11. [Code Review Checklist](#code-review-checklist)

---

## Project Overview & Technology Stack

### Application Purpose
This is a comprehensive insurance management system designed for managing insurance quotations, customers, policies, and related business entities. It includes both an admin interface for staff and a customer portal for policy holders.

### Core Technology Stack

#### Backend
- **Framework**: Laravel 10.x
- **PHP Version**: 8.1+
- **Database**: MySQL 8+
- **Authentication**: Laravel Auth + Custom Customer Auth
- **Authorization**: Spatie Laravel Permission

#### Frontend
- **JavaScript Framework**: Vue.js 2
- **CSS Framework**: Bootstrap 5
- **Build Tool**: Laravel Mix (Webpack)
- **UI Components**: Custom Blade templates + Vue components

#### Key Dependencies
```json
{
  "spatie/laravel-permission": "Role and permission management",
  "spatie/laravel-activitylog": "Comprehensive audit logging",
  "barryvdh/laravel-dompdf": "PDF generation",
  "maatwebsite/excel": "Excel import/export",
  "opcodesio/log-viewer": "Log management interface"
}
```

### Application Architecture
- **Pattern**: MVC with Repository Pattern (implied)
- **Structure**: Multi-tenant support via Family Groups
- **Authentication**: Dual authentication systems (Admin + Customer)
- **Audit Trail**: Complete action logging with Spatie ActivityLog

---

## Development Environment Setup

### Prerequisites
- PHP 8.1+ with extensions: `mbstring`, `xml`, `bcmath`, `pdo_mysql`, `gd`
- MySQL 8.0+ or MariaDB 10.3+
- Node.js 16+ and npm
- Composer 2.x

### Initial Setup Steps

#### 1. Clone and Configure
```bash
# Clone the repository
git clone [repository-url]
cd admin-panel

# Copy environment file
cp .env.example .env
```

#### 2. Environment Configuration
Edit `.env` file with your local settings:
```env
APP_NAME="Insurance Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=insurance_system
DB_USERNAME=root
DB_PASSWORD=your_password

# Mail Configuration (for testing)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="admin@insurance.local"
MAIL_FROM_NAME="${APP_NAME}"
```

#### 3. Installation Commands
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Generate application key
php artisan key:generate

# Create database and run migrations with seed data
php artisan migrate --seed

# Generate IDE helper files (recommended for development)
php artisan ide-helper:generate
php artisan ide-helper:models

# Compile frontend assets
npm run dev

# Start development server
php artisan serve
```

#### 4. Default Login Credentials
- **Admin Portal**: `admin@admin.com` / `Admin@123#`
- **Log Viewer**: Available at `/webmonks-log-viewer`

### Development Scripts
The `composer.json` includes helpful scripts:

```bash
# Quick development setup
composer run dev-setup

# Start development with auto-reload
composer run dev
```

---

## Database Schema Overview

### Core Entities and Relationships

#### User Management
```
Users (Admin Staff)
├── Roles (via Spatie Permission)
└── Permissions (via Spatie Permission)

Customers (Insurance Clients)
├── FamilyGroups (1:many)
├── FamilyMembers (many:many via family_groups)
├── CustomerInsurances (1:many)
└── CustomerAuditLog (1:many)
```

#### Insurance Business Logic
```
InsuranceCompanies
├── Quotations (1:many)
├── CustomerInsurances (1:many)
└── AddonCovers (many:many)

Quotations
├── QuotationCompanies (1:many comparison quotes)
├── Customers (many:1)
└── PolicyTypes (many:1)

CustomerInsurances (Active Policies)
├── Customers (many:1)
├── InsuranceCompanies (many:1)
├── Renewals (tracked via dates)
└── Documents (file attachments)
```

#### Business Support Entities
- **Brokers**: Insurance intermediaries
- **Branches**: Office locations
- **PolicyTypes**: Insurance product categories
- **PremiumTypes**: Pricing structures
- **FuelTypes**: Vehicle-specific data
- **ReferenceUsers**: Contact management
- **RelationshipManagers**: Account management

### Key Schema Patterns

#### Audit Trail Pattern
All models include:
```php
// Database columns
'created_by', 'updated_by', 'deleted_by'
'created_at', 'updated_at', 'deleted_at'

// Model traits
use SoftDeletes;
use TableRecordObserver; // Custom audit trait
```

#### Family Group Pattern
```php
// Customers can belong to family groups
Customer -> FamilyGroup -> [FamilyMembers]
// One family head manages multiple family members
// Shared access to policies and documents
```

### Important Schema Notes
- Uses **enum columns** (may cause issues with some schema introspection tools)
- **Soft deletes** enabled on most entities
- **Foreign key constraints** properly configured
- **Indexes** on frequently queried columns (customer_id, insurance_company_id, etc.)

---

## Development Workflows

### Coding Standards

#### PHP Standards (Laravel)
```php
// Naming Conventions
class CustomerController extends Controller  // PascalCase for classes
public function storeCustomer()             // camelCase for methods
$customer_id                                // snake_case for variables
$this->customerService                      // camelCase for properties

// Model Conventions
class Customer extends Model
{
    use SoftDeletes, TableRecordObserver;

    protected $fillable = ['name', 'email', 'phone'];
    protected $guarded = ['id', 'created_by'];

    // Relationships use descriptive names
    public function familyGroup()
    public function customerInsurances()
}

// Controller Patterns
class CustomerController extends Controller
{
    // Standard CRUD methods
    public function index()     // List view
    public function create()    // Create form
    public function store()     // Save new record
    public function show($id)   // Detail view
    public function edit($id)   // Edit form
    public function update($id) // Save changes
    public function destroy($id)// Soft delete

    // Export functionality
    public function export()    // Excel export

    // Status updates
    public function updateStatus($id, $status)
}
```

#### Frontend Standards (Vue.js 2)
```javascript
// Component naming: PascalCase
Vue.component('CustomerForm', {
    // Use props for parent communication
    props: ['customer', 'readonly'],

    // Use events for child-to-parent communication
    methods: {
        saveCustomer() {
            this.$emit('customer-saved', this.customer);
        }
    }
});

// Use consistent data structures
data() {
    return {
        customer: {
            name: '',
            email: '',
            phone: ''
        },
        loading: false,
        errors: {}
    };
}
```

### Git Workflow

#### Branch Strategy
```bash
# Main branches
main          # Production-ready code
develop       # Integration branch for features

# Feature branches
feature/customer-family-management
feature/quotation-comparison
bugfix/authentication-timeout
hotfix/pdf-generation-error
```

#### Commit Standards
```bash
# Format: type(scope): description

feat(customer): add family group management
fix(auth): resolve customer session timeout
docs(api): update quotation endpoints
refactor(export): optimize Excel generation
test(customer): add integration tests
```

### Code Organization Principles

#### Directory Structure Logic
```
app/
├── Http/Controllers/
│   ├── Admin/           # Admin-specific controllers
│   ├── Auth/            # Authentication controllers
│   ├── Api/             # API controllers
│   └── Security/        # Security-related controllers
├── Models/              # Eloquent models
├── Services/            # Business logic services
├── Exports/             # Excel export classes
└── Mail/                # Email templates and logic
```

#### Service Layer Pattern
```php
// Services contain complex business logic
app/Services/
├── QuotationService.php      # Quote generation and comparison
├── PdfGenerationService.php  # PDF handling
├── FileUploadService.php     # File management
└── CustomerPortalService.php # Customer-specific operations
```

---

## Feature Development Guide

### Adding New Business Entity

#### Step 1: Database Migration
```php
// Create migration
php artisan make:migration create_new_entities_table

// Migration template with audit fields
Schema::create('new_entities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();

    // Audit fields (required pattern)
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->unsignedBigInteger('deleted_by')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Foreign keys
    $table->foreign('created_by')->references('id')->on('users');
    $table->foreign('updated_by')->references('id')->on('users');
    $table->foreign('deleted_by')->references('id')->on('users');
});
```

#### Step 2: Eloquent Model
```php
// Create model
php artisan make:model NewEntity

// Model template
class NewEntity extends Model
{
    use SoftDeletes, TableRecordObserver;

    protected $fillable = [
        'name', 'description'
    ];

    protected $guarded = [
        'id', 'created_by', 'updated_by', 'deleted_by'
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes for common queries
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
```

#### Step 3: Controller with Standard Methods
```php
// Create controller
php artisan make:controller NewEntityController --resource

class NewEntityController extends Controller
{
    // List with export capability
    public function index(Request $request)
    {
        $entities = NewEntity::query()
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('new-entities.index', compact('entities'));
    }

    // Export functionality (required pattern)
    public function export(Request $request)
    {
        return Excel::download(
            new NewEntityExport($request),
            'new-entities-' . date('Y-m-d') . '.xlsx'
        );
    }

    // Status update (common pattern)
    public function updateStatus($id, $status)
    {
        $entity = NewEntity::findOrFail($id);
        $entity->update(['status' => $status]);

        return response()->json(['message' => 'Status updated']);
    }
}
```

#### Step 4: Form Requests for Validation
```php
// Create form requests
php artisan make:request StoreNewEntityRequest
php artisan make:request UpdateNewEntityRequest

class StoreNewEntityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:new_entities',
            'description' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'This entity name already exists.'
        ];
    }
}
```

#### Step 5: Excel Export Class
```php
// Create export
php artisan make:export NewEntityExport

class NewEntityExport implements FromQuery, WithHeadings, WithMapping
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return NewEntity::query()
            ->with(['createdBy'])
            ->when($this->request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Description', 'Created By', 'Created At'];
    }

    public function map($entity): array
    {
        return [
            $entity->id,
            $entity->name,
            $entity->description,
            $entity->createdBy->name ?? '',
            $entity->created_at->format('Y-m-d H:i:s')
        ];
    }
}
```

#### Step 6: Routes Registration
```php
// In routes/web.php
Route::resource('new-entities', NewEntityController::class);
Route::get('new-entities/export', [NewEntityController::class, 'export'])->name('new-entities.export');
Route::patch('new-entities/{id}/status/{status}', [NewEntityController::class, 'updateStatus']);
```

#### Step 7: Blade Views
```php
// resources/views/new-entities/index.blade.php
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>New Entities</h5>
        <div>
            <a href="{{ route('new-entities.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export
            </a>
            <a href="{{ route('new-entities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search form -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <!-- Data table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entities as $entity)
                    <tr>
                        <td>{{ $entity->id }}</td>
                        <td>{{ $entity->name }}</td>
                        <td>{{ $entity->description }}</td>
                        <td>{{ $entity->createdBy->name ?? '' }}</td>
                        <td>
                            <a href="{{ route('new-entities.edit', $entity) }}"
                               class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST"
                                  action="{{ route('new-entities.destroy', $entity) }}"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $entities->links() }}
    </div>
</div>
@endsection
```

### Creating Custom Middleware

#### Example: Family Access Verification
```php
// Create middleware
php artisan make:middleware VerifyFamilyAccess

class VerifyFamilyAccess
{
    public function handle($request, Closure $next)
    {
        $customer = auth('customer')->user();
        $requestedCustomerId = $request->route('customer_id');

        // Check if customer has access to requested family member's data
        if (!$customer->canAccessFamilyMember($requestedCustomerId)) {
            return redirect()->route('customer.dashboard')
                           ->with('error', 'Access denied.');
        }

        return $next($request);
    }
}

// Register in Kernel.php
protected $routeMiddleware = [
    'verify.family.access' => VerifyFamilyAccess::class,
];

// Use in routes
Route::middleware(['auth:customer', 'verify.family.access'])
     ->get('/customer/{customer_id}/policies', [CustomerPortalController::class, 'policies']);
```

### Building Export Functionality

#### Standard Export Pattern
```php
// Controller method
public function export(Request $request)
{
    // Validate export parameters
    $request->validate([
        'format' => 'in:xlsx,csv',
        'date_from' => 'date|nullable',
        'date_to' => 'date|nullable'
    ]);

    $format = $request->get('format', 'xlsx');
    $filename = 'customers-' . date('Y-m-d') . '.' . $format;

    return Excel::download(
        new CustomerExport($request),
        $filename
    );
}

// Export class with advanced filtering
class CustomerExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    private $filters;

    public function __construct($request)
    {
        $this->filters = $request->all();
    }

    public function query()
    {
        return Customer::query()
            ->with(['familyGroup', 'createdBy'])
            ->when($this->filters['search'] ?? null, function ($q, $search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($this->filters['date_from'] ?? null, function ($q, $date) {
                $q->whereDate('created_at', '>=', $date);
            })
            ->when($this->filters['date_to'] ?? null, function ($q, $date) {
                $q->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc');
    }

    // Apply styling for professional exports
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => ['alignment' => ['wrapText' => true]]
        ];
    }
}
```

---

## Testing Strategies

### Testing Environment Setup
```php
// phpunit.xml configuration highlights
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_DRIVER" value="array"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="QUEUE_DRIVER" value="sync"/>
```

### Unit Testing Patterns

#### Model Testing
```php
// tests/Unit/Models/CustomerTest.php
class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_belongs_to_family_group()
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer = Customer::factory()->create([
            'family_group_id' => $familyGroup->id
        ]);

        $this->assertInstanceOf(FamilyGroup::class, $customer->familyGroup);
        $this->assertEquals($familyGroup->id, $customer->familyGroup->id);
    }

    public function test_customer_can_access_family_member_data()
    {
        $familyHead = Customer::factory()->create();
        $familyMember = Customer::factory()->create([
            'family_group_id' => $familyHead->family_group_id
        ]);

        $this->assertTrue($familyHead->canAccessFamilyMember($familyMember->id));
    }
}
```

#### Service Testing
```php
// tests/Unit/Services/QuotationServiceTest.php
class QuotationServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuotationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuotationService();
    }

    public function test_generates_quotation_with_multiple_companies()
    {
        $companies = InsuranceCompany::factory(3)->create();
        $customer = Customer::factory()->create();

        $quotation = $this->service->generateQuotation($customer->id, [
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'policy_type_id' => 1
        ]);

        $this->assertDatabaseHas('quotations', [
            'customer_id' => $customer->id,
            'vehicle_make' => 'Toyota'
        ]);

        $this->assertCount(3, $quotation->quotationCompanies);
    }
}
```

### Feature Testing

#### Authentication Testing
```php
// tests/Feature/Auth/CustomerAuthTest.php
class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_login_with_family_access()
    {
        $customer = Customer::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/customer/login', [
            'email' => $customer->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_family_member_can_access_shared_data()
    {
        $familyHead = Customer::factory()->create();
        $familyMember = Customer::factory()->create([
            'family_group_id' => $familyHead->family_group_id
        ]);

        $this->actingAs($familyHead, 'customer');

        $response = $this->get("/customer/{$familyMember->id}/policies");

        $response->assertOk();
    }
}
```

#### Controller Testing
```php
// tests/Feature/Controllers/CustomerControllerTest.php
class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_can_create_customer()
    {
        $customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post(route('customers.store'), $customerData);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', $customerData);
    }

    public function test_can_export_customers()
    {
        Customer::factory(5)->create();

        $response = $this->get(route('customers.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
```

### Browser Testing with Laravel Dusk

#### Installation and Setup
```bash
# Install Dusk
composer require --dev laravel/dusk
php artisan dusk:install

# Configure environment
cp .env.dusk.local.example .env.dusk.local
```

#### Customer Portal E2E Tests
```php
// tests/Browser/CustomerPortalTest.php
class CustomerPortalTest extends DuskTestCase
{
    public function test_customer_can_view_policies()
    {
        $customer = Customer::factory()->create();
        CustomerInsurance::factory(3)->create(['customer_id' => $customer->id]);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer, 'customer')
                   ->visit('/customer/dashboard')
                   ->assertSee('My Policies')
                   ->clickLink('View Policies')
                   ->assertPathIs('/customer/policies')
                   ->assertSeeIn('.policy-list', 'Policy Number');
        });
    }
}
```

### Test Data Management

#### Factory Patterns
```php
// database/factories/CustomerFactory.php
class CustomerFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'family_group_id' => FamilyGroup::factory(),
            'created_by' => User::factory()
        ];
    }

    // State methods for specific scenarios
    public function withFamily()
    {
        return $this->state(function (array $attributes) {
            $familyGroup = FamilyGroup::factory()->create();
            return ['family_group_id' => $familyGroup->id];
        });
    }

    public function withInsurances($count = 2)
    {
        return $this->afterCreating(function (Customer $customer) use ($count) {
            CustomerInsurance::factory($count)->create([
                'customer_id' => $customer->id
            ]);
        });
    }
}
```

---

## Deployment & Configuration

### Production Environment Setup

#### Server Requirements
- **Web Server**: Nginx or Apache with PHP-FPM
- **PHP**: 8.1+ with required extensions
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Cache**: Redis (recommended) or Memcached
- **Queue**: Redis or database-based
- **Storage**: Local filesystem or S3-compatible

#### Production Configuration

#### Environment Variables (.env.production)
```env
APP_NAME="Insurance Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Security
APP_KEY=base64:your-32-character-secret-key

# Database
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=insurance_prod
DB_USERNAME=insurance_user
DB_PASSWORD=secure-password

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=secure-redis-password
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"

# File Storage
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name

# Activity Logging
ACTIVITY_LOGGER_ENABLED=true

# Security Headers
CSP_ENABLED=true
CSP_REPORT_ONLY=false
SECURITY_LOG_CSP_VIOLATIONS=true
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/insurance/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Prevent execution of PHP in uploads directory
        location ~ ^/storage/.*\.php$ {
            deny all;
        }
    }

    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(vendor|storage|bootstrap/cache) {
        deny all;
    }
}
```

#### Deployment Script
```bash
#!/bin/bash
# deploy.sh

set -e

echo "Starting deployment..."

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Clear and optimize caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations (with backup first)
php artisan backup:run
php artisan migrate --force

# Compile assets
npm ci
npm run production

# Update permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
sudo supervisorctl restart insurance-worker:*

echo "Deployment completed successfully!"
```

#### Queue Worker Configuration (Supervisor)
```ini
# /etc/supervisor/conf.d/insurance-worker.conf
[program:insurance-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/insurance/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=/var/www/insurance
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/insurance/storage/logs/worker.log
stopwaitsecs=3600
```

#### Database Backup Configuration
```bash
# Add to crontab: crontab -e
# Database backup every 6 hours
0 */6 * * * /usr/bin/mysqldump -u backup_user -p'backup_password' insurance_prod > /backups/insurance_$(date +\%Y\%m\%d_\%H\%M\%S).sql

# File backup daily
0 2 * * * rsync -av --delete /var/www/insurance/storage/app/public/ /backups/files/

# Log rotation
0 0 * * 0 find /var/www/insurance/storage/logs -name "*.log" -mtime +30 -delete
```

---

## Troubleshooting & Maintenance

### Common Issues and Solutions

#### Database Issues

**Issue**: Migration fails with foreign key constraints
```bash
# Solution: Check dependencies and run in correct order
php artisan migrate:status
php artisan migrate:rollback --step=1
# Fix migration dependencies
php artisan migrate
```

**Issue**: Enum column type issues with IDE helper
```bash
# Solution: Update doctrine/dbal or exclude enum columns
# In config/ide-helper.php
'ignored_columns' => ['enum_column_name']
```

#### Authentication Issues

**Issue**: Customer session timeout too aggressive
```php
// Check config/session.php
'lifetime' => 120, // Increase if needed

// Check middleware
// In app/Http/Middleware/SecureSession.php
private $maxInactiveMinutes = 30; // Adjust as needed
```

**Issue**: Family access not working properly
```php
// Debug family group relationships
Customer::with('familyGroup.familyMembers')->find($customer_id);

// Check middleware logic
// Verify VerifyFamilyAccess middleware is properly registered
```

#### Performance Issues

**Issue**: Slow page loads with large datasets
```php
// Solution 1: Add database indexes
Schema::table('customers', function (Blueprint $table) {
    $table->index(['created_at', 'status']);
    $table->index('family_group_id');
});

// Solution 2: Implement pagination
$customers = Customer::paginate(20);

// Solution 3: Use eager loading
$customers = Customer::with(['familyGroup', 'customerInsurances'])->paginate(20);
```

**Issue**: Memory exhaustion during Excel exports
```php
// Solution: Use chunked exports
public function query()
{
    return Customer::query()->limit(5000);
}

// Or implement streaming
class CustomerStreamExport implements FromQuery, ShouldQueue
{
    public function chunkSize(): int
    {
        return 1000;
    }
}
```

#### File Upload Issues

**Issue**: PDF generation fails
```php
// Check DomPDF configuration
// config/dompdf.php
'paper_size' => 'a4',
'orientation' => 'portrait',
'enable_php' => true,

// Debug PDF generation
try {
    $pdf = PDF::loadView('pdfs.quotation', $data);
    return $pdf->download();
} catch (Exception $e) {
    Log::error('PDF Generation Failed: ' . $e->getMessage());
}
```

**Issue**: File storage permissions
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Check symbolic link
php artisan storage:link
```

### Maintenance Tasks

#### Daily Maintenance
```bash
#!/bin/bash
# daily-maintenance.sh

# Clean expired sessions
php artisan session:gc

# Clear old logs (keep 30 days)
find storage/logs -name "*.log" -mtime +30 -delete

# Backup database
mysqldump -u user -p password database > /backups/daily-$(date +%Y%m%d).sql

# Check disk space
df -h | awk '$5 > "85%" {print $0}' | mail -s "Disk Space Alert" admin@company.com
```

#### Weekly Maintenance
```bash
#!/bin/bash
# weekly-maintenance.sh

# Optimize database tables
php artisan db:optimize-tables

# Clear application caches and rebuild
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update Composer dependencies (minor versions only)
composer update --with-dependencies --no-dev

# Rotate logs
php artisan log:clear --keep=4
```

#### Monthly Maintenance
```bash
#!/bin/bash
# monthly-maintenance.sh

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Archive old audit logs (older than 1 year)
php artisan audit:archive --older-than=365

# Database integrity check
php artisan db:check-integrity

# Security audit
composer audit
npm audit --audit-level=moderate
```

### Monitoring and Logging

#### Application Monitoring
```php
// Set up custom health checks
// routes/web.php
Route::get('/health', function () {
    $checks = [
        'database' => DB::connection()->getPdo() ? 'OK' : 'FAILED',
        'cache' => Cache::has('health-check') ? 'OK' : 'FAILED',
        'storage' => Storage::exists('test.txt') ? 'OK' : 'FAILED'
    ];

    $status = in_array('FAILED', $checks) ? 500 : 200;

    return response()->json($checks, $status);
});
```

#### Log Analysis Commands
```bash
# Monitor error logs in real-time
tail -f storage/logs/laravel.log | grep ERROR

# Search for specific errors
grep -r "SQLSTATE" storage/logs/

# Check authentication failures
grep "authentication" storage/logs/laravel.log

# Monitor performance
grep "slow query" storage/logs/laravel.log
```

#### Database Performance Monitoring
```sql
-- Check slow queries
SELECT * FROM information_schema.processlist
WHERE time > 30 ORDER BY time DESC;

-- Check table sizes
SELECT
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB'
FROM information_schema.tables
WHERE table_schema = 'insurance_prod'
ORDER BY (data_length + index_length) DESC;

-- Check index usage
SELECT
    table_name,
    index_name,
    cardinality,
    ROUND(stat_value * 100 / cardinality, 2) AS selectivity
FROM information_schema.statistics
WHERE table_schema = 'insurance_prod';
```

---

## Security Guidelines

### Authentication Security

#### Password Policies
```php
// config/auth.php - Enhance password rules
'password_timeout' => 10800, // 3 hours

// Implement strong password rules
// In form requests
public function rules()
{
    return [
        'password' => [
            'required',
            'string',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'confirmed'
        ]
    ];
}
```

#### Rate Limiting
```php
// config/auth.php
'rate_limiting' => [
    'login_attempts' => 5,
    'lockout_duration' => 900, // 15 minutes
    'throttle_key' => 'email'
]

// Custom rate limiter middleware
class SecurityRateLimiter
{
    public function handle($request, Closure $next)
    {
        $key = 'login_attempts_' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (Cache::has($key) && Cache::get($key) >= $maxAttempts) {
            return response()->json([
                'error' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        return $next($request);
    }
}
```

#### Session Security
```php
// config/session.php
'encrypt' => true,
'http_only' => true,
'same_site' => 'strict',
'secure' => env('SESSION_SECURE_COOKIE', true),
'lifetime' => env('SESSION_LIFETIME', 120)
```

### Data Protection

#### Input Validation & Sanitization
```php
// Always use Form Requests for validation
class StoreCustomerRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        // Sanitize input before validation
        $this->merge([
            'phone' => preg_replace('/[^0-9+\-\(\)\s]/', '', $this->phone),
            'name' => strip_tags($this->name)
        ]);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|regex:/^[\+]?[0-9\-\(\)\s]+$/',
            'date_of_birth' => 'required|date|before:today'
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Name must contain only letters and spaces.',
            'phone.regex' => 'Please provide a valid phone number.'
        ];
    }
}
```

#### SQL Injection Prevention
```php
// Always use Eloquent ORM or Query Builder
// ✅ GOOD - Using Eloquent
$customers = Customer::where('name', 'like', '%' . $search . '%')->get();

// ✅ GOOD - Using Query Builder with bindings
$customers = DB::table('customers')
    ->where('name', 'like', '%' . $search . '%')
    ->get();

// ❌ BAD - Raw SQL with concatenation
$customers = DB::select("SELECT * FROM customers WHERE name LIKE '%" . $search . "%'");

// ✅ ACCEPTABLE - Raw SQL with bindings
$customers = DB::select("SELECT * FROM customers WHERE name LIKE ?", ['%' . $search . '%']);
```

#### XSS Prevention
```php
// In Blade templates, use {{ }} for auto-escaping
{{ $customer->name }} <!-- Automatically escaped -->
{!! $customer->description !!} <!-- Raw output - only when needed -->

// For HTML content, use Purifier
composer require mews/purifier

// In model or service
use Mews\Purifier\Facades\Purifier;

public function setDescriptionAttribute($value)
{
    $this->attributes['description'] = Purifier::clean($value);
}
```

#### File Upload Security
```php
// Secure file upload implementation
class FileUploadService
{
    private array $allowedTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    private int $maxFileSize = 5242880; // 5MB

    public function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        // Validate file type
        if (!in_array($file->getMimeType(), $this->allowedTypes)) {
            throw new InvalidArgumentException('File type not allowed.');
        }

        // Validate file size
        if ($file->getSize() > $this->maxFileSize) {
            throw new InvalidArgumentException('File size too large.');
        }

        // Generate secure filename
        $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs($directory, $filename, 'local');

        return $path;
    }
}
```

### Access Control

#### Role-Based Access Control (RBAC)
```php
// Database seeder for roles and permissions
class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.export',
            'quotations.view',
            'quotations.create',
            'reports.view',
            'admin.access'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $agentRole = Role::create(['name' => 'Agent']);

        // Assign permissions
        $adminRole->givePermissionTo(Permission::all());
        $managerRole->givePermissionTo([
            'customers.view', 'customers.create', 'customers.edit',
            'quotations.view', 'quotations.create',
            'reports.view'
        ]);
        $agentRole->givePermissionTo([
            'customers.view', 'customers.create',
            'quotations.view', 'quotations.create'
        ]);
    }
}

// In controllers - check permissions
class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customers.view')->only(['index', 'show']);
        $this->middleware('permission:customers.create')->only(['create', 'store']);
        $this->middleware('permission:customers.edit')->only(['edit', 'update']);
        $this->middleware('permission:customers.delete')->only(['destroy']);
        $this->middleware('permission:customers.export')->only(['export']);
    }
}

// In Blade templates - show/hide based on permissions
@can('customers.create')
    <a href="{{ route('customers.create') }}" class="btn btn-primary">Add Customer</a>
@endcan
```

#### API Security (if applicable)
```php
// API routes with Sanctum authentication
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('customers', CustomerApiController::class);
});

// API Rate limiting
// In RouteServiceProvider.php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// API controller with proper validation
class CustomerApiController extends Controller
{
    public function store(StoreCustomerRequest $request)
    {
        // Request is already validated by FormRequest
        $customer = Customer::create($request->validated());

        return response()->json($customer, 201);
    }
}
```

### Security Headers

#### Content Security Policy (CSP)
```php
// config/csp.php (if using spatie/laravel-csp)
return [
    'enabled' => env('CSP_ENABLED', true),
    'report_only' => env('CSP_REPORT_ONLY', false),

    'policy' => [
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
        'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
        'font-src' => "'self' https://fonts.gstatic.com",
        'img-src' => "'self' data: https:",
        'connect-src' => "'self'"
    ]
];

// Middleware to add security headers
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
```

---

## Performance Optimization

### Database Optimization

#### Query Optimization
```php
// ❌ N+1 Query Problem
$customers = Customer::all();
foreach ($customers as $customer) {
    echo $customer->familyGroup->name; // Generates N queries
}

// ✅ Eager Loading Solution
$customers = Customer::with('familyGroup')->get();
foreach ($customers as $customer) {
    echo $customer->familyGroup->name; // Single query
}

// ✅ Advanced eager loading with constraints
$customers = Customer::with([
    'familyGroup',
    'customerInsurances' => function ($query) {
        $query->where('status', 'active')->orderBy('created_at', 'desc');
    },
    'customerInsurances.insuranceCompany'
])->get();
```

#### Database Indexing Strategy
```sql
-- Customer search optimization
CREATE INDEX idx_customers_search ON customers (name, email, phone);
CREATE INDEX idx_customers_status_date ON customers (status, created_at);

-- Insurance policy lookups
CREATE INDEX idx_customer_insurances_customer_status ON customer_insurances (customer_id, status);
CREATE INDEX idx_customer_insurances_expiry ON customer_insurances (expiry_date, status);

-- Quotation queries
CREATE INDEX idx_quotations_customer_date ON quotations (customer_id, created_at);
CREATE INDEX idx_quotation_companies_quotation ON quotation_companies (quotation_id, premium_amount);

-- Activity log performance (very important for audit tables)
CREATE INDEX idx_activity_log_subject ON activity_log (subject_type, subject_id);
CREATE INDEX idx_activity_log_causer ON activity_log (causer_type, causer_id);
CREATE INDEX idx_activity_log_created ON activity_log (created_at);
```

#### Query Optimization Techniques
```php
// Use chunking for large datasets
Customer::chunk(1000, function ($customers) {
    foreach ($customers as $customer) {
        // Process customer
    }
});

// Use cursor for memory-efficient iteration
Customer::cursor()->each(function ($customer) {
    // Process customer with minimal memory usage
});

// Optimize counting queries
// ❌ Slow for large tables
$count = Customer::count();

// ✅ Better for paginated results
$customers = Customer::paginate(20);
$count = $customers->total();

// Use exists() instead of count() for existence checks
// ❌ Counts all records
if (CustomerInsurance::where('customer_id', $id)->count() > 0) {
    // Do something
}

// ✅ Stops at first match
if (CustomerInsurance::where('customer_id', $id)->exists()) {
    // Do something
}
```

### Caching Strategies

#### Application-Level Caching
```php
// Cache configuration
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'prefix' => env('CACHE_PREFIX', 'insurance_cache'),
    ]
]

// Service layer with caching
class CustomerService
{
    public function getCustomerWithInsurances($customerId)
    {
        $cacheKey = "customer_with_insurances_{$customerId}";

        return Cache::remember($cacheKey, now()->addHours(4), function () use ($customerId) {
            return Customer::with([
                'familyGroup',
                'customerInsurances.insuranceCompany',
                'customerInsurances' => function ($query) {
                    $query->where('status', 'active');
                }
            ])->findOrFail($customerId);
        });
    }

    public function clearCustomerCache($customerId)
    {
        Cache::forget("customer_with_insurances_{$customerId}");
        Cache::tags(['customer', "customer_{$customerId}"])->flush();
    }
}

// Cache invalidation in models
class Customer extends Model
{
    protected static function booted()
    {
        static::updated(function ($customer) {
            Cache::forget("customer_with_insurances_{$customer->id}");
        });
    }
}
```

#### View Caching
```php
// Cache expensive dashboard data
class DashboardController extends Controller
{
    public function index()
    {
        $dashboardData = Cache::remember('dashboard_stats', now()->addMinutes(30), function () {
            return [
                'total_customers' => Customer::count(),
                'active_policies' => CustomerInsurance::where('status', 'active')->count(),
                'expiring_soon' => CustomerInsurance::where('expiry_date', '<=', now()->addDays(30))->count(),
                'recent_quotations' => Quotation::with(['customer', 'quotationCompanies'])
                    ->latest()
                    ->limit(10)
                    ->get()
            ];
        });

        return view('dashboard', compact('dashboardData'));
    }
}
```

### Frontend Optimization

#### Asset Optimization
```javascript
// webpack.mix.js optimization
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
        processCssUrls: false,
        postCss: [
            require('autoprefixer')
        ]
   })
   .minify(['public/js/app.js', 'public/css/app.css'])
   .sourceMaps(true, 'source-map')
   .version(); // Cache busting

// Code splitting for large applications
mix.extract(['vue', 'jquery', 'bootstrap']);
```

#### Image Optimization
```php
// Optimize uploaded images
use Intervention\Image\Facades\Image;

class FileUploadService
{
    public function optimizeImage(UploadedFile $file): string
    {
        $image = Image::make($file);

        // Resize if too large
        if ($image->width() > 1200) {
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Optimize quality
        $filename = Str::random(32) . '.jpg';
        $path = storage_path('app/public/images/' . $filename);

        $image->encode('jpg', 85)->save($path);

        return 'images/' . $filename;
    }
}
```

#### Lazy Loading Implementation
```javascript
// Implement lazy loading for large tables
document.addEventListener('DOMContentLoaded', function() {
    const lazyTables = document.querySelectorAll('.lazy-table');

    const tableObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadTableData(entry.target);
                tableObserver.unobserve(entry.target);
            }
        });
    });

    lazyTables.forEach(table => tableObserver.observe(table));
});

function loadTableData(table) {
    const url = table.dataset.url;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            table.innerHTML = data.html;
        });
}
```

### Server-Level Optimization

#### PHP Optimization
```ini
; php.ini optimizations
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M

; OPcache configuration
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
```

#### Queue Optimization
```php
// Use queue for heavy operations
class GenerateQuotationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customerId;
    private $quotationData;

    public function __construct($customerId, $quotationData)
    {
        $this->customerId = $customerId;
        $this->quotationData = $quotationData;
    }

    public function handle(QuotationService $service)
    {
        $quotation = $service->generateQuotation($this->customerId, $this->quotationData);

        // Send notification when complete
        $customer = Customer::find($this->customerId);
        $customer->notify(new QuotationReadyNotification($quotation));
    }
}

// Dispatch in controller
public function generateQuotation(Request $request)
{
    $validated = $request->validate([
        'vehicle_make' => 'required|string',
        'vehicle_model' => 'required|string',
        // ... other fields
    ]);

    GenerateQuotationJob::dispatch(auth()->id(), $validated);

    return response()->json(['message' => 'Quotation generation started']);
}
```

---

## Code Review Checklist

### Security Review
- [ ] All user input is validated and sanitized
- [ ] SQL injection prevention (using Eloquent/Query Builder)
- [ ] XSS prevention (proper escaping in views)
- [ ] Authentication and authorization checks in place
- [ ] File uploads are properly validated and secured
- [ ] Sensitive data is not logged or exposed
- [ ] Rate limiting is implemented where needed
- [ ] CSRF protection is enabled on forms

### Code Quality Review
- [ ] Code follows PSR standards and Laravel conventions
- [ ] Proper error handling with try-catch blocks
- [ ] No hard-coded values (use config files or env variables)
- [ ] Database queries are optimized (no N+1 problems)
- [ ] Proper use of Eloquent relationships
- [ ] Code is properly documented with PHPDoc
- [ ] No code duplication (DRY principle)
- [ ] Single Responsibility Principle followed

### Database Review
- [ ] Migrations have proper rollback methods
- [ ] Foreign key constraints are defined
- [ ] Indexes are added for frequently queried columns
- [ ] Database queries are efficient
- [ ] Soft deletes are used where appropriate
- [ ] Audit trail fields are included (created_by, updated_by, deleted_by)

### Testing Review
- [ ] Unit tests cover critical business logic
- [ ] Feature tests cover main user workflows
- [ ] Edge cases are tested
- [ ] Database transactions are used in tests
- [ ] Test data is properly cleaned up
- [ ] Tests run in isolation and are repeatable

### Performance Review
- [ ] Eager loading is used to prevent N+1 queries
- [ ] Caching is implemented for expensive operations
- [ ] Large datasets use pagination or chunking
- [ ] File uploads are optimized
- [ ] Database queries are indexed appropriately
- [ ] Heavy operations are queued

### Frontend Review
- [ ] Forms have proper validation (both client and server-side)
- [ ] User feedback is provided (loading states, success/error messages)
- [ ] Responsive design works on all devices
- [ ] Accessibility standards are followed
- [ ] JavaScript is properly minified and optimized
- [ ] CSS follows consistent naming conventions

### Documentation Review
- [ ] README is updated with setup instructions
- [ ] API endpoints are documented (if applicable)
- [ ] Complex business logic is commented
- [ ] Database schema changes are documented
- [ ] Configuration changes are noted

---

## Conclusion

This guide provides a comprehensive foundation for developers joining the Laravel Insurance Management System project. The key to success is understanding both the technical architecture and the business domain of insurance management.

### Key Takeaways:
1. **Security First**: Insurance data requires strict security measures
2. **Audit Everything**: Complete audit trails are essential for compliance
3. **Family-Centric Design**: The family group pattern is core to the system
4. **Performance Matters**: Large datasets require careful optimization
5. **Testing is Critical**: Financial systems need thorough testing

### Next Steps for New Developers:
1. Set up the development environment using this guide
2. Create a simple feature following the patterns outlined
3. Write comprehensive tests for your feature
4. Review existing code to understand established patterns
5. Participate in code reviews to learn team standards

### Resources for Continued Learning:
- Laravel Documentation: https://laravel.com/docs
- Vue.js 2 Guide: https://v2.vuejs.org/v2/guide/
- Spatie Package Documentation: https://spatie.be/docs
- PHP Standards: https://www.php-fig.org/psr/

Remember: When in doubt, follow existing patterns in the codebase and don't hesitate to ask questions from the team.