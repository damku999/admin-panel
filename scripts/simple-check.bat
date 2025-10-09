@echo off
echo ========================================
echo   Simple Code Check (No Dependencies)
echo ========================================
echo.

echo [1/5] Checking PHP syntax errors...
echo.
set errorCount=0
for /r app %%f in (*.php) do (
    php -l "%%f" > nul 2>&1
    if errorlevel 1 (
        echo ✗ Syntax error: %%f
        set /a errorCount+=1
    )
)
if %errorCount%==0 (
    echo ✓ No syntax errors found
) else (
    echo ⚠ Found %errorCount% files with syntax errors
)
echo.

echo [2/5] Clearing application caches...
call php artisan cache:clear > nul 2>&1
call php artisan config:clear > nul 2>&1
call php artisan route:clear > nul 2>&1
call php artisan view:clear > nul 2>&1
echo ✓ Caches cleared
echo.

echo [3/5] Optimizing autoloader...
call composer dump-autoload -o > nul 2>&1
echo ✓ Autoloader optimized
echo.

echo [4/5] Checking for debug statements...
echo.
findstr /s /i /n /c:"dd(" /c:"dump(" /c:"var_dump(" app\*.php 2>nul
if errorlevel 1 (
    echo ✓ No debug statements found
) else (
    echo ⚠ Found debug statements above - consider removing before deployment
)
echo.

echo [5/5] Running security audit...
call composer audit
echo.

echo ========================================
echo   Check Complete!
echo ========================================
echo.
echo To install advanced quality tools, run:
echo   scripts\install-quality-tools.bat
echo.

pause
