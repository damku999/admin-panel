# Code Quality Report
Generated: 2025-10-09 10.36.29

## Summary

## 1. Code Style Analysis (Laravel Pint)

```

```

## 2. Static Analysis

PHPStan not installed.

## 3. Security Audit

```
composer : No security vulnerability advisories found.
At C:\wamp64\www\test\admin-panel\scripts\full-report.ps1:53 char:19
+ $securityOutput = composer audit --format=plain 2>&1 | Out-String
+                   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    + CategoryInfo          : NotSpecified: (No security vul...visories found.:String) [], RemoteException
    + FullyQualifiedErrorId : NativeCommandError
 

```

## 4. Outdated Dependencies

```
composer : Legend:
At C:\wamp64\www\test\admin-panel\scripts\full-report.ps1:66 char:15
+ $depsOutput = composer outdated --direct 2>&1 | Out-String
+               ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    + CategoryInfo          : NotSpecified: (Legend::String) [], RemoteException
    + FullyQualifiedErrorId : NativeCommandError
 
! patch or minor release available - update recommended
~ major release available - update possible
barryvdh/laravel-ide-helper 2.15.1  ~ 3.6.0   Laravel IDE Helper, generates correct PHPDocs for all Facade classes, to improve auto-completion.
laravel/boost               1.1.5   ! 1.3.0   Laravel Boost accelerates AI-assisted development to generate high-quality, Laravel-specific code.
laravel/framework           10.49.0 ~ 12.33.0 The Laravel Framework.
laravel/sail                1.45.0  ! 1.46.0  Docker files for running a basic Laravel application.
laravel/sanctum             3.3.3   ~ 4.2.0   Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.
nunomaduro/collision        7.12.0  ~ 8.8.2   Cli error handling for console/command-line PHP applications.
pestphp/pest                2.36.0  ~ 3.8.4   The elegant PHP Testing Framework.
phpunit/phpunit             10.5.36 ~ 11.5.42 The PHP Unit Testing framework.
spatie/laravel-permission   5.11.1  ~ 6.21.0  Permission handling for Laravel 6.0 and up

```

## 5. Project Statistics

- PHP Files (app/): 299
- Test Files: 20
- Blade Templates: 187
- Migrations: 52

## 6. Code Metrics

- Total Lines of Code (app/): ~46159

## 7. Common Issues

- Found 4 TODO/FIXME comments in code
- Found 2 potential debug statements (dd, dump, var_dump)

## Recommendations

### Immediate Actions
1. Run `vendor\bin\pint` to auto-fix code style issues
2. Review and fix critical PHPStan errors
3. Update security-vulnerable dependencies
4. Remove debug statements before deployment

### Code Quality Improvements
1. Add PHPDoc type hints to reduce static analysis errors
2. Implement missing unit tests for critical services
3. Review and complete TODO items
4. Consider adding Larastan for Laravel-specific analysis

### Commands to Fix Issues

```bash
# Fix code style
vendor\bin\pint

# Clear caches
php artisan optimize:clear

# Update dependencies
composer update --with-dependencies

# Run tests
php artisan test
```

