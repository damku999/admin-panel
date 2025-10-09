# Code Quality Improvement TODO List
**Created:** 2025-10-09
**Based On:** claudedocs/code-quality-report-2025-10-09-1125.md
**Current Quality Score:** 6.2/10
**Target Quality Score:** 8.5/10

---

## 🔴 URGENT - Immediate Actions (This Week)

### ✅ Priority 1: Fix Critical Type Safety Issues (PHPStan Errors: 1,894)

**Status:** 🔴 NOT STARTED
**Estimated Time:** 8-12 hours
**Severity:** HIGH

#### Tasks:
- [ ] **1.1** Generate model property annotations for all 39 models
  - Command: `php artisan ide-helper:models --write --reset`
  - Expected: Eliminate ~60% (1,136) of PHPStan errors
  - Files: All models in `app/Models/` directory

- [ ] **1.2** Add missing facade imports across codebase
  - Files affected: ~100+ files using Log, Cache, DB facades
  - Pattern: Add `use Illuminate\Support\Facades\{Facade};`
  - Priority files:
    - `app/Traits/WhatsAppApiTrait.php` (12 errors)
    - All Services using facades without imports

- [ ] **1.3** Update phpstan.neon configuration
  - Remove overly broad ignore patterns
  - Add specific ignores only where necessary
  - Test: `php vendor/bin/phpstan analyze app/Models --level=5`

- [ ] **1.4** Fix Console Commands property access
  - `app/Console/Commands/RetryFailedNotifications.php` (9 errors)
  - `app/Console/Commands/SendBirthdayWishes.php` (1 error)
  - `app/Console/Commands/SendRenewalReminders.php` (1 error)

**Success Criteria:**
- PHPStan errors reduced from 1,894 to <500
- All models have @property annotations
- Zero facade import errors

---

### ✅ Priority 2: Remove Debug Code from Production

**Status:** 🔴 NOT STARTED
**Estimated Time:** 2-3 hours
**Severity:** HIGH (Security Risk)

#### Files Requiring Cleanup (7 files):
- [ ] **2.1** `app/Services/CustomerService.php`
  - Remove: dd()/dump() statements
  - Replace with: `\Log::debug()` where needed

- [ ] **2.2** `app/Traits/WhatsAppApiTrait.php`
  - Remove: Debug logging statements
  - Keep: Error logging only

- [ ] **2.3** `app/Modules/Customer/Services/CustomerService.php`
  - Remove: var_dump() calls
  - Replace with proper logging

- [ ] **2.4** `resources/views/admin/notification_templates/index_enhanced.blade.php`
  - Remove: dd() statements from blade templates

- [ ] **2.5** `resources/views/reports/index.blade.php`
  - Remove: Any debugging code
  - Clean up commented debug lines

- [ ] **2.6** `resources/views/customer/auth/two-factor-challenge.blade.php`
  - Remove: Debug statements

- [ ] **2.7** `_ide_helper.php`
  - Keep as-is (IDE helper file, not production code)

**Verification Command:**
```bash
# Search for debug statements
grep -r "dd\(" app/ resources/views/
grep -r "dump\(" app/ resources/views/
grep -r "var_dump\(" app/ resources/views/
grep -r "print_r\(" app/ resources/views/
```

**Success Criteria:**
- Zero dd/dump/var_dump in `app/` and `resources/views/`
- All debug logging replaced with proper Log facade usage
- Verification command returns no results

---

### ✅ Priority 3: Set Up Pre-Commit Hooks

**Status:** 🔴 NOT STARTED
**Estimated Time:** 1-2 hours
**Severity:** MEDIUM (Prevents future issues)

