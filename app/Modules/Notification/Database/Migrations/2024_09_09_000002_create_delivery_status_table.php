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
        Schema::create('delivery_status', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->index();
            $table->enum('type', ['whatsapp', 'email', 'sms']);
            $table->string('recipient'); // Phone number or email
            $table->enum('status', ['sent', 'delivered', 'failed', 'pending']);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error')->nullable();
            $table->integer('attempts')->default(1);
            $table->json('metadata')->nullable(); // Additional delivery info
            $table->timestamps();

            // Indexes for reporting and queries
            $table->index(['type', 'status']);
            $table->index(['sent_at']);
            $table->index(['recipient']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_status');
    }
};