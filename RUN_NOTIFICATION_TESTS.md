# Notification Template System - Comprehensive Test Suite

## Overview

This document describes the comprehensive test suite for the notification template system covering 70+ variables across customer, policy, quotation, claim, and company data.

## Test Suite Summary

### Unit Tests (tests/Unit/Notification/)

1. **VariableResolverServiceTest.php** - 50+ tests
   - Customer variables (name, email, mobile, dates)
   - Policy variables (number, type, premium, NCB)
   - Insurance company variables
   - Date variables with formatting
   - Vehicle variables (registration, make/model, IDV)
   - Computed variables (days_remaining, policy_tenure, best_company, best_premium, comparison_list)
   - Quotation variables
   - Settings variables (advisor, company details)
   - System variables (current_date, current_year)
   - Null handling and edge cases
   - Template resolution with multiple variables
   - Validation tests
   - Currency formatting (Indian Rupee format)
   - Large values handling

2. **VariableRegistryServiceTest.php** - 30+ tests
   - Variable metadata retrieval
   - Category filtering
   - Notification type filtering
   - Template extraction
   - Template validation
   - Variable type filtering (attachment, computed, system, setting)
   - UI display formatting
   - Metadata structure validation

3. **NotificationContextTest.php** - 35+ tests
   - Context construction
   - Entity presence checks
   - Required context validation
   - Settings access with dot notation
   - Custom data management
   - Factory methods (fromCustomerId, fromInsuranceId, fromQuotationId, fromClaimId)
   - Relationship loading
   - Array conversion

4. **TemplateServiceTest.php** - 30+ tests
   - Template rendering with NotificationContext
   - Legacy array data support
   - Variable replacement ({{}} and {} formats)
   - Factory methods (renderFromInsurance, renderFromCustomer, renderFromQuotation, renderFromClaim)
   - Preview functionality
   - Available variables retrieval
   - Error handling
   - Settings loading
   - Multi-channel support (WhatsApp, Email)

### Feature Tests (tests/Feature/Notification/)

5. **CustomerNotificationTest.php** - 15+ tests
   - Customer welcome workflow
   - Birthday wish workflow
   - Anniversary workflows (wedding, engagement)
   - Company details inclusion
   - Portal URL inclusion
   - Multi-channel delivery
   - Fallback handling

6. **PolicyNotificationTest.php** - 20+ tests
   - Policy created workflow
   - Renewal reminders (30, 15, 7 days)
   - Expired policy reminders
   - Days remaining computation
   - Vehicle details inclusion
   - Policy type and premium formatting
   - Edge cases (zero premium, large amounts, missing dates)

7. **QuotationNotificationTest.php** - 15+ tests
   - Quotation ready workflow
   - Best company selection
   - Best premium identification
   - Comparison list generation
   - List sorting by premium
   - Edge cases (single company, many companies, same premiums)
   - Large premium values
   - Missing quotation companies

8. **ClaimNotificationTest.php** - 15+ tests
   - Claim initiated workflow
   - Claim stage update workflow
   - **Dynamic pending documents list** (database-driven)
   - Numbered list generation
   - Submitted documents exclusion
   - No pending documents message
   - Special characters handling
   - Many documents handling

## Total Tests: 210+ test cases

## Test Execution

### Run All Tests
```bash
cd C:\wamp64\www\test\admin-panel
php artisan test
```

### Run Unit Tests Only
```bash
php artisan test --testsuite=Unit
```

### Run Feature Tests Only
```bash
php artisan test --testsuite=Feature
```

### Run Notification Tests Only
```bash
# Unit tests
php artisan test tests/Unit/Notification

# Feature tests
php artisan test tests/Feature/Notification

# All notification tests
php artisan test tests/Unit/Notification tests/Feature/Notification
```

### Run Specific Test File
```bash
php artisan test tests/Unit/Notification/VariableResolverServiceTest.php
php artisan test tests/Feature/Notification/CustomerNotificationTest.php
```

