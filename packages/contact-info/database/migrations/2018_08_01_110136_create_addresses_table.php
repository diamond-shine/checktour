<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('patronymic')->nullable();

            $table->string('last_name')->nullable();

            $table->string('first_name')->nullable();

            $table->string('country')->nullable();

            $table->string('region')->nullable();

            $table->string('city')->nullable();

            $table->string('street')->nullable();

            $table->string('additional')->nullable();

            $table->string('house_number')->nullable();

            $table->string('apartment_number')->nullable();

            $table->string('postcode')->nullable();

            $table->json('payload')->nullable();

            $table->typedMorphs('addressable', 'uuid');

            $table->boolean('is_default')->default(0);

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
        Schema::drop('addresses');
    }

}
