# Development Scripts

All development scripts are now managed through Composer for cross-platform compatibility.

## Available Commands

### Testing

**Run Notification Tests**
```bash
composer test:notifications
```
Runs comprehensive notification system tests with coverage reporting.

### Setup & Configuration

**Setup Notification System**
```bash
composer setup:notifications
```
Configures notification system: runs migrations, seeds permissions, clears caches.

### Code Quality

**Quick Fix**
```bash
composer fix
```
Auto-fixes code style with Laravel Pint and clears caches.

**Quick Cache Clear**
```bash
composer fix:quick
```
Optimizes autoloader and clears all application caches.

**Code Analysis**
```bash
composer analyze
```
Runs Pint (dry-run), PHPStan analysis, and security audit.

**Full Analysis**
```bash
composer analyze:full
```
Complete analysis including outdated package check (longer running).

**Simple Check**
```bash
composer check
```
Optimizes autoloader, clears caches, runs security audit.

### Quality Tools Installation

**Install Quality Tools**
```bash
composer quality:install
```
Installs Laravel Pint, PHPStan, and Larastan for code quality analysis.

**Install Rector Laravel (Code Refactoring)**
```bash
composer require driftingly/rector-laravel --dev
```
Automated code refactoring tool for Laravel upgrades and best practices.

---

## Recommended Workflow

### Daily Development
```bash
composer check
```

### Before Each Commit
```bash
composer fix
git add .
git commit -m "Your message"
```

### Weekly Maintenance
```bash
composer analyze:full
```

### Before Deployment
```bash
composer analyze
composer test:notifications
```

---

## Manual Artisan Commands

For direct Laravel command execution:

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Run specific seeder
php artisan db:seed --class=UnifiedPermissionsSeeder

# Run tests
php artisan test

# Code style fixing
php artisan pint
# OR
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

---

## Configuration Files

- `phpstan.neon` - PHPStan configuration
- `pint.json` - Laravel Pint rules
- `composer.json` - All script definitions

---

## Troubleshooting

### "Command not found" errors
Ensure you're in the project root directory and have run `composer install`.

### "Class not found" errors
```bash
composer dump-autoload -o
```

### Vendor binaries not accessible
```bash
composer install
```

---

## Additional Resources

- Full project documentation: `claudedocs/PROJECT_DOCUMENTATION.md`
- Test documentation: `claudedocs/TESTING_QUICK_REFERENCE.md`
- Architecture guide: `claudedocs/SYSTEM_ARCHITECTURE.md`
