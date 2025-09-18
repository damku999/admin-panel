<?php
/**
 * System Verification Script
 * Runs comprehensive checks after database sync and model creation
 */

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Customer;
use App\Models\Claim;
use App\Models\Quotation;
use App\Models\MessageQueue;
use App\Models\DeliveryStatus;
use App\Models\NotificationTemplate;
use App\Models\CommunicationPreference;
use App\Models\EventStore;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "========================================\n";
echo "SYSTEM VERIFICATION SCRIPT\n";
echo "Insurance Management System\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Database Connection Test
echo "1. Testing database connection...\n";
try {
    $tableCount = count(DB::select('SHOW TABLES'));
    $success[] = "✅ Database connected successfully ($tableCount tables found)";
} catch (Exception $e) {
    $errors[] = "❌ Database connection failed: " . $e->getMessage();
}

// 2. Migration Status Check
echo "2. Checking migration status...\n";
try {
    $migrationCount = DB::table('migrations')->count();
    if ($migrationCount >= 49) {
        $success[] = "✅ All migrations applied ($migrationCount total)";
    } else {
        $warnings[] = "⚠️ Only $migrationCount migrations found (expected 49+)";
    }
} catch (Exception $e) {
    $errors[] = "❌ Migration check failed: " . $e->getMessage();
}

// 3. Core Tables Check
echo "3. Verifying core tables exist...\n";
$requiredTables = [
    'users', 'customers', 'claims', 'quotations', 'permissions', 'roles',
    'message_queue', 'delivery_status', 'notification_templates',
    'communication_preferences', 'event_store'
];

foreach ($requiredTables as $table) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if (empty($exists)) {
            $errors[] = "❌ Required table missing: $table";
        } else {
            $success[] = "✅ Table exists: $table";
        }
    } catch (Exception $e) {
        $errors[] = "❌ Error checking table $table: " . $e->getMessage();
    }
}

// 4. Model Loading Test
echo "4. Testing model loading...\n";
$models = [
    'User' => User::class,
    'Customer' => Customer::class,
    'Claim' => Claim::class,
    'Quotation' => Quotation::class,
    'MessageQueue' => MessageQueue::class,
    'DeliveryStatus' => DeliveryStatus::class,
    'NotificationTemplate' => NotificationTemplate::class,
    'CommunicationPreference' => CommunicationPreference::class,
    'EventStore' => EventStore::class,
    'Role' => Role::class,
    'Permission' => Permission::class
];

foreach ($models as $name => $class) {
    try {
        $count = $class::count();
        $success[] = "✅ Model $name loads successfully ($count records)";
    } catch (Exception $e) {
        $errors[] = "❌ Model $name failed to load: " . $e->getMessage();
    }
}

// 5. Permission System Test
echo "5. Testing permission system...\n";
try {
    $permissionCount = Permission::count();
    $roleCount = Role::count();

    if ($permissionCount >= 6) {
        $success[] = "✅ Permissions created ($permissionCount total)";
    } else {
        $warnings[] = "⚠️ Expected at least 6 permissions, found $permissionCount";
    }

    if ($roleCount >= 3) {
        $success[] = "✅ Roles created ($roleCount total)";
    } else {
        $warnings[] = "⚠️ Expected at least 3 roles, found $roleCount";
    }

    // Check specific permissions that should exist
    $requiredPermissions = [
        'claim-create', 'claim-edit', 'claim-delete', 'claim-list',
        'quotation-download-pdf', 'quotation-send-whatsapp'
    ];

    foreach ($requiredPermissions as $permission) {
        if (Permission::where('name', $permission)->exists()) {
            $success[] = "✅ Required permission exists: $permission";
        } else {
            $errors[] = "❌ Missing required permission: $permission";
        }
    }

} catch (Exception $e) {
    $errors[] = "❌ Permission system test failed: " . $e->getMessage();
}

// 6. Admin User Test
echo "6. Testing admin user setup...\n";
try {
    $adminUser = User::where('email', 'parthrawal89@gmail.com')->first();
    if ($adminUser) {
        $success[] = "✅ Admin user exists: parthrawal89@gmail.com";

        if ($adminUser->hasRole('Admin')) {
            $success[] = "✅ Admin user has Admin role";
        } else {
            $errors[] = "❌ Admin user missing Admin role";
        }

        $userPermissions = $adminUser->getAllPermissions()->count();
        $success[] = "✅ Admin user has $userPermissions permissions";

    } else {
        $errors[] = "❌ Admin user not found: parthrawal89@gmail.com";
    }
} catch (Exception $e) {
    $errors[] = "❌ Admin user test failed: " . $e->getMessage();
}

