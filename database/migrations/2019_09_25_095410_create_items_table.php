<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->unsigned();
            $table->string('name')->unique();
            $table->integer('brand_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('description')->nullable();
            $table->float('saleprice')->unsigned();
            $table->float('stockable')->unsigned();
            $table->float('stockreorder')->unsigned();
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
        Schema::dropIfExists('items');
    }
}
