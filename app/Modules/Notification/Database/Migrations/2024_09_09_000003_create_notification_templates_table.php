<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['whatsapp', 'email', 'sms']);
            $table->string('subject')->nullable(); // For email templates
            $table->text('content');
            $table->json('variables')->nullable(); // Template variables/placeholders
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['type', 'active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};