@echo off
echo ========================================
echo DATABASE SYNC SCRIPT FOR ADMIN PANEL
echo ========================================
echo.

echo BEFORE RUNNING - IMPORTANT CHECKS:
echo 1. Backup your database first!
echo 2. Make sure you're connected to the correct database
echo 3. Review the database-sync-commands.md file
echo.

set /p confirm="Are you sure you want to proceed? (Y/N): "
if /i not "%confirm%"=="Y" (
    echo Operation cancelled.
    pause
    exit /b
)

echo.
echo Starting database sync process...
echo.

echo Step 1: Running database sync script...
php artisan tinker --execute="require 'scripts/database-sync.php';"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Database sync failed!
    echo Check the error messages above.
    pause
    exit /b 1
)

echo.
echo Step 2: Clearing application caches...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo Step 3: Checking migration status...
php artisan migrate:status

echo.
echo ========================================
echo SYNC COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo You can now:
echo 1. Login at: http://localhost/admin-panel/login
echo 2. Use credentials: parthrawal89@gmail.com / Devyaan@1967
echo 3. Test all functionality
echo.
pause