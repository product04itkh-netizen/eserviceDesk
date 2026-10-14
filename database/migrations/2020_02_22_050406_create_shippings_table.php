<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Confirmed against a real production schema dump: shippings has
            // no userid, shipname, or service_id column — linkage to a
            // service is entirely through the service_shipping pivot table
            // (belongsToMany), and the controller never sets any of the
            // three (shipname is validated as 'required' but never actually
            // assigned to the model — a pre-existing, separate app bug).
            $table->string('shipreference')->nullable();
            $table->date('shipdate');
            $table->float('shipcost');
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
        Schema::dropIfExists('shippings');
    }
}
