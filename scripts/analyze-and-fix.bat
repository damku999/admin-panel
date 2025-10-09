@echo off
echo ========================================
echo   Code Analysis and Auto-Fix Report
echo ========================================
echo.

echo [1/6] Running Laravel Pint (Code Style Auto-Fix)...
if exist vendor\bin\pint.bat (
    call vendor\bin\pint --test
    if %errorlevel% neq 0 (
        echo Fixing code style issues...
        call vendor\bin\pint
    )
) else (
    echo ⚠ Laravel Pint not installed. Run: composer require laravel/pint --dev
)
echo.

echo [2/6] Running PHPStan (Static Analysis)...
if exist vendor\bin\phpstan.bat (
    call vendor\bin\phpstan analyse --memory-limit=2G
) else (
    echo ⚠ PHPStan not installed. Run: composer require phpstan/phpstan --dev
)
echo.

echo [3/6] Running PHP CS Fixer (if available)...
if exist vendor\bin\php-cs-fixer.bat (
    call vendor\bin\php-cs-fixer fix --dry-run --diff
) else (
    echo ⚠ PHP CS Fixer not installed, skipping...
)
echo.

echo [4/6] Checking for security vulnerabilities...
call composer audit
echo.

echo [5/6] Checking for outdated packages...
call composer outdated --direct
echo.

echo [6/6] Generating summary...
echo.
echo ========================================
echo   Analysis Complete!
echo ========================================
echo.
echo Next Steps:
echo.
if not exist vendor\bin\pint.bat (
    echo - Install Pint: composer require laravel/pint --dev
)
if not exist vendor\bin\phpstan.bat (
    echo - Install PHPStan: composer require phpstan/phpstan --dev
)
echo - Fix code style: vendor\bin\pint
echo - Review security issues above
echo - Update outdated packages: composer update
echo.

pause
