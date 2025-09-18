<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->string('document_path')->nullable();
            $table->dateTime('submitted_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('claim_id');
            $table->index('is_required');
            $table->index('is_submitted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
    }
};