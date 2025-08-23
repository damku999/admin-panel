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
        Schema::create('quotation_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_company_id')->constrained()->cascadeOnDelete();
            $table->string('quote_number')->unique();
            $table->string('plan_name');
            $table->decimal('basic_od_premium', 12, 2)->default(0);
            $table->decimal('cng_lpg_premium', 12, 2)->default(0);
            $table->decimal('total_od_premium', 12, 2)->default(0);
            $table->json('addon_covers_breakdown')->nullable();
            $table->decimal('total_addon_premium', 12, 2)->default(0);
            $table->decimal('net_premium', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('total_premium', 12, 2)->default(0);
            $table->decimal('roadside_assistance', 12, 2)->default(0);
            $table->decimal('final_premium', 12, 2)->default(0);
            $table->boolean('is_recommended')->default(false);
            $table->integer('ranking')->default(1);
            $table->text('benefits')->nullable();
            $table->text('exclusions')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            $table->index(['quotation_id', 'ranking']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_companies');
    }
};
