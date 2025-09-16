<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->string('document_path')->nullable();
            $table->datetime('submitted_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');

            // Indexes for performance
            $table->index('claim_id');
            $table->index('is_required');
            $table->index('is_submitted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_documents');
    }
}