#### Tasks:
- [ ] **3.1** Create `.git/hooks/pre-commit` file
  ```bash
  #!/bin/sh

  echo "Running pre-commit checks..."

  # Check for debug statements
  if git diff --cached --name-only | xargs grep -l "dd\("; then
      echo "❌ Error: dd() statement found. Please remove before committing."
      exit 1
  fi

  if git diff --cached --name-only | xargs grep -l "dump\("; then
      echo "❌ Error: dump() statement found. Please remove before committing."
      exit 1
  fi

  # Run PHPStan on staged PHP files
  STAGED_PHP_FILES=$(git diff --cached --name-only --diff-filter=ACM | grep ".php$")
  if [ ! -z "$STAGED_PHP_FILES" ]; then
      echo "Running PHPStan on staged files..."
      php vendor/bin/phpstan analyze --error-format=table $STAGED_PHP_FILES
      if [ $? -ne 0 ]; then
          echo "❌ PHPStan found errors. Please fix before committing."
          exit 1
      fi
  fi

  echo "✅ Pre-commit checks passed!"
  exit 0
  ```

- [ ] **3.2** Make hook executable
  ```bash
  chmod +x .git/hooks/pre-commit
  ```

- [ ] **3.3** Test hook
  ```bash
  # Create test file with dd()
  echo "<?php dd('test');" > test_debug.php
  git add test_debug.php
  git commit -m "test"
  # Should be blocked by hook
  ```

- [ ] **3.4** Document hook in README
  - Add section on git hooks
  - Explain how to bypass if needed: `git commit --no-verify`

**Success Criteria:**
- Hook blocks commits with debug code
- Hook runs PHPStan on staged files
- Team members can set up hooks easily

---

### ✅ Priority 4: Add Untracked Files to Git

**Status:** 🔴 NOT STARTED
**Estimated Time:** 30 minutes
**Severity:** MEDIUM

#### Untracked Files to Add:
```bash
# Documentation files
CONTROLLER_TESTS_STATUS.md
IMPLEMENTATION_COMPLETE.md
NOTIFICATION_SYSTEM_SETUP_COMPLETE.md
QUICK_DEPLOYMENT_GUIDE.md
TESTING_QUICK_REFERENCE.md

# New feature files
app/Console/Commands/RetryFailedNotifications.php
app/Console/Commands/TestEmailNotification.php
app/Http/Controllers/CustomerDeviceController.php
app/Http/Controllers/NotificationLogController.php
app/Http/Controllers/NotificationWebhookController.php
app/Services/Notification/
app/Services/EmailService.php
app/Services/SmsService.php
app/Services/PushNotificationService.php
app/Services/NotificationLoggerService.php

# Config files
config/notification_variables.php
config/notifications.php
config/push.php
config/sms.php
config/whatsapp.php

# Migrations
database/migrations/2025_10_08_000050_create_notification_logs_table.php
database/migrations/2025_10_08_000051_create_notification_delivery_tracking_table.php
database/migrations/2025_10_08_100001_create_customer_devices_table.php
database/migrations/2025_10_08_100001_create_notification_template_versions_table.php

# Tests
tests/Feature/Notification/
tests/Unit/Notification/
tests/Feature/Controllers/

# Claude docs
claudedocs/code-quality-report-2025-10-09-1125.md
claudedocs/NOTIFICATION_ENHANCEMENT_INDEX.md
claudedocs/EMAIL_INTEGRATION_COMPLETE_REPORT.md

# Quality tools
phpstan.neon
pint.json
```

#### Files to EXCLUDE (add to .gitignore):
```
nul
app/nul
phpstan_report.json
*.backup
*.old
database/sql/
```

**Action:**
```bash
# Add legitimate files
git add app/Console/Commands/RetryFailedNotifications.php
git add app/Console/Commands/TestEmailNotification.php
git add app/Http/Controllers/CustomerDeviceController.php
git add app/Http/Controllers/NotificationLogController.php
git add app/Http/Controllers/NotificationWebhookController.php
git add app/Services/Notification/
git add config/notification_variables.php config/notifications.php config/push.php config/sms.php config/whatsapp.php
git add database/migrations/2025_10_08_*.php
git add tests/Feature/Notification/ tests/Unit/Notification/ tests/Feature/Controllers/
git add phpstan.neon pint.json

# Add to .gitignore
echo "nul" >> .gitignore
echo "app/nul" >> .gitignore
echo "phpstan_report.json" >> .gitignore
echo "*.backup" >> .gitignore
echo "*.old" >> .gitignore
echo "database/sql/" >> .gitignore

# Commit
git commit -m "Add notification system files and quality tools"
```

