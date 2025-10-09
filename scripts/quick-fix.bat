@echo off
echo ========================================
echo   Quick Auto-Fix Script
echo ========================================
echo.

echo [1/3] Fixing code style with Laravel Pint...
if exist vendor\bin\pint.bat (
    call vendor\bin\pint
    echo ✓ Code style fixed
) else (
    echo ⚠ Laravel Pint not installed. Installing...
    call composer require laravel/pint --dev
    if exist vendor\bin\pint.bat (
        call vendor\bin\pint
        echo ✓ Code style fixed
    ) else (
        echo ✗ Failed to install Pint
    )
)
echo.

echo [2/3] Optimizing autoloader...
call composer dump-autoload -o
echo ✓ Autoloader optimized
echo.

echo [3/3] Clearing all caches...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
echo ✓ Caches cleared
echo.

echo ========================================
echo   Quick fixes complete!
echo ========================================
echo.

pause
