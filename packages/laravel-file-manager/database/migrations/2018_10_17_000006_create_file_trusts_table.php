<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTrustsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_trusts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('file_id');

            $table->string('file_trastuble_type');

            $table->string('file_trastuble_key');

            $table->index([
                'file_id',
                'file_trastuble_type',
                'file_trastuble_key',
            ], 'file_trastuble_index');

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
        Schema::drop('file_trusts');
    }

}