**Success Criteria:**
- All feature files tracked in git
- Temporary/backup files excluded via .gitignore
- Clean git status output

---

## 🟡 HIGH PRIORITY - Short-Term Goals (This Month)

### Week 1-2: Type Safety Overhaul

- [ ] **4.1** Achieve PHPStan level 5 compliance in `app/Models/`
  - Target: 0 errors in models directory
  - Run: `php vendor/bin/phpstan analyze app/Models --level=5`

- [ ] **4.2** Fix facade imports across Services
  - Review all 39 service classes
  - Add proper `use` statements
  - Target: Eliminate all "class not found" errors

- [ ] **4.3** Update all Commands to use typed properties
  - Add @var annotations where needed
  - Fix dynamic property access

- [ ] **4.4** Incrementally increase PHPStan level
  - Current: Level 5 with 1,894 errors
  - Target Week 2: Level 5 with <100 errors
  - Target Month 1: Level 6 with <50 errors

**Success Criteria:**
- PHPStan level 5: <100 errors
- All models fully annotated
- Services have proper imports

---

### Week 3: Test Coverage Foundation

- [ ] **5.1** Enable Xdebug code coverage
  ```bash
  # Install Xdebug (if not installed)
  # Configure php.ini:
  # zend_extension=xdebug
  # xdebug.mode=coverage

  # Test coverage works
  php vendor/bin/pest --coverage --min=0
  ```

- [ ] **5.2** Write repository layer tests (Priority: HIGH)
  - Target: 80% coverage for repositories
  - Files: All 22 repositories in `app/Repositories/`
  - Example test structure:
    ```php
    it('creates customer with valid data', function () {
        $repo = app(CustomerRepositoryInterface::class);
        $data = Customer::factory()->make()->toArray();
        $customer = $repo->create($data);

        expect($customer)->toBeInstanceOf(Customer::class)
            ->and($customer->email)->toBe($data['email']);
    });
    ```

- [ ] **5.3** Add critical service path tests
  - Focus: CustomerService, PolicyService, QuotationService
  - Target: 70% coverage for main services
  - Cover: Happy paths, error scenarios, edge cases

- [ ] **5.4** Set up CI/CD coverage reporting
  - Integrate with GitHub Actions
  - Fail build if coverage drops below threshold
  - Generate coverage badges

**Success Criteria:**
- Xdebug configured and working
- Repository layer: 80% coverage
- Services: 70% coverage for top 10 services
- CI/CD: Coverage reporting active

---

### Week 4: Refactoring & Documentation

- [ ] **6.1** Break down 10 most complex methods
  - Identify methods >50 lines with complexity >10
  - Extract private helper methods
  - Document with PHPDoc
  - Add method-level tests

- [ ] **6.2** Remove duplicate Export classes
  - Decision: Keep `CustomerInsurancesExport.php`
  - Delete: `CustomerInsurancesExport1.php`
  - Update any references

- [ ] **6.3** Resolve Modules/ vs Services/ architecture
  - **Option A:** Keep monolithic (recommended for current scale)
    - Move Module services to main Services/
    - Update bindings in RepositoryServiceProvider
  - **Option B:** Migrate fully to modules
    - Move all services to Modules/
    - Update all imports
  - **Decision:** Document in ARCHITECTURE.md

- [ ] **6.4** Update README with quality improvements
  - Add "Code Quality" section
  - Document PHPStan usage
  - Add test running instructions
  - Link to quality report

**Success Criteria:**
- No methods >100 lines in services
- No duplicate export classes
- Clear architectural decision documented
- README updated with quality info

---

