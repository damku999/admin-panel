<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_group_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('relationship');
            $table->boolean('is_head')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['family_group_id', 'customer_id'], 'family_customer_unique');
            $table->index('is_head');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};