@echo off
echo ========================================
echo   Installing Code Quality Tools
echo ========================================
echo.

echo This will install the following tools:
echo - Laravel Pint (code style fixer)
echo - PHPStan (static analysis)
echo - Larastan (PHPStan for Laravel)
echo.
echo Press any key to continue or Ctrl+C to cancel...
pause > nul
echo.

echo [1/3] Installing Laravel Pint...
call composer require laravel/pint --dev
echo.

echo [2/3] Installing PHPStan...
call composer require phpstan/phpstan --dev
echo.

echo [3/3] Installing Larastan...
call composer require nunomaduro/larastan --dev
echo.

echo ========================================
echo   Installation Complete!
echo ========================================
echo.
echo Tools installed:
if exist vendor\bin\pint.bat (
    echo ✓ Laravel Pint
) else (
    echo ✗ Laravel Pint - installation failed
)
if exist vendor\bin\phpstan.bat (
    echo ✓ PHPStan
) else (
    echo ✗ PHPStan - installation failed
)
echo.

echo Creating configuration files...
echo.

REM Create phpstan.neon if it doesn't exist
if not exist phpstan.neon (
    echo Creating phpstan.neon...
    (
        echo includes:
        echo     - ./vendor/nunomaduro/larastan/extension.neon
        echo.
        echo parameters:
        echo     paths:
        echo         - app
        echo     level: 5
        echo     ignoreErrors:
        echo         - '#Unsafe usage of new static#'
    ) > phpstan.neon
    echo ✓ phpstan.neon created
)

REM Create pint.json if it doesn't exist
if not exist pint.json (
    echo Creating pint.json...
    (
        echo {
        echo     "preset": "laravel",
        echo     "rules": {
        echo         "simplified_null_return": true,
        echo         "braces": false
        echo     }
        echo }
    ) > pint.json
    echo ✓ pint.json created
)

echo.
echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo You can now run:
echo   scripts\quick-fix.bat        - Auto-fix code style and clear caches
echo   scripts\analyze-and-fix.bat  - Run full analysis
echo   vendor\bin\pint              - Fix code style
echo   vendor\bin\phpstan analyse   - Run static analysis
echo.

pause
