<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Internal cost of labor for the ticket (e.g. technician time), separate
 * from chargeamount (what's billed to the customer). Lets the Revenue &
 * Profit report compute true profit instead of gross margin. Admin-only,
 * entered/adjusted on the edit form — not known at intake time.
 */
class AddLaborCostToServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->float('labor_cost')->nullable()->after('chargeamount');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('labor_cost');
        });
    }
}
