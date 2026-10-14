<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->unsigned();
            $table->date('date');
            $table->string('rma')->nullable();
            // No category_id here at all — confirmed against the real schema
            // dump. Category lives on purchases_details (per line item), and
            // no app code ever reads/writes Purchase::category_id.
            $table->integer('vendor_id')->unsigned();
            // service_id: links a stock purchase to the repair ticket it
            // was bought for. Required by PurchaseController's validator
            // and set on every store()/read; bigInteger (not int) and NOT
            // NULL to match the real column exactly.
            $table->bigInteger('service_id');
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
        Schema::dropIfExists('purchases');
    }
}
