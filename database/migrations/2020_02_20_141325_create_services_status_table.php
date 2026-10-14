<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreateServicesStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->unsigned();
            $table->bigInteger('service_id')->unsigned();
            $table->string('status');
            // Drives the "Calling List" worklist (ServiceController@calling) —
            // present in live use (1/2/3 values) but was missing from this
            // migration entirely.
            $table->unsignedTinyInteger('calling_status')->nullable();
            $table->date('calling_date')->nullable();
            $table->timestamps();
          //  $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services_status');
    }
}
