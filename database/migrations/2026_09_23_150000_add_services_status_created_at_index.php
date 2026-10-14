<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * report_agin() filters services_status.created_at with a range comparison
 * (X days ago) across all 205,689 real status rows — the earlier index pass
 * covered the FK columns (service_id) but missed this one, found live while
 * sweeping the Service module's reports for the same real-data performance
 * class of bug.
 */
class AddServicesStatusCreatedAtIndex extends Migration
{
    public function up()
    {
        Schema::table('services_status', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::table('services_status', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
