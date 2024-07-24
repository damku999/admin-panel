<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerInsurancesTable extends Migration
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
            $table->date('issue_date')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('broker_id')->nullable();
            $table->unsignedBigInteger('relationship_manager_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->bigInteger('insurance_company_id')->nullable();
            $table->bigInteger('premium_type_id')->nullable();
            $table->bigInteger('policy_type_id')->nullable();
            $table->bigInteger('fuel_type_id')->nullable();
            $table->string('policy_no')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('rto')->nullable();
            $table->string('make_model')->nullable();
            $table->enum('commission_on', ['net_premium', 'od_premium', 'tp_premium'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->date('tp_expiry_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->double('od_premium')->nullable();
            $table->double('tp_premium')->nullable();
            $table->double('net_premium')->nullable();
            $table->double('premium_amount')->nullable();
            $table->double('gst')->nullable();
            $table->double('final_premium_with_gst')->nullable();
            $table->double('sgst1')->nullable();
            $table->double('cgst1')->nullable();
            $table->double('cgst2')->nullable();
            $table->double('sgst2')->nullable();
            $table->double('my_commission_percentage')->nullable();
            $table->double('my_commission_amount')->nullable();
            $table->double('transfer_commission_percentage')->unsigned()->nullable();
            $table->double('transfer_commission_amount')->nullable();
            $table->double('reference_commission_percentage')->nullable();
            $table->double('reference_commission_amount')->nullable();
            $table->double('actual_earnings')->nullable();
            $table->double('ncb_percentage')->nullable();
            $table->string('mode_of_payment')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('policy_document_path', 500)->nullable();
            $table->string('gross_vehicle_weight', 500)->nullable();
            $table->string('mfg_year')->nullable();
            $table->integer('reference_by')->nullable();
            $table->string('plan_name', 150)->nullable();
            $table->string('premium_paying_term', 150)->nullable();
            $table->string('policy_term', 150)->nullable();
            $table->string('sum_insured', 150)->nullable();
            $table->string('pension_amount_yearly', 150)->nullable();
            $table->string('approx_maturity_amount', 150)->nullable();
            $table->string('life_insurance_payment_mode', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_renewed')->default(0);
            $table->dateTime('renewed_date')->nullable();
            $table->integer('new_insurance_id')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
}
