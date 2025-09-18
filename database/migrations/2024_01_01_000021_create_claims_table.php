<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number', 125)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_insurance_id');
            $table->enum('insurance_type', ['Health', 'Vehicle']);
            $table->date('incident_date');
            $table->text('description')->nullable();
            $table->string('whatsapp_number', 125)->nullable();
            $table->boolean('send_email_notifications')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_insurance_id')->references('id')->on('customer_insurances')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};