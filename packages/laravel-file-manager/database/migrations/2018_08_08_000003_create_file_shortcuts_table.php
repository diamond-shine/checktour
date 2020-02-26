<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileShortcutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_shortcuts', function (Blueprint $table) {
            $table->string('file_id');

            $table->string('entity_type');

            $table->uuid('entity_id');

            $table->string('label')->nullable();

            $table->json('conversion')->nullable();

            $table->json('payload')->nullable();

            $table->unsignedInteger('weight')->default(0)->index();

            $table->index(['entity_type', 'entity_id', 'label']);

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
        Schema::drop('file_shortcuts');
    }

}
