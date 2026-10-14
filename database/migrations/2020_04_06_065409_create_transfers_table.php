<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('transferdate');
            $table->date('cleardate')->nullable();
            $table->date('returndate')->nullable();
            $table->integer('vendor_id');
            $table->text('reason')->nullable();
            $table->enum('type',['Credit Note','Cancel Credit Note','Clear Credit Note']);
            $table->string('cnno')->nullable();
            $table->float('cnprice')->nullable();
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
        Schema::dropIfExists('transfers');
    }
}
