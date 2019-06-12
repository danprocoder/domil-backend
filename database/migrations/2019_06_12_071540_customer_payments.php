<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->integer('total_amount')->unsigned();
            $table->integer('brand_share')->unsigned();
            $table->integer('company_share')->unsigned();
            $table->string('meta_for');
            $table->integer('meta_id');
            $table->string('payment_ref');
            $table->timestamp('paid_brand_at')->nullable();
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
        Schema::dropIfExists('customer_payments');
    }
}
