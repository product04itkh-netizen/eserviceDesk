<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * spatie/laravel-medialibrary's own migration — this package is a direct
 * dependency (composer.json, ^7.14) and Service implements HasMedia /
 * appends 'logo' (used for ticket photo attachments), but this table was
 * never published/created at all. Invisible in empty-DB testing since the
 * 'logo' append only triggers a media query when an actual row gets
 * serialized; surfaced the moment real Service rows existed. Copied
 * verbatim from vendor/spatie/laravel-medialibrary/database/migrations/
 * create_media_table.php.stub — the package's own canonical schema.
 */
class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->nullableTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
}
