<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('email')->index();

            $table->string('password', 60);

            $table->string('first_name')->nullable();

            $table->string('last_name')->nullable();

            $table->string('login')->unique()->nullable();

            $table->boolean('is_admin')->default(0);

            $table->boolean('is_banned')->default(0);

            $table->boolean('is_active')->default(0);

            $table->rememberToken();

            $table->timestamp('last_logged_at')->nullable();

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
        Schema::drop('users');
    }

}
