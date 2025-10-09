@echo off
echo ========================================
echo NOTIFICATION SYSTEM SETUP
echo ========================================
echo.

echo Step 1: Running migrations...
php artisan migrate --force
echo.

echo Step 2: Creating permissions...
php artisan db:seed --class=UnifiedPermissionsSeeder --force
echo.

echo Step 3: Clearing cache...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
echo.

echo ========================================
echo SETUP COMPLETE!
echo ========================================
echo.
echo You can now access:
echo - Notification Templates: /notification-templates
echo - Notification Logs: /admin/notification-logs
echo - Analytics: /admin/notification-logs/analytics
echo - Customer Devices: /admin/customer-devices
echo.

pause
