#!/bin/bash

echo "========================================"
echo "DATABASE SYNC SCRIPT FOR ADMIN PANEL"
echo "========================================"
echo ""

echo "BEFORE RUNNING - IMPORTANT CHECKS:"
echo "1. Backup your database first!"
echo "2. Make sure you're connected to the correct database"
echo "3. Review the database-sync-commands.md file"
echo ""

read -p "Are you sure you want to proceed? (Y/N): " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo "Operation cancelled."
    exit 0
fi

echo ""
echo "Starting database sync process..."
echo ""

echo "Step 1: Running database sync script..."
php artisan tinker --execute="require 'scripts/database-sync.php';"

if [ $? -ne 0 ]; then
    echo ""
    echo "ERROR: Database sync failed!"
    echo "Check the error messages above."
    exit 1
fi

echo ""
echo "Step 2: Clearing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Step 3: Checking migration status..."
php artisan migrate:status

echo ""
echo "========================================"
echo "SYNC COMPLETED SUCCESSFULLY!"
echo "========================================"
echo ""
echo "You can now:"
echo "1. Login at: http://localhost/admin-panel/login"
echo "2. Use credentials: parthrawal89@gmail.com / Devyaan@1967"
echo "3. Test all functionality"
echo ""