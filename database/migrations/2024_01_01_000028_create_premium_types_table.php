<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_vehicle')->default(false);
            $table->boolean('is_life_insurance_policies')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('is_vehicle');
            $table->index('is_life_insurance_policies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_types');
    }
};