<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_orders', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('userid')->unsigned();
          $table->date('date');
          $table->string('ref')->nullable();
          $table->integer('customer_id')->unsigned();
          $table->boolean('payment');
          $table->date('paydate')->nullable();
          $table->string('note')->nullable();
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
        Schema::dropIfExists('sale_orders');
    }
}