// 7. Core Functionality Test
echo "7. Testing core functionality...\n";
try {
    // Test claims functionality
    $claimCount = Claim::count();
    $success[] = "✅ Claims system accessible ($claimCount claims)";

    // Test quotation functionality
    $quotationCount = Quotation::count();
    $success[] = "✅ Quotation system accessible ($quotationCount quotations)";

    // Test customer functionality
    $customerCount = Customer::count();
    $success[] = "✅ Customer system accessible ($customerCount customers)";

} catch (Exception $e) {
    $errors[] = "❌ Core functionality test failed: " . $e->getMessage();
}

// 8. Notification System Test
echo "8. Testing notification system...\n";
try {
    // Test message queue
    $queueCount = MessageQueue::count();
    $success[] = "✅ Message queue accessible ($queueCount messages)";

    // Test notification templates
    $templateCount = NotificationTemplate::count();
    $success[] = "✅ Notification templates accessible ($templateCount templates)";

    // Test communication preferences
    $prefCount = CommunicationPreference::count();
    $success[] = "✅ Communication preferences accessible ($prefCount preferences)";

    // Test delivery status
    $deliveryCount = DeliveryStatus::count();
    $success[] = "✅ Delivery status accessible ($deliveryCount statuses)";

} catch (Exception $e) {
    $errors[] = "❌ Notification system test failed: " . $e->getMessage();
}

// 9. Event Store Test
echo "9. Testing event store...\n";
try {
    $eventCount = EventStore::count();
    $success[] = "✅ Event store accessible ($eventCount events)";

    // Test event store functionality
    EventStore::appendEvent('test', 'verification', 'system.verified', [
        'timestamp' => now()->toISOString(),
        'message' => 'System verification completed successfully'
    ]);
    $success[] = "✅ Event store write test successful";

} catch (Exception $e) {
    $errors[] = "❌ Event store test failed: " . $e->getMessage();
}

// 10. Foreign Key Constraints Test
echo "10. Testing foreign key constraints...\n";
try {
    // This will fail if foreign keys are not properly set up
    $constraintCheck = DB::select("
        SELECT COUNT(*) as count
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");

    $fkCount = $constraintCheck[0]->count ?? 0;
    if ($fkCount > 0) {
        $success[] = "✅ Foreign key constraints active ($fkCount constraints)";
    } else {
        $warnings[] = "⚠️ No foreign key constraints found";
    }

} catch (Exception $e) {
    $errors[] = "❌ Foreign key test failed: " . $e->getMessage();
}

// Display Results
echo "\n========================================\n";
echo "VERIFICATION RESULTS\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✅ SUCCESS (" . count($success) . " checks passed):\n";
    foreach ($success as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️ WARNINGS (" . count($warnings) . " issues):\n";
    foreach ($warnings as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS (" . count($errors) . " critical issues):\n";
    foreach ($errors as $item) {
        echo "   $item\n";
    }
    echo "\n";
    echo "🚨 SYSTEM NOT READY - Please fix errors above\n\n";
    exit(1);
} else {
    echo "🎉 SYSTEM VERIFICATION COMPLETE!\n\n";
    echo "📊 SUMMARY:\n";
    echo "   • " . count($success) . " successful checks\n";
    echo "   • " . count($warnings) . " warnings\n";
    echo "   • " . count($errors) . " errors\n\n";

    echo "🚀 SYSTEM IS READY FOR USE!\n\n";
    echo "📧 LOGIN CREDENTIALS:\n";
    echo "   Email: parthrawal89@gmail.com\n";
    echo "   Password: Devyaan@1967\n\n";

    echo "🌐 ACCESS URLS:\n";
    echo "   Admin Panel: /login\n";
    echo "   Customer Portal: /customer/login\n\n";

    echo "📋 RECOMMENDED NEXT STEPS:\n";
    echo "   1. Test login functionality\n";
    echo "   2. Create a test claim\n";
    echo "   3. Create a test quotation\n";
    echo "   4. Test notification system\n";
    echo "   5. Verify permissions work correctly\n\n";
}

echo "========================================\n";
echo "Verification completed at: " . now()->toDateTimeString() . "\n";
echo "========================================\n";