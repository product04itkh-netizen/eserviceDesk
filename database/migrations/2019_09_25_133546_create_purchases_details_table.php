<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->unsigned();
            $table->bigInteger('purchase_id')->unsigned();
            // Confirmed against a real production schema dump: item_id is a
            // real, working column here — item_name/saleprice never existed
            // at all. New Purchase's free-text item field (which wrote to
            // item_name) was never actually functional in production, which
            // is exactly why only 4 purchases exist in the system's entire
            // history. Fixed the controller/view to use the catalog select
            // (matching Edit Purchase, which is the path that actually works)
            // instead of adding columns for a feature that never worked.
            $table->integer('item_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->float('purchaseprice')->unsigned();
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
        Schema::dropIfExists('purchases_details');
    }
}
