<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Pivot table used by the "Aging Report" (ServiceController::index, raw
 * leftjoin on groupproblem_id/problem_id — no Eloquent relation, no model).
 * No migration ever created this table at all — confirmed missing entirely
 * via a live "relation does not exist" error on the Postgres side, and now
 * confirmed byte-for-byte against a real production schema dump: just the
 * two bare int columns, no id, no timestamps, no FK constraints.
 */
class CreateGroupproblemProblemTable extends Migration
{
    public function up()
    {
        Schema::create('groupproblem_problem', function (Blueprint $table) {
            $table->integer('groupproblem_id');
            $table->integer('problem_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('groupproblem_problem');
    }
}
