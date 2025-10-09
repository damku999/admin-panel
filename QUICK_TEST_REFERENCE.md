# Quick Test Reference Guide

## 🚀 Quick Start

### Run All Controller Tests
```bash
php vendor/bin/pest tests/Feature/Controllers/
```

### Run Specific Controller Tests
```bash
# Customers
php vendor/bin/pest tests/Feature/Controllers/CustomerControllerTest.php

# Insurance Policies
php vendor/bin/pest tests/Feature/Controllers/CustomerInsuranceControllerTest.php

# Quotations
php vendor/bin/pest tests/Feature/Controllers/QuotationControllerTest.php

# Claims
php vendor/bin/pest tests/Feature/Controllers/ClaimControllerTest.php

# Notification Templates
php vendor/bin/pest tests/Feature/Controllers/NotificationTemplateControllerTest.php
```

### Run Tests by Pattern
```bash
# All index tests
php vendor/bin/pest --filter="index displays" tests/Feature/Controllers/

# All CRUD tests
php vendor/bin/pest --filter="store creates|update modifies|delete removes" tests/Feature/Controllers/

# All authorization tests
php vendor/bin/pest --filter="unauthenticated" tests/Feature/Controllers/
```

## ⚠️ Known Issue

**All HTTP tests currently return 404** - See CONTROLLER_TESTS_STATUS.md for details and solutions.

## 📋 Test Files Created

| File | Tests | Lines | Coverage |
|------|-------|-------|----------|
| CustomerControllerTest.php | 30+ | 410 | CRUD, Search, Export, Import, WhatsApp |
| CustomerInsuranceControllerTest.php | 40+ | 370+ | Policies, Renewals, Documents, WhatsApp |
| QuotationControllerTest.php | 50+ | 430+ | Quotes, PDF, Multi-company, AJAX |
| ClaimControllerTest.php | 50+ | 450+ | Claims, WhatsApp workflows, Statistics |
| NotificationTemplateControllerTest.php | 60+ | 550+ | Templates, Preview, Variables, Test send |

## 🔧 Test Database

```bash
# Database name (configured in phpunit.xml)
u430606517_midastech_part_test

# Tests use RefreshDatabase trait - database is reset before each test
```

## 📝 Common Test Patterns

### Basic Test Structure
```php
test('index displays customers list', function () {
    Customer::factory()->count(5)->create();

    $response = $this->get(route('customers.index'));

    $response->assertStatus(200);
    $response->assertViewIs('customers.index');
    $response->assertViewHas('customers');
});
```

### Authentication Setup
```php
beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(UnifiedPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo(\Spatie\Permission\Models\Permission::all());

    $this->actingAs($this->user);
    session()->put('user_id', $this->user->id);

    $this->withoutMiddleware([
        \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        \Spatie\Permission\Middlewares\RoleMiddleware::class,
        \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ]);
});
```

### Database Assertions
```php
$this->assertDatabaseHas('customers', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

$this->assertSoftDeleted('customers', [
    'id' => $customer->id,
]);
```

### Response Assertions
```php
$response->assertStatus(200);
$response->assertRedirect(route('customers.index'));
$response->assertSessionHas('success');
$response->assertSessionHasErrors(['email', 'name']);
$response->assertViewIs('customers.index');
$response->assertViewHas('customers');
$response->assertJsonStructure(['success', 'data']);
```

## 🐛 Debugging

### View Test Output
```bash
php vendor/bin/pest tests/Feature/Controllers/ -v
```

### Stop on First Failure
```bash
php vendor/bin/pest tests/Feature/Controllers/ --stop-on-failure
```

### Run Single Test
```bash
php vendor/bin/pest --filter="test name here" tests/Feature/Controllers/
```

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 📊 Coverage by Feature

### CustomerController
- ✅ Index with search, filters, sorting
- ✅ Create/Store with validation
- ✅ Edit/Update
- ✅ Delete (soft delete)
- ✅ Status updates
- ✅ Export (XLSX/CSV)
- ✅ Import form
- ✅ Resend WhatsApp onboarding
- ✅ AJAX autocomplete
- ✅ Pagination
- ✅ Authorization checks

### CustomerInsuranceController
- ✅ Policy CRUD operations
- ✅ Policy renewals (renew form + storeRenew)
- ✅ Status toggles
- ✅ Document uploads
- ✅ WhatsApp document delivery
- ✅ Renewal reminder WhatsApp
- ✅ Export functionality
- ✅ Relationship tests
- ✅ Authorization checks

### QuotationController
- ✅ Quotation CRUD
- ✅ Generate multi-company quotes
- ✅ WhatsApp quotation delivery
- ✅ PDF generation and download
- ✅ Get quote form HTML (AJAX)
- ✅ Export functionality
- ✅ AJAX autocomplete
- ✅ Business logic (reference generation)
- ✅ Relationship tests
- ✅ Authorization checks

### ClaimController
- ✅ Claim CRUD operations
- ✅ AJAX policy search
- ✅ Claim statistics endpoint
- ✅ WhatsApp document list (Vehicle/Health specific)
- ✅ WhatsApp pending documents
- ✅ WhatsApp claim number
- ✅ WhatsApp preview (multiple types)
- ✅ Export with relationships
- ✅ Relationship tests (stages, documents, liability)
- ✅ Authorization checks

### NotificationTemplateController
- ✅ Template CRUD operations
- ✅ Filtering (search, channel, status, category)
- ✅ Sorting
- ✅ Preview with real data (customer/insurance/quotation)
- ✅ Get customer data endpoint
- ✅ Get available variables
- ✅ Send test notifications (WhatsApp/Email)
- ✅ Variable storage as JSON
- ✅ Multi-channel support
- ✅ Authorization checks

## 🎯 Next Steps

1. Fix HTTP routing issue (see CONTROLLER_TESTS_STATUS.md)
2. Run full test suite
3. Address any failures
4. Add integration tests if needed

## 📞 Support

For issues or questions about these tests:
1. Check CONTROLLER_TESTS_STATUS.md for known issues
2. Review test file comments for implementation details
3. Consult team members familiar with project testing setup
