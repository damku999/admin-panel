# Automated Code Analysis & Fixing Guide

## Quick Start

### Option 1: Use Built-in Analysis Command
```bash
/sc:analyze --focus quality
/sc:analyze --focus security
/sc:analyze --focus performance
```

### Option 2: Run Custom Scripts

**Quick Auto-Fix (Windows)**
```bash
scripts\quick-fix.bat
```

**Full Analysis Report (Windows)**
```bash
scripts\analyze-and-fix.bat
```

**Comprehensive Report (PowerShell)**
```powershell
powershell -ExecutionPolicy Bypass -File scripts\full-report.ps1
```

## Available Tools

### 1. Laravel Pint (Auto Code Style Fixing)
```bash
# Check for issues
vendor\bin\pint --test

# Auto-fix all issues
vendor\bin\pint

# Fix specific directory
vendor\bin\pint app/Services
```

### 2. PHPStan (Static Analysis)
```bash
# Run analysis
vendor\bin\phpstan analyse --memory-limit=2G

# Generate baseline (ignore existing errors)
vendor\bin\phpstan analyse --generate-baseline

# Focus on specific level
vendor\bin\phpstan analyse --level=5
```

### 3. Composer Tools
```bash
# Security audit
composer audit

# Check outdated packages
composer outdated

# Optimize autoloader
composer dump-autoload -o
```

### 4. Laravel Artisan Commands
```bash
# Run all tests
php artisan test

# Code coverage
php artisan test --coverage

# Clear all caches
php artisan optimize:clear

# Run specific test
php artisan test --filter=CustomerServiceTest
```

## Recommended Setup

### Install Additional Quality Tools

```bash
# Install Larastan (PHPStan for Laravel)
composer require --dev nunomaduro/larastan

# Install PHP CS Fixer (alternative to Pint)
composer require --dev friendsofphp/php-cs-fixer

# Install Pest PHP (modern testing)
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
```

### Configure phpstan.neon
```neon
includes:
    - ./vendor/nunomaduro/larastan/extension.neon

parameters:
    paths:
        - app
    level: 5
    ignoreErrors:
        - '#Unsafe usage of new static#'
    excludePaths:
        - ./*/*/FileToBeExcluded.php
```

### Configure pint.json
```json
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": false,
        "new_with_braces": {
            "anonymous_class": false,
            "named_class": false
        }
    }
}
```

## Automated Workflow

### Pre-commit Hook (Git)
Create `.git/hooks/pre-commit`:
```bash
#!/bin/sh

echo "Running code quality checks..."

# Run Pint
vendor/bin/pint --test
if [ $? -ne 0 ]; then
    echo "Code style issues found. Running auto-fix..."
    vendor/bin/pint
    git add .
fi

# Run PHPStan
vendor/bin/phpstan analyse --error-format=raw --no-progress
if [ $? -ne 0 ]; then
    echo "Static analysis failed. Please fix errors before committing."
    exit 1
fi

echo "All checks passed!"
exit 0
```

### CI/CD Pipeline (GitHub Actions)
Create `.github/workflows/code-quality.yml`:
```yaml
name: Code Quality

on: [push, pull_request]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Pint
        run: vendor/bin/pint --test

      - name: Run PHPStan
        run: vendor/bin/phpstan analyse

      - name: Run Tests
        run: php artisan test --coverage
```

## Daily Workflow

### Morning Routine
```bash
# Pull latest changes
git pull

# Update dependencies
composer install

# Clear caches
php artisan optimize:clear

# Run full analysis
powershell -ExecutionPolicy Bypass -File scripts\full-report.ps1
```

### Before Committing
```bash
# Auto-fix code style
vendor\bin\pint

# Run static analysis
vendor\bin\phpstan analyse

# Run tests
php artisan test

# Commit changes
git add .
git commit -m "Your commit message"
```

### Weekly Maintenance
```bash
# Check for outdated packages
composer outdated

# Security audit
composer audit

# Update dependencies
composer update

# Run full test suite
php artisan test --coverage
```

## Common Issues & Fixes

### Issue: "Undefined property" warnings
**Fix:** Add PHPDoc annotations
```php
/** @var Customer $customer */
$customer = $this->repository->create($data);
```

### Issue: Code style inconsistencies
**Fix:** Run Laravel Pint
```bash
vendor\bin\pint
```

### Issue: Deprecated dependencies
**Fix:** Update composer packages
```bash
composer update --with-dependencies
```

### Issue: Test failures
**Fix:** Clear caches and re-run
```bash
php artisan optimize:clear
php artisan test --parallel
```

## Integration with Claude Code

Use slash commands for automated analysis:

```bash
# Full code analysis
/sc:analyze

# Focus on specific areas
/sc:analyze --focus security
/sc:analyze --focus performance
/sc:analyze --focus quality

# Cleanup code
/sc:cleanup

# Run tests
/sc:test
```

## Report Locations

All analysis reports are saved to:
- `claudedocs/code-quality-report-*.md` - Full reports
- `storage/logs/laravel.log` - Application logs
- `tests/coverage/` - Test coverage reports

## Resources

- [Laravel Pint Docs](https://laravel.com/docs/pint)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Larastan GitHub](https://github.com/nunomaduro/larastan)
- [Pest PHP Docs](https://pestphp.com/)
