# Comprehensive Playwright Testing Suite

This directory contains a complete Playwright testing suite for the Insurance Management System, designed to achieve 100% screen coverage with comprehensive testing across all user interfaces, workflows, and accessibility requirements.

## 🎯 Testing Coverage

### Admin Interface Tests
- **Authentication** (`admin/auth.spec.js`)
  - Login/logout workflows
  - Password reset functionality
  - Session management
  - Rate limiting

- **Dashboard** (`admin/dashboard.spec.js`)
  - Statistics widgets
  - Charts and graphs
  - Navigation functionality
  - User profile management

- **Customer Management** (`admin/customers.spec.js`)
  - CRUD operations
  - Family group management
  - Data validation
  - Search and filtering
  - Export functionality

- **Insurance Companies** (`admin/insurance-companies.spec.js`)
  - Company management
  - Status updates
  - Data export

- **Quotations** (`admin/quotations.spec.js`)
  - Quote generation
  - PDF downloads
  - WhatsApp integration
  - Comparison features

- **Claims Management** (`admin/claims.spec.js`)
  - Complete claims workflow
  - Document management
  - Status updates
  - Stage management
  - WhatsApp notifications

- **Reports** (`admin/reports.spec.js`)
  - Report generation
  - Date range filtering
  - Column selection
  - Export functionality

- **User Management** (`admin/user-management.spec.js`)
  - Role and permission management
  - User CRUD operations

### Customer Portal Tests
- **Authentication** (`customer/auth.spec.js`)
  - Customer login/logout
  - Password reset
  - Email verification
  - Family member access

- **Dashboard** (`customer/dashboard.spec.js`)
  - Policy overview
  - Quick actions
  - Recent activities
  - Family switching

- **Policies** (`customer/policies.spec.js`)
  - Policy listing
  - Document downloads
  - Renewal information
  - Claims status

- **Profile Management** (`customer/profile.spec.js`)
  - Profile updates
  - Password changes
  - Family member management

### Visual Regression Testing
- **Screen Capture** (`visual/visual-regression.spec.js`)
  - Full page screenshots
  - Component-level captures
  - Responsive design validation
  - Modal and dropdown states
  - Print stylesheet testing

### Accessibility Testing
- **WCAG Compliance** (`accessibility/a11y.spec.js`)
  - WCAG 2.1 AA compliance
  - Keyboard navigation
  - Color contrast validation
  - Screen reader compatibility
  - Form labels and ARIA attributes
  - Mobile accessibility

### End-to-End Workflows
- **Complete User Journeys** (`workflows/end-to-end.spec.js`)
  - Customer lifecycle workflows
  - Claims processing workflows
  - Multi-user interactions
  - Error recovery scenarios

## 🚀 Getting Started

### Prerequisites
- Node.js 18+ and npm
- PHP 8.1+ with Laravel
- MySQL database
- Git

### Installation
```bash
# Install dependencies
npm install

# Install Playwright browsers
npx playwright install

# Setup test environment
cp .env.example .env.testing
php artisan key:generate --env=testing
php artisan migrate:fresh --seed --env=testing
```

### Running Tests

#### Quick Start
```bash
# Run all tests
./tests/e2e/run-playwright-tests.sh --all

# Run specific test categories
./tests/e2e/run-playwright-tests.sh --admin-only
./tests/e2e/run-playwright-tests.sh --customer-only
./tests/e2e/run-playwright-tests.sh --visual-only
./tests/e2e/run-playwright-tests.sh --a11y-only
```

#### Advanced Usage
```bash
# Run tests in headed mode (visible browser)
./tests/e2e/run-playwright-tests.sh --admin-only --headed

# Run tests with debugging
./tests/e2e/run-playwright-tests.sh --admin-only --debug

# Run tests in parallel
./tests/e2e/run-playwright-tests.sh --all --workers=4

# Test specific browsers
./tests/e2e/run-playwright-tests.sh --all --firefox
./tests/e2e/run-playwright-tests.sh --all --all-browsers
```

#### Direct Playwright Commands
```bash
# Run setup tests first
npx playwright test tests/e2e/playwright/setup.spec.js

# Run specific test files
npx playwright test tests/e2e/playwright/admin/dashboard.spec.js
npx playwright test tests/e2e/playwright/customer/

# Run with UI mode
npx playwright test --ui

# Update visual regression baselines
npx playwright test tests/e2e/playwright/visual/ --update-snapshots
```

## 📊 Test Reports

### HTML Reports
Tests generate comprehensive HTML reports with:
- Test results and timing
- Screenshots on failure
- Video recordings
- Trace viewer integration

Access reports at: `tests/e2e/playwright-report/index.html`

### Screenshots and Videos
- Screenshots saved to: `tests/e2e/screenshots/`
- Videos on failure: `tests/e2e/test-results/`
- Visual regression baselines: `tests/e2e/playwright/visual/`

