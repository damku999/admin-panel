<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('stage_name');
            $table->text('description')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->datetime('stage_date')->nullable();
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
            $table->index('is_current');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_stages');
    }
}