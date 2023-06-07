<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_insurances', function (Blueprint $table) {
            $table->id();
            $table->string('month')->nullable();
            $table->string('sr_no')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('policy_type_id')->nullable();
            $table->string('branch')->nullable();
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->unsignedBigInteger('relationship_manager_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('type_of_policy')->nullable();
            $table->string('policy_no')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('rto')->nullable();
            $table->string('make_model')->nullable();
            $table->string('fuel_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email_id')->nullable();
            $table->string('od_premium')->nullable();
            $table->string('tp_premium')->nullable();
            $table->string('rsa')->nullable();
            $table->string('net_premium')->nullable();
            $table->string('gst')->nullable();
            $table->string('final_premium_with_gst')->nullable();
            $table->string('mode_of_payment')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('premium')->nullable();
            $table->string('insurance_status')->nullable();
            $table->string('extra1')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('extra2')->nullable();
            $table->string('extra3')->nullable();
            $table->string('extra4')->nullable();
            $table->string('extra5')->nullable();
            $table->string('extra6')->nullable();
            $table->string('extra7')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_insurances');
    }
};