## 🏗️ Test Architecture

### Test Structure
```
tests/e2e/playwright/
├── fixtures/
│   └── test-helpers.js          # Shared utilities and helpers
├── data/
│   └── test-data-manager.js     # Database seeding and test data
├── auth/
│   ├── admin.json              # Stored admin authentication
│   └── customer.json           # Stored customer authentication
├── admin/                      # Admin interface tests
├── customer/                   # Customer portal tests
├── visual/                     # Visual regression tests
├── accessibility/              # Accessibility tests
├── workflows/                  # End-to-end workflows
├── setup.spec.js              # Test environment setup
├── global-setup.js            # Global configuration
└── global-teardown.js         # Cleanup procedures
```

### Key Features

#### Test Helpers (`fixtures/test-helpers.js`)
- Screenshot capture with timestamps
- Form filling utilities
- Accessibility checking with axe-core
- Responsive design testing
- Performance metrics collection
- JavaScript error detection

#### Test Data Manager (`data/test-data-manager.js`)
- Database seeding for tests
- Test customer/company/policy creation
- Cleanup procedures
- Comprehensive test dataset generation

#### Authentication Management
- Persistent authentication states
- Automatic login helpers
- Session management
- Multi-user context support

## 🔧 Configuration

### Playwright Configuration
The main configuration is in `playwright.config.js`:
- Multiple browser support (Chrome, Firefox, Safari)
- Mobile device emulation
- Parallel execution
- Automatic retries
- Video and screenshot capture

### Environment Variables
- `APP_URL`: Application base URL (default: http://localhost:8000)
- `CI`: Enables CI-specific optimizations
- `CLEANUP_ARTIFACTS`: Controls test artifact cleanup

## 🚨 Debugging Tests

### Debug Mode
```bash
# Run single test with debugging
npx playwright test tests/e2e/playwright/admin/auth.spec.js --debug

# Use headed mode for visual debugging
./tests/e2e/run-playwright-tests.sh --admin-only --headed
```

### Common Issues

#### Test Database Issues
```bash
# Reset test database
php artisan migrate:fresh --seed --env=testing

# Check database connection
php artisan tinker --execute="DB::connection('testing')->getPdo();"
```

#### Authentication Issues
```bash
# Regenerate auth states
rm tests/e2e/playwright/auth/*.json
npx playwright test tests/e2e/playwright/setup.spec.js
```

#### Port Conflicts
```bash
# Check if port 8000 is in use
lsof -i :8000

# Use different port
php artisan serve --port=8001
# Update APP_URL in test config
```

## 📈 Performance Optimization

### Parallel Execution
Tests support parallel execution across multiple workers:
```bash
# Run with 4 parallel workers
./tests/e2e/run-playwright-tests.sh --all --workers=4
```

### Test Isolation
Each test runs in isolation with:
- Fresh browser context
- Separate authentication states
- Independent test data

### Resource Management
- Automatic cleanup of temporary files
- Browser instance reuse
- Optimized screenshot capture

## 🔄 Continuous Integration

### GitHub Actions
The `.github/workflows/playwright-tests.yml` provides:
- Multi-browser testing
- Mobile device testing
- Performance testing
- Security testing
- Artifact collection

### Test Triggers
- Push to main/develop branches
- Pull request creation
- Scheduled daily runs
- Manual workflow dispatch

## 📋 Best Practices

### Writing Tests
1. Use descriptive test names
2. Include setup and teardown
3. Add meaningful screenshots
4. Test both happy path and error cases
5. Verify accessibility requirements

### Test Data
1. Use TestDataManager for consistent data
2. Clean up after tests
3. Use realistic test scenarios
4. Avoid hard-coded values

### Maintenance
1. Update visual baselines regularly
2. Monitor test performance
3. Review accessibility violations
4. Keep dependencies updated

## 🆘 Support

### Getting Help
1. Check test logs and screenshots
2. Review HTML test reports
3. Use debug mode for investigation
4. Check browser console errors

### Contributing
1. Follow existing test patterns
2. Add appropriate test coverage
3. Update documentation
4. Test across different browsers

---

## Test Coverage Summary

This comprehensive test suite provides:
- ✅ **100% Screen Coverage** - Every page, modal, and component tested
- ✅ **Cross-Browser Testing** - Chrome, Firefox, Safari support
- ✅ **Mobile Responsiveness** - Phone and tablet viewport testing
- ✅ **Accessibility Compliance** - WCAG 2.1 AA standard validation
- ✅ **Visual Regression** - Automated screenshot comparison
- ✅ **End-to-End Workflows** - Complete user journey validation
- ✅ **Performance Monitoring** - Load time and resource usage tracking
- ✅ **Error Recovery** - Exception handling and fallback testing

The test suite is designed to catch regressions, ensure quality, and provide confidence in deployments across the entire insurance management system.