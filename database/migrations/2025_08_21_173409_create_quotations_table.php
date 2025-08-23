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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number')->nullable();
            $table->string('make_model_variant');
            $table->string('rto_location');
            $table->year('manufacturing_year');
            $table->date('date_of_registration');
            $table->integer('cubic_capacity_kw');
            $table->integer('seating_capacity');
            $table->enum('fuel_type', ['Petrol', 'Diesel', 'CNG', 'Electric', 'Hybrid']);
            $table->decimal('idv_vehicle', 12, 2);
            $table->decimal('idv_trailer', 12, 2)->default(0);
            $table->decimal('idv_cng_lpg_kit', 12, 2)->default(0);
            $table->decimal('idv_electrical_accessories', 12, 2)->default(0);
            $table->decimal('idv_non_electrical_accessories', 12, 2)->default(0);
            $table->decimal('total_idv', 12, 2);
            $table->json('addon_covers')->nullable();
            $table->enum('policy_type', ['Comprehensive', 'Own Damage', 'Third Party']);
            $table->integer('policy_tenure_years')->default(1);
            $table->enum('status', ['Draft', 'Generated', 'Sent', 'Accepted', 'Rejected'])->default('Draft');
            $table->timestamp('sent_at')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
