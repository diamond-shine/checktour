<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('tour_id'); //productId on booking service
            $table->string('booking_number');
            $table->string('tour_bookeo_id'); //productId on booking service
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email');
            $table->string('country_code')->nullable();
            $table->unsignedInteger('guid_id')->nullable();
            // $table->string('promotion_code');
            // $table->string('promotion_name');
            // $table->string('coupon_code');
            // $table->string('gift_voucher_code');
            // $table->string('specific_voucher_code');
            $table->string('arrived_waiting_room')->default(false);
            $table->string('creation_agent'); //creationAgent on booking service
            $table->boolean('is_private_event')->default(false);
            $table->text('source_data');
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
        Schema::dropIfExists('bookings');
    }
}