### Run With Coverage
```bash
php artisan test --coverage
php artisan test --coverage-html reports/coverage
```

### Run With Specific Filter
```bash
# Run only tests matching pattern
php artisan test --filter=customer_name

# Run only dynamic document tests
php artisan test --filter=pending_documents
```

## Key Test Scenarios Covered

### 1. Variable Resolution (70+ Variables)
- **Customer**: name, email, mobile, whatsapp, date_of_birth, wedding_anniversary, engagement_anniversary
- **Policy**: policy_number, policy_type, premium_type, premium_amount, net_premium, ncb_percentage, plan_name, policy_term
- **Insurance Company**: insurance_company, insurance_company_code
- **Dates**: start_date, expiry_date, expired_date, issue_date, maturity_date, days_remaining, current_date
- **Vehicle**: vehicle_number, registration_no, vehicle_make_model, rto, mfg_year, idv_amount, fuel_type
- **Quotation**: quotes_count, best_company_name, best_premium, comparison_list (computed)
- **Claim**: claim_number, claim_status, stage_name, notes, pending_documents_list (dynamic from DB)
- **Company**: advisor_name, company_name, company_phone, company_email, company_website, company_address, portal_url, whatsapp_number, support_email
- **Attachments**: @policy_document, @customer_pan, @customer_aadhar
- **System**: current_date, current_year

### 2. Computed Variables
- **days_remaining**: Calculates days until policy expiry (handles expired policies)
- **policy_tenure**: Computes tenure in years ("1 Year" or "5 Years")
- **best_company**: Finds company with lowest premium from quotation
- **best_premium**: Returns lowest premium amount
- **comparison_list**: Generates sorted numbered list of all quotes
- **pending_documents_list**: **Dynamic** - Queries database for pending claim documents

### 3. Formatting
- **Currency**: Indian Rupee format (₹5,000 | ₹10,00,000)
- **Dates**: d-M-Y format (15-Jan-2025)
- **Percentage**: With decimal (20.0%)
- **Lists**: Numbered format with line breaks

### 4. Workflows Tested
- Customer registration → Welcome WhatsApp
- Policy creation → Policy details WhatsApp
- Policy expiry → Renewal reminders (30/15/7 days)
- Quotation generation → Best quote WhatsApp with comparison
- Birthday → Birthday wishes WhatsApp
- Claim creation → Claim initiated WhatsApp
- Claim documents → Dynamic pending list WhatsApp

### 5. Edge Cases
- Null/missing data handling
- Zero values (₹0)
- Large values (₹1,00,00,000)
- Expired policies (days_remaining = 0)
- Missing relationships
- Empty quotation companies
- No pending documents
- Special characters in data
- Multiple anniversary types

### 6. Error Handling
- Missing notification types
- Inactive templates
- Invalid channels
- Missing template variables
- Null date values
- Unresolved variables
- Template rendering errors

## Database Requirements

### Factories Needed (All Created)
- CustomerFactory
- CustomerInsuranceFactory
- QuotationFactory
- QuotationCompanyFactory
- ClaimFactory
- ClaimDocumentFactory
- NotificationTypeFactory
- NotificationTemplateFactory
- AppSettingFactory
- InsuranceCompanyFactory
- PolicyTypeFactory
- PremiumTypeFactory
- FuelTypeFactory

### Test Database
Tests use `RefreshDatabase` trait - database is reset before each test.

Configure test database in `phpunit.xml`:
```xml
<env name="DB_DATABASE" value="u430606517_midastech_part_test"/>
```

## Performance Benchmarks

### Expected Performance
- Unit tests: ~2-3 seconds for all 145 tests
- Feature tests: ~3-5 seconds for all 65 tests
- Total: ~5-8 seconds for complete suite

### Optimization
- Uses `RefreshDatabase` for speed
- Factories for efficient data creation
- Minimal database queries
- No actual WhatsApp API calls (mocked)

