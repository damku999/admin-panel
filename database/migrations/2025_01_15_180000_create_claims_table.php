<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip for testing environment - needs to run for test database

        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_insurance_id');
            $table->enum('insurance_type', ['Health', 'Vehicle']);
            $table->date('incident_date');
            $table->text('description')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->boolean('send_email_notifications')->default(true);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_insurance_id')->references('id')->on('customer_insurances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claims');
    }
}