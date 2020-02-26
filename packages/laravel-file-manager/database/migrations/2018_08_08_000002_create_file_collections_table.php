<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_collections', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title')->nullable();

            $table->string('label')->nullable();

            $table->string('entity_type')->nullable();

            $table->uuid('entity_id')->nullable();

            $table->index(['entity_type', 'entity_id', 'label']);

            $table->softDeletes();

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
        Schema::drop('file_collections');
    }

}
