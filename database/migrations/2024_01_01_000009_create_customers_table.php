<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('mobile_number');
            $table->date('date_of_birth')->nullable();
            $table->date('wedding_anniversary_date')->nullable();
            $table->date('engagement_anniversary_date')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('customer_type_id')->nullable();
            $table->boolean('status')->default(true);
            $table->string('pan_card_number')->nullable();
            $table->string('aadhar_card_number')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('pan_card_path')->nullable();
            $table->string('aadhar_card_path')->nullable();
            $table->string('gst_path')->nullable();
            $table->unsignedBigInteger('family_group_id')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('password_reset_sent_at')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->index('email');
            $table->index('mobile_number');
            $table->index('type');
            $table->index('family_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};