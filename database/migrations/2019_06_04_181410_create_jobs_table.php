<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->string('title');
            $table->string('description');
            $table->integer('price')->nullable();
            $table->string('payment_ref')->nullable();
            $table->timestamp('price_set_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->integer('customer_payment_id')->unsigned()->nullable();
            $table->decimal('customer_rating', 1, 1)->nullable();
            $table->timestamp('rated_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('current_status', ['in-progress', 'completed', 'delivered'])->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
