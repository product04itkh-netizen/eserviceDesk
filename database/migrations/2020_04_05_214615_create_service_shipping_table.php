<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceShippingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_shipping', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Eloquent's default belongsToMany() naming (used by both
            // Service::shipping() and Shipping::service(), neither of which
            // specifies explicit pivot keys) expects singular service_id/
            // shipping_id — the original plural columns here never matched
            // what the actual relationship queries generate.
            $table->bigInteger('service_id')->unsigned();
            $table->bigInteger('shipping_id')->unsigned();
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
        Schema::dropIfExists('service_shipping');
    }
}
