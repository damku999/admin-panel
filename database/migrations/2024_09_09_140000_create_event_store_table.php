<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_store', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('event_name')->index();
            $table->string('aggregate_type')->nullable()->index();
            $table->string('aggregate_id')->nullable()->index();
            $table->longText('event_data');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            
            // Composite indexes for common query patterns
            $table->index(['aggregate_type', 'aggregate_id', 'occurred_at']);
            $table->index(['event_name', 'occurred_at']);
            $table->index(['occurred_at', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_store');
    }
};