# Notification Testing - Quick Reference Card

## 🚀 Run Tests

```bash
# All notification tests
php artisan test tests/Unit/Notification tests/Feature/Notification

# Unit tests only
php artisan test tests/Unit/Notification

# Feature tests only
php artisan test tests/Feature/Notification

# With coverage
php artisan test --coverage tests/Unit/Notification tests/Feature/Notification

# Specific file
php artisan test tests/Unit/Notification/VariableResolverServiceTest.php

# Windows batch script
run-tests.bat
```

## 📊 Test Statistics

- **Total Tests**: 210+
- **Unit Tests**: 145 (69%)
- **Feature Tests**: 65 (31%)
- **Variables Covered**: 70+
- **Workflows Covered**: 12
- **Execution Time**: ~5-8 seconds
- **Coverage Target**: >90%

## 🧪 Test Files

### Unit Tests (tests/Unit/Notification/)
1. **VariableResolverServiceTest.php** - 50+ tests (all variables)
2. **VariableRegistryServiceTest.php** - 30+ tests (metadata, extraction)
3. **NotificationContextTest.php** - 35+ tests (context building)
4. **TemplateServiceTest.php** - 30+ tests (rendering)

### Feature Tests (tests/Feature/Notification/)
5. **CustomerNotificationTest.php** - 15+ tests (welcome, birthday)
6. **PolicyNotificationTest.php** - 20+ tests (created, renewals)
7. **QuotationNotificationTest.php** - 15+ tests (comparison lists)
8. **ClaimNotificationTest.php** - 15+ tests (dynamic documents)

## 🔑 Key Variables Tested

### Customer (7)
- customer_name, customer_email, customer_mobile, customer_whatsapp
- date_of_birth, wedding_anniversary, engagement_anniversary

### Policy (8)
- policy_number, policy_type, premium_type, premium_amount
- net_premium, ncb_percentage, plan_name, policy_term

### Computed (6) ⚡
- days_remaining, policy_tenure
- best_company, best_premium, comparison_list
- pending_documents_list (dynamic DB query)

### Dates (6)
- start_date, expiry_date, expired_date
- issue_date, maturity_date, current_date

### Vehicle (5)
- vehicle_number, registration_no, vehicle_make_model
- rto, mfg_year, idv_amount, fuel_type

### Company (9)
- advisor_name, company_name, company_phone
- company_email, company_website, company_address
- portal_url, whatsapp_number, support_email

### Quotation (4)
- quotes_count, best_company_name, best_premium, comparison_list

### Claim (5)
- claim_number, claim_status, stage_name
- notes, pending_documents_list

## 📝 Formatting Tested

### Currency (Indian Rupee)
```
5000 → ₹5,000
50000 → ₹50,000
10000000 → ₹1,00,00,000
```

### Dates (d-M-Y)
```
2025-01-15 → 15-Jan-2025
2025-12-31 → 31-Dec-2025
```

### Percentages
```
20 → 20.0%
15.5 → 15.5%
```

## 🔄 Workflows Tested

1. Customer Welcome
2. Birthday Wishes
3. Wedding Anniversary
4. Engagement Anniversary
5. Policy Created
6. Renewal Reminder 30 days
7. Renewal Reminder 15 days
8. Renewal Reminder 7 days
9. Policy Expired
10. Quotation Ready
11. Claim Initiated
12. Claim Stage Update

## 🎯 Critical Tests

### Most Complex
**Dynamic Pending Documents List** (ClaimNotificationTest)
- Queries database for pending documents
- Excludes submitted documents
- Generates numbered list
- Handles empty/many documents

### Most Critical
**Template Resolution** (VariableResolverServiceTest)
- Resolves all variables in template
- Handles missing variables
- Formats output correctly
- Validates context

### Most Important
**Days Remaining** (PolicyNotificationTest)
- Computes expiry countdown
- Handles expired policies (returns 0)
- Critical for renewal reminders

## 🛠️ Troubleshooting

### Tests Won't Run
```bash
# Check database
cat phpunit.xml | grep DB_DATABASE

# Rebuild autoload
composer dump-autoload

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Factory Errors
```bash
# Verify factories exist
ls database/factories/

# Check namespaces
grep "namespace" database/factories/*.php
```

### Database Errors
```bash
# Check connection
php artisan tinker
>>> DB::connection()->getPdo();

# Create test database if needed
mysql -u root -e "CREATE DATABASE u430606517_midastech_part_test;"
```

## 📚 Documentation

- **Detailed Guide**: [RUN_NOTIFICATION_TESTS.md](./RUN_NOTIFICATION_TESTS.md)
- **Complete Summary**: [claudedocs/NOTIFICATION_TESTING_SUITE_SUMMARY.md](./claudedocs/NOTIFICATION_TESTING_SUITE_SUMMARY.md)
- **Complete Report**: [claudedocs/TESTING_SUITE_COMPLETE_REPORT.md](./claudedocs/TESTING_SUITE_COMPLETE_REPORT.md)

## 🔍 Example Test

```php
/** @test */
public function it_resolves_customer_name()
{
    // Arrange
    $customer = Customer::factory()->create(['name' => 'John Doe']);
    $context = new NotificationContext(['customer' => $customer]);

    // Act
    $result = $this->resolver->resolveVariable('customer_name', $context);

    // Assert
    $this->assertEquals('John Doe', $result);
}
```

## ✅ Coverage Goals

- Services: 95%+
- Models: 85%+
- Overall: 90%+

## 🎉 Expected Results

```
Tests:    210 passed (210 assertions)
Duration: 7.2s
```

---

**Quick Reference Version**: 1.0
**Last Updated**: October 8, 2025
