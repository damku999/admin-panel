<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('vehicle_number', 125)->nullable();
            $table->string('make_model_variant', 125);
            $table->string('rto_location', 125);
            $table->year('manufacturing_year');
            $table->integer('cubic_capacity_kw');
            $table->integer('seating_capacity');
            $table->enum('fuel_type', ['Petrol', 'Diesel', 'CNG', 'Electric', 'Hybrid']);
            $table->decimal('ncb_percentage', 5, 2)->default(0.00)->comment('No Claim Bonus percentage (0-50)');
            $table->decimal('idv_vehicle', 12, 2);
            $table->decimal('idv_trailer', 12, 2)->default(0.00);
            $table->decimal('idv_cng_lpg_kit', 12, 2)->default(0.00);
            $table->decimal('idv_electrical_accessories', 12, 2)->default(0.00);
            $table->decimal('idv_non_electrical_accessories', 12, 2)->default(0.00);
            $table->decimal('total_idv', 12, 2);
            $table->json('addon_covers')->nullable();
            $table->enum('policy_type', ['Comprehensive', 'Own Damage', 'Third Party']);
            $table->integer('policy_tenure_years')->default(1);
            $table->enum('status', ['Draft', 'Generated', 'Sent', 'Accepted', 'Rejected'])->default('Draft');
            $table->timestamp('sent_at')->nullable();
            $table->string('whatsapp_number', 125)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};