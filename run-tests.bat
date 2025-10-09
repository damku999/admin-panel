@echo off
REM Notification Template System Test Execution Script
REM Run this from the admin-panel directory

echo ================================================================================
echo   NOTIFICATION TEMPLATE SYSTEM - COMPREHENSIVE TEST SUITE
echo ================================================================================
echo.

echo [1/5] Running Unit Tests - Variable Resolver Service...
php artisan test tests/Unit/Notification/VariableResolverServiceTest.php --colors
echo.

echo [2/5] Running Unit Tests - Variable Registry Service...
php artisan test tests/Unit/Notification/VariableRegistryServiceTest.php --colors
echo.

echo [3/5] Running Unit Tests - Notification Context...
php artisan test tests/Unit/Notification/NotificationContextTest.php --colors
echo.

echo [4/5] Running Unit Tests - Template Service...
php artisan test tests/Unit/Notification/TemplateServiceTest.php --colors
echo.

echo [5/5] Running Feature Tests - All Workflows...
php artisan test tests/Feature/Notification --colors
echo.

echo ================================================================================
echo   TEST SUMMARY
echo ================================================================================
echo.
echo Running complete test suite with coverage...
php artisan test tests/Unit/Notification tests/Feature/Notification --coverage
echo.

echo ================================================================================
echo   TESTS COMPLETED
echo ================================================================================
echo.
echo See RUN_NOTIFICATION_TESTS.md for detailed documentation
echo.

pause
