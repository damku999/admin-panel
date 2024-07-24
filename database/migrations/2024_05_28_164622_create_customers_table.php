<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('wedding_anniversary_date')->nullable();
            $table->date('engagement_anniversary_date')->nullable();
            $table->enum('type', ['Corporate', 'Retail'])->nullable();
            $table->boolean('status')->default(1);
            $table->string('pan_card_number', 50)->nullable();
            $table->string('aadhar_card_number', 50)->nullable();
            $table->string('gst_number', 50)->nullable();
            $table->string('pan_card_path', 150)->nullable();
            $table->string('aadhar_card_path', 150)->nullable();
            $table->string('gst_path', 150)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
}