## Coverage Goals

### Target Coverage: >90%

#### Services Coverage
- VariableResolverService: 95%+
- VariableRegistryService: 95%+
- NotificationContext: 95%+
- TemplateService: 90%+

#### Critical Paths
- All 70+ variables resolution: 100%
- All computed variables: 100%
- Template rendering: 100%
- Dynamic document list: 100%
- Currency formatting: 100%
- Date formatting: 100%

## Test Data Examples

### Sample Customer
```php
Customer::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'mobile_number' => '9876543210',
    'date_of_birth' => '1990-01-15'
]);
```

### Sample Insurance
```php
CustomerInsurance::factory()->create([
    'policy_no' => 'POL-2025-001234',
    'premium_amount' => 5000,
    'start_date' => '2025-01-01',
    'expired_date' => '2026-01-01',
    'registration_no' => 'GJ-01-AB-1234'
]);
```

### Sample Quotation with Companies
```php
$quotation = Quotation::factory()->create();
QuotationCompany::factory()->count(3)->create([
    'quotation_id' => $quotation->id,
    'premium_amount' => [4500, 5000, 6000] // Sorted to show best
]);
```

### Sample Claim with Pending Documents (Dynamic)
```php
$claim = Claim::factory()->create(['claim_number' => 'CLM-2025-001']);
ClaimDocument::factory()->create([
    'claim_id' => $claim->id,
    'document_name' => 'Vehicle RC Copy',
    'is_submitted' => false // Pending
]);
ClaimDocument::factory()->create([
    'claim_id' => $claim->id,
    'document_name' => 'Insurance Policy',
    'is_submitted' => true // Already submitted - excluded from pending list
]);
```

## CI/CD Integration

### GitHub Actions Example
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.1
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --coverage
```

## Troubleshooting

### Tests Failing?

1. **Database Connection**
   - Check `phpunit.xml` DB settings
   - Ensure test database exists and is accessible

2. **Missing Factories**
   - All factories should be in `database/factories/`
   - Run `composer dump-autoload`

3. **Migration Issues**
   - Ensure all migrations are up to date
   - Check for missing relationships

4. **Slow Tests**
   - Consider using in-memory SQLite for tests
   - Optimize factory relationships loading

### Common Errors

**Error**: "Class 'Database\Factories\CustomerFactory' not found"
- **Fix**: Run `composer dump-autoload`

**Error**: "SQLSTATE[HY000] [1049] Unknown database"
- **Fix**: Create test database or update `phpunit.xml`

**Error**: "Failed asserting that null is not null"
- **Fix**: Check that notification types/templates are created in test setup

## Next Steps

### Additional Tests to Consider
1. Integration tests with actual WhatsApp API (mocked)
2. Performance tests for bulk notifications
3. Concurrency tests for template rendering
4. Security tests for template injection
5. Accessibility tests for email templates

### Future Enhancements
1. Snapshot testing for template output
2. Visual regression testing for email templates
3. Load testing for notification system
4. End-to-end tests with queue workers

## Documentation References

- [Laravel Testing Documentation](https://laravel.com/docs/10.x/testing)
- [Pest PHP Documentation](https://pestphp.com/docs)
- [Notification Variable System Architecture](./claudedocs/NOTIFICATION_VARIABLE_SYSTEM_ARCHITECTURE.md)
- [Template Workflow Integration](./claudedocs/TEMPLATE_WORKFLOW_INTEGRATION.md)

## Test Maintenance

### When Adding New Variables
1. Add to `config/notification_variables.php`
2. Add resolution logic in `VariableResolverService`
3. Add tests in `VariableResolverServiceTest`
4. Update this documentation

### When Adding New Notification Types
1. Add to database seeders
2. Create template factory state
3. Add feature test workflow
4. Update coverage reports

---

**Test Suite Version**: 1.0
**Last Updated**: October 8, 2025
**Total Tests**: 210+
**Coverage Target**: >90%
