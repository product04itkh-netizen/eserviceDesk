<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid')->unsigned();
            // Unique per the P0 fix — day-scoped sequence (YYYYMMDD-N) generated
            // transactionally via receiptno_counters, see 2026_XX_XX_create_receiptno_counters_table.
            $table->string('receiptno')->unique();
            // dateTime, not date — Service's setDateAttribute/getDateAttribute
            // both explicitly round-trip through 'Y-m-d H:i:s' (confirmed by a
            // live "Data missing" Carbon parse failure once real rows went
            // through a full save+reload cycle). A plain date column silently
            // truncates the time component on write, then the getter's strict
            // format parse fails on read. Same drift pattern as everything
            // else this session — the live column must actually be datetime.
            $table->dateTime('date');
            $table->date('buyingdate')->nullable();
            // returndate was never a real column — confirmed against the real
            // schema dump. It only exists on transfers (Service's own
            // getReturndate1Attribute accessor is dead code, never called).
            $table->string('serialno')->nullable();
            $table->string('model')->nullable();
            $table->longText('description')->nullable();
            $table->enum('chargetype',array('free','cash'));
            $table->float('chargeamount');
            $table->boolean('payment')->nullable();
            $table->date('paydate')->nullable();
            $table->integer('customerid');
            $table->integer('assignto');
            $table->string('phone');
            $table->boolean('warrenty')->nullable();
            $table->text('solution')->nullable();
            $table->boolean('ordered')->nullable();
            $table->text('ordered_comment')->nullable();
            // Corrected to the values actually in live use (confirmed via the
            // status-sort CASE statement in ServiceController@index, and now
            // verified byte-for-byte against a real production schema dump) —
            // the original enum here ('pending','processin','processout',
            // 'incomplete','complete') never matched what the app has shipped
            // for years.
            $table->enum('status',array('not start','start','pending','progress','processing','complete','incomplete','cancel','delivered'));
            $table->boolean('transfer_hq')->nullable();
            $table->date('transfer_hq_date')->nullable();
            // text, not string — matches the real production column exactly.
            $table->text('rmano')->nullable();
            $table->date('rmadate')->nullable();
            $table->integer('rmacode')->nullable();
            $table->boolean('delivered')->nullable();
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
        Schema::dropIfExists('services');
    }
}
