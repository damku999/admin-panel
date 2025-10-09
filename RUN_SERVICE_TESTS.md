# Quick Guide: Running Service Tests

## Prerequisites

Before running tests, ensure:
1. Test database is configured in `.env.testing`
2. Dependencies are installed: `composer install`
3. Database migrations are run: `php artisan migrate --env=testing`

---

## Quick Test Commands

### Run All Service Tests
```bash
php artisan test tests/Unit/Services/
```

### Run Individual Service Tests

**CustomerService Tests**:
```bash
# Mock-based tests (fast, no database)
php artisan test tests/Unit/Services/CustomerServiceTest.php

# Integration tests (uses database)
php artisan test tests/Unit/Services/CustomerServiceSimplifiedTest.php
```

**PolicyService Tests**:
```bash
# Mock-based tests
php artisan test tests/Unit/Services/PolicyServiceTest.php

# Integration tests
php artisan test tests/Unit/Services/PolicyServiceSimplifiedTest.php
```

**QuotationService Tests**:
```bash
# Mock-based tests
php artisan test tests/Unit/Services/QuotationServiceTest.php

# Integration tests
php artisan test tests/Unit/Services/QuotationServiceSimplifiedTest.php
```

---

## Test Filtering

### Run Specific Test
```bash
php artisan test --filter="creates customer successfully"
```

### Run Tests by Pattern
```bash
# Run all simplified (integration) tests
php artisan test --filter=Simplified

# Run all customer-related tests
php artisan test --filter=Customer

# Run all policy tests
php artisan test --filter=Policy

# Run all quotation tests
php artisan test --filter=Quotation
```

---

## Verbose Output

### See Detailed Test Execution
```bash
php artisan test --verbose
```

### Stop on First Failure
```bash
php artisan test --stop-on-failure
```

### Show Warnings
```bash
php artisan test --display-warnings
```

---

## Coverage Reports

### Generate Coverage Report
```bash
# Requires Xdebug or PCOV
php artisan test --coverage

# With minimum coverage requirement
php artisan test --coverage --min=70

# Coverage for specific directory
php artisan test tests/Unit/Services/ --coverage
```

### HTML Coverage Report
```bash
phpunit --coverage-html coverage/
```

---

## Parallel Testing

### Run Tests in Parallel (Faster)
```bash
php artisan test --parallel

# Specify number of processes
php artisan test --parallel --processes=4
```

---

## Common Test Scenarios

### 1. Before Committing Code
```bash
# Run all tests with coverage
php artisan test --coverage --min=70
```

### 2. Debugging Test Failures
```bash
# Run specific failing test with verbose output
php artisan test --filter="test name" --verbose --stop-on-failure
```

### 3. CI/CD Pipeline
```bash
# Run all tests with coverage and strict mode
php artisan test --coverage --min=70 --stop-on-failure
```

### 4. Development Workflow
```bash
# Watch mode (requires package)
php artisan test --watch

# Or run tests for specific service you're working on
php artisan test tests/Unit/Services/CustomerServiceTest.php
```

---

## Test Database Setup

### Option 1: SQLite (Faster for Tests)
Create `.env.testing`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Option 2: MySQL Test Database
Create `.env.testing`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_panel_test
DB_USERNAME=root
DB_PASSWORD=
```

### Run Migrations
```bash
php artisan migrate --env=testing
php artisan db:seed --env=testing --class=TestDataSeeder
```

---

## Troubleshooting

### Issue: "Class not found"
**Solution**: Regenerate autoload files
```bash
composer dump-autoload
```

### Issue: "Database connection failed"
**Solution**: Check `.env.testing` configuration
```bash
# Test database connection
php artisan migrate:status --env=testing
```

### Issue: "Too many connections"
**Solution**: Use SQLite for tests or increase MySQL max_connections

### Issue: Tests are slow
**Solutions**:
1. Use SQLite in-memory database
2. Run tests in parallel
3. Use mock objects instead of real database queries
4. Disable unnecessary seeding

### Issue: "Table doesn't exist"
**Solution**: Run migrations in test environment
```bash
php artisan migrate:fresh --env=testing
```

---

## Test Execution Tips

### 1. Run Fast Tests First
```bash
# Run unit tests (fast, use mocks)
php artisan test tests/Unit/Services/*Test.php

# Then run integration tests
php artisan test tests/Unit/Services/*SimplifiedTest.php
```

### 2. Focus on Changed Code
```bash
# If you modified CustomerService
php artisan test tests/Unit/Services/CustomerService*Test.php --stop-on-failure
```

### 3. Test Specific Methods
```bash
# Test only customer creation
php artisan test --filter="creates customer"

# Test only policy renewal
php artisan test --filter="renewal"

# Test only quotation calculations
php artisan test --filter="calculate"
```

---

## Expected Test Results

### CustomerService (68 tests)
```
PASS  Tests\Unit\Services\CustomerServiceTest
PASS  Tests\Unit\Services\CustomerServiceSimplifiedTest
Tests:  68 passed
Time:   ~5-10 seconds
```

### PolicyService (85 tests)
```
PASS  Tests\Unit\Services\PolicyServiceTest
PASS  Tests\Unit\Services\PolicyServiceSimplifiedTest
Tests:  85 passed
Time:   ~6-12 seconds
```

### QuotationService (64 tests)
```
PASS  Tests\Unit\Services\QuotationServiceTest
PASS  Tests\Unit\Services\QuotationServiceSimplifiedTest
Tests:  64 passed
Time:   ~5-10 seconds
```

### All Services Combined (220+ tests)
```
PASS  Tests\Unit\Services
Tests:  220+ passed
Time:   ~20-30 seconds
```

---

## Test Writing Guidelines

### Good Test Names
```php
✅ test('creates customer successfully with valid data')
✅ test('throws exception when creating customer with duplicate email')
✅ test('handles transaction rollback on database error')

❌ test('test1')
❌ test('customer test')
❌ test('it works')
```

### Good Assertions
```php
✅ expect($customer)->toBeInstanceOf(Customer::class);
✅ expect($customer->email)->toBe('test@example.com');
✅ expect($result)->toBeTrue();

❌ expect($customer)->toBeTruthy(); // Too vague
❌ $this->assertEquals($customer->id, 1); // Use Pest syntax
```

### Arrange-Act-Assert Pattern
```php
test('creates customer successfully', function () {
    // Arrange - Setup test data
    $customerData = ['name' => 'John', 'email' => 'john@example.com'];

    // Act - Execute the action
    $result = $this->service->createCustomer($customerData);

    // Assert - Verify the outcome
    expect($result)->toBeInstanceOf(Customer::class);
    expect($result->name)->toBe('John');
});
```

---

## Next Steps

1. **Run the tests**: `php artisan test tests/Unit/Services/`
2. **Check coverage**: `php artisan test --coverage --min=70`
3. **Fix any failures**: Use `--verbose` and `--stop-on-failure` flags
4. **Add to CI/CD**: Integrate test running into your deployment pipeline

---

## Additional Resources

- **Pest PHP Documentation**: https://pestphp.com/docs
- **Laravel Testing**: https://laravel.com/docs/testing
- **Testing Best Practices**: See `TEST_COVERAGE_REPORT.md`

---

## Support

If you encounter issues:
1. Check the test output for specific error messages
2. Review the `TEST_COVERAGE_REPORT.md` for test structure
3. Ensure database migrations are up to date
4. Verify all dependencies are installed

---

**Last Updated**: October 9, 2025
**Test Suite Version**: 1.0
**Total Test Coverage**: 70%+ for critical services
