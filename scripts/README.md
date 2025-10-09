# Code Quality Scripts

## Quick Start Guide

### First Time Setup

**Install all quality tools:**
```bash
scripts\install-quality-tools.bat
```

This installs:
- ✓ Laravel Pint (code style auto-fixer)
- ✓ PHPStan (static analysis)
- ✓ Larastan (PHPStan for Laravel)

---

## Available Scripts

### 1. Simple Check (No Installation Required)
```bash
scripts\simple-check.bat
```
**What it does:**
- Checks PHP syntax errors
- Clears caches
- Finds debug statements (dd, dump, var_dump)
- Security audit
- Optimizes autoloader

**Use when:** You want a quick check without installing extra tools

---

### 2. Quick Fix
```bash
scripts\quick-fix.bat
```
**What it does:**
- Auto-fixes code style with Laravel Pint
- Optimizes autoloader
- Clears all caches

**Use when:** Before committing code

---

### 3. Full Analysis
```bash
scripts\analyze-and-fix.bat
```
**What it does:**
- Laravel Pint analysis & auto-fix
- PHPStan static analysis
- Security vulnerability check
- Outdated package check

**Use when:** Weekly maintenance or before releases

---

### 4. Detailed Report
```powershell
powershell -ExecutionPolicy Bypass -File scripts\full-report.ps1
```
**What it does:**
- Generates comprehensive report
- Saves to `claudedocs/code-quality-report-*.md`
- Includes statistics, metrics, and recommendations

**Use when:** You need a saved report for review

---

## Recommended Workflow

### Daily
```bash
scripts\simple-check.bat
```

### Before Each Commit
```bash
scripts\quick-fix.bat
git add .
git commit -m "Your message"
```

### Weekly
```bash
scripts\analyze-and-fix.bat
```

### Monthly
```powershell
powershell -ExecutionPolicy Bypass -File scripts\full-report.ps1
```

---

## Manual Commands

If you prefer to run tools individually:

```bash
# Fix code style
vendor\bin\pint

# Check code style (don't fix)
vendor\bin\pint --test

# Run static analysis
vendor\bin\phpstan analyse

# Security audit
composer audit

# Update packages
composer update

# Run tests
php artisan test
```

---

## Troubleshooting

### "vendor\bin\pint is not recognized"
**Solution:** Run the installer first:
```bash
scripts\install-quality-tools.bat
```

### "Class not found" errors
**Solution:** Optimize autoloader:
```bash
composer dump-autoload -o
```

### Scripts won't run
**Solution:** Make sure you're in the project root directory:
```bash
cd C:\wamp64\www\test\admin-panel
```

---

## Configuration Files

Created automatically by the installer:

- `phpstan.neon` - PHPStan configuration
- `pint.json` - Laravel Pint rules

You can customize these files to adjust analysis rules.

---

## See Also

- Full guide: `claudedocs/AUTOMATED_ANALYSIS_GUIDE.md`
- Test documentation: `claudedocs/PEST_PHP_CONVERSION.md`
