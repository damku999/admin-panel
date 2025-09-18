<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('insurance_company_id');
            $table->string('quote_number')->unique();
            $table->string('policy_type');
            $table->integer('policy_tenure_years');
            $table->decimal('idv_vehicle', 12, 2)->nullable();
            $table->decimal('idv_trailer', 12, 2)->nullable();
            $table->decimal('idv_cng_lpg_kit', 12, 2)->nullable();
            $table->decimal('idv_electrical_accessories', 12, 2)->nullable();
            $table->decimal('idv_non_electrical_accessories', 12, 2)->nullable();
            $table->decimal('total_idv', 12, 2)->nullable();
            $table->decimal('basic_od_premium', 12, 2)->nullable();
            $table->decimal('tp_premium', 12, 2)->nullable();
            $table->decimal('cng_lpg_premium', 12, 2)->nullable();
            $table->decimal('total_od_premium', 12, 2)->nullable();
            $table->text('addon_covers_breakdown')->nullable();
            $table->decimal('total_addon_premium', 12, 2)->nullable();
            $table->decimal('net_premium', 12, 2)->nullable();
            $table->decimal('sgst_amount', 12, 2)->nullable();
            $table->decimal('cgst_amount', 12, 2)->nullable();
            $table->decimal('total_premium', 12, 2)->nullable();
            $table->decimal('roadside_assistance', 12, 2)->nullable();
            $table->decimal('final_premium', 12, 2)->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->text('recommendation_note')->nullable();
            $table->integer('ranking')->nullable();
            $table->text('benefits')->nullable();
            $table->text('exclusions')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->foreign('insurance_company_id')->references('id')->on('insurance_companies')->onDelete('cascade');
            $table->index(['quotation_id', 'ranking']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_companies');
    }
};