## 🟢 MEDIUM PRIORITY - Long-Term Goals (This Quarter)

### Month 2: Testing Excellence

- [ ] **7.1** Achieve 60% overall code coverage
  - Current: ~30% (estimated)
  - Target: 60%
  - Focus areas: Controllers, Services, Repositories

- [ ] **7.2** Add controller integration tests
  - Test HTTP layer
  - Cover authentication flows
  - Test permission middleware
  - Target: 60% controller coverage

- [ ] **7.3** Implement PHPUnit data providers for edge cases
  - Parameterized tests for validation
  - Multiple input scenarios
  - Boundary condition testing

- [ ] **7.4** Add mutation testing with Infection PHP
  ```bash
  composer require --dev infection/infection
  vendor/bin/infection --coverage=build/coverage
  ```
  - Target: 80% MSI (Mutation Score Indicator)

**Success Criteria:**
- 60% overall coverage achieved
- Controllers: 60% coverage
- Mutation testing set up
- MSI: >80%

---

### Month 3: Performance Optimization

- [ ] **8.1** Implement query result caching
  - Add caching to AbstractBaseRepository
  - Cache frequently accessed data
  - Set appropriate TTLs
  - Example:
    ```php
    protected int $cacheMinutes = 5;

    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        if ($this->cacheMinutes > 0) {
            return Cache::remember(
                $this->getCacheKey($request),
                now()->addMinutes($this->cacheMinutes),
                fn() => $query->paginate($perPage)
            );
        }
        return $query->paginate($perPage);
    }
    ```

- [ ] **8.2** Add eager loading to prevent N+1 queries
  - Review all repository methods
  - Add `->with()` for relationships
  - Example:
    ```php
    protected array $defaultRelations = ['familyGroup', 'customerType'];

    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->modelClass::query();
        if (!empty($this->defaultRelations)) {
            $query->with($this->defaultRelations);
        }
        // ...
    }
    ```

- [ ] **8.3** Set up Laravel Telescope for query monitoring
  ```bash
  composer require laravel/telescope
  php artisan telescope:install
  php artisan migrate
  ```
  - Monitor slow queries
  - Identify N+1 problems
  - Track API performance

- [ ] **8.4** Add performance test suite
  - Load testing for critical endpoints
  - Response time assertions
  - Database query count assertions
  - Example:
    ```php
    it('loads customer list without N+1 queries', function () {
        DB::enableQueryLog();

        $response = $this->get('/customers');

        expect(count(DB::getQueryLog()))->toBeLessThan(5)
            ->and($response->status())->toBe(200);
    });
    ```

**Success Criteria:**
- No N+1 queries on critical paths
- Query result caching implemented
- Telescope installed and configured
- Performance tests passing

---

### Month 4: Security Hardening

- [ ] **9.1** Conduct penetration testing
  - Hire security firm OR use OWASP ZAP
  - Test authentication flows
  - Test authorization bypass
  - SQL injection testing
  - XSS vulnerability testing

- [ ] **9.2** Add security-focused unit tests
  - Test CSRF protection
  - Test XSS prevention
  - Test SQL injection prevention
  - Test authorization checks
  - Example:
    ```php
    it('prevents SQL injection in search', function () {
        $maliciousInput = "'; DROP TABLE users; --";

        $response = $this->get("/customers?search=" . urlencode($maliciousInput));

        expect($response->status())->toBe(200)
            ->and(DB::table('users')->count())->toBeGreaterThan(0);
    });
    ```

- [ ] **9.3** Implement API rate limiting documentation
  - Document current rate limits
  - Add rate limit headers to responses
  - Create rate limit middleware
  - Test rate limiting

- [ ] **9.4** Set up automated security scanning
  - Install SonarQube OR use Snyk
  - Integrate with CI/CD
  - Fail builds on critical vulnerabilities
  - Weekly security reports

**Success Criteria:**
- Penetration test passed with no critical findings
- Security test suite created
- Rate limiting documented and tested
- Automated security scanning active

