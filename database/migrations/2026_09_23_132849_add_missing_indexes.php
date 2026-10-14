<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The real production schema (confirmed via a live mysqldump) never had any
 * secondary indexes beyond primary keys — invisible against a handful of
 * synthetic test rows, but at real volume (58k+ services, 205k+ status rows,
 * 13k+ service_rmas) several hot paths degrade from milliseconds to tens of
 * seconds. Purely additive: doesn't change query results, just how fast
 * Postgres can satisfy the filters/joins the app already does.
 *
 * Found live: DashboardController's totalcash correlated subquery against
 * service_rmas.service_id took 58s unindexed, right at PHP's 60s execution
 * limit, taking the whole dashboard down with real data loaded.
 */
class AddMissingIndexes extends Migration
{
    public function up()
    {
        Schema::table('service_rmas', function (Blueprint $table) {
            $table->index('service_id');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment');
            $table->index('assignto');
            $table->index('customerid');
            $table->index('date');
        });
        Schema::table('services_status', function (Blueprint $table) {
            $table->index('service_id');
        });
        Schema::table('problem_service', function (Blueprint $table) {
            $table->index('service_id');
            $table->index('problem_id');
        });
        Schema::table('service_brand', function (Blueprint $table) {
            $table->index('service_id');
        });
        Schema::table('purchases_details', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('purchase_id');
        });
    }

    public function down()
    {
        Schema::table('service_rmas', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment']);
            $table->dropIndex(['assignto']);
            $table->dropIndex(['customerid']);
            $table->dropIndex(['date']);
        });
        Schema::table('services_status', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
        });
        Schema::table('problem_service', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
            $table->dropIndex(['problem_id']);
        });
        Schema::table('service_brand', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
        });
        Schema::table('purchases_details', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['purchase_id']);
        });
    }
}
