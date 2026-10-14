<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceRmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_rmas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('service_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('item_name');
            $table->integer('quantity')->unsigned();
            $table->float('purchaseprice')->unsigned();
            $table->float('sellingprice')->unsigned();
            $table->smallInteger('payment')->unsigned();
            $table->date('paydate')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_rmas');
    }
}
