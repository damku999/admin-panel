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
        Schema::create('message_queue', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->enum('type', ['whatsapp', 'email', 'sms']);
            $table->json('recipient'); // Contains phone/email based on type
            $table->json('content'); // Message content, subject, attachments etc.
            $table->tinyInteger('priority')->default(5); // 1=high, 5=normal, 9=low
            $table->enum('status', ['queued', 'processing', 'sent', 'failed'])->default('queued');
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->text('error')->nullable();
            $table->timestamp('queued_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('retry_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'priority', 'queued_at']);
            $table->index(['type', 'status']);
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_queue');
    }
};