<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('stage_name');
            $table->text('description')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->dateTime('stage_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('claim_id');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_stages');
    }
};