---

## 📊 Progress Tracking

### Current Status (2025-10-09)
- **Overall Progress:** 0%
- **Quality Score:** 6.2/10
- **PHPStan Errors:** 1,894
- **Test Coverage:** ~30% (estimated)
- **Debug Code Files:** 7

### Week 1 Target (2025-10-16)
- **Overall Progress:** 30%
- **Quality Score:** 7.0/10
- **PHPStan Errors:** <500
- **Test Coverage:** 35%
- **Debug Code Files:** 0

### Month 1 Target (2025-11-09)
- **Overall Progress:** 60%
- **Quality Score:** 7.5/10
- **PHPStan Errors:** <100
- **Test Coverage:** 60%
- **Security Tests:** Created

### Quarter 1 Target (2026-01-09)
- **Overall Progress:** 100%
- **Quality Score:** 8.5/10
- **PHPStan Errors:** 0 (level 6)
- **Test Coverage:** 70%
- **Performance:** Optimized

---

## 🎯 Success Metrics

### Code Quality Metrics
| Metric | Current | Week 1 | Month 1 | Quarter 1 |
|--------|---------|--------|---------|-----------|
| Quality Score | 6.2/10 | 7.0/10 | 7.5/10 | 8.5/10 |
| PHPStan Errors (L5) | 1,894 | <500 | <100 | 0 |
| PHPStan Level | 5 | 5 | 5-6 | 6 |
| Debug Code Files | 7 | 0 | 0 | 0 |
| Test Coverage | ~30% | 35% | 60% | 70% |
| Complex Methods (>50 lines) | 55 | 45 | 30 | <20 |
| Duplicate Code | Yes | Yes | No | No |

### Testing Metrics
| Metric | Current | Week 1 | Month 1 | Quarter 1 |
|--------|---------|--------|---------|-----------|
| Total Tests | 33 | 50 | 150 | 200+ |
| Repository Coverage | 0% | 50% | 80% | 80% |
| Service Coverage | 25% | 40% | 70% | 70% |
| Controller Coverage | 15% | 30% | 60% | 60% |
| Mutation Score (MSI) | N/A | N/A | N/A | >80% |

### Performance Metrics
| Metric | Current | Week 1 | Month 1 | Quarter 1 |
|--------|---------|--------|---------|-----------|
| Avg Page Load | Unknown | Unknown | <300ms | <200ms |
| N+1 Queries | Many | Many | Few | None |
| Cache Hit Rate | 0% | 0% | >50% | >70% |
| DB Query Count (avg) | Unknown | Unknown | <20 | <10 |

---

## 📝 Notes & Decisions

### Architecture Decisions
- **Modules vs Services:** TBD - Need to decide by Week 4
- **Cache Strategy:** Redis recommended for production
- **Queue Driver:** Database OK for current scale, consider Redis for >1000 jobs/day

### Technical Debt
- `CustomerInsurancesExport1.php` - Remove in Week 4
- TODO comments in 2FA views - Address in Month 2
- Duplicate service implementations in Modules/ - Resolve in Week 4

### Dependencies to Review
- Consider: Laravel Pint for code formatting
- Consider: Larastan (PHPStan for Laravel)
- Consider: Pest Parallel for faster testing
- Consider: Laravel Octane for performance

---

## 🚀 Quick Commands

```bash
# Run quality checks
php vendor/bin/phpstan analyze --level=5
php vendor/bin/pest
php vendor/bin/pint --test

# Check for debug code
grep -r "dd\(" app/
grep -r "dump\(" app/

# Generate coverage report
php vendor/bin/pest --coverage --min=60

# Run specific test suites
php vendor/bin/pest tests/Unit/
php vendor/bin/pest tests/Feature/

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear all caches
php artisan optimize:clear
```

---

**Last Updated:** 2025-10-09 11:30 AM
**Next Review:** 2025-10-16 (Week 1 checkpoint)
**Maintained By:** Development Team
