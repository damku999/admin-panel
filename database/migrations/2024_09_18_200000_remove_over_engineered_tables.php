<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOverEngineeredTables extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove over-engineered notification/event sourcing tables and simplify to Laravel standards
     *
     * @return void
     */
    public function up()
    {
        // Skip for testing environment
        if (app()->environment() === 'testing') {
            return;
        }

        // Drop over-engineered notification/event sourcing tables
        $tablesToDrop = [
            'event_store',
            'message_queue',
            'delivery_status',
            'notification_templates',
            'communication_preferences'
        ];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: This will NOT recreate the complex tables - they were over-engineered
     * If needed, use Laravel's built-in notification system instead
     *
     * @return void
     */
    public function down()
    {
        // Intentionally empty - these tables were over-engineered
        // Use Laravel's built-in notifications/mail instead
    }
}