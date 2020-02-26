<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('booking_id')->index();
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('quantity');
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->decimal('total_price_modified', 12, 2)->default(0.00);
            $table->boolean('is_modified')->default(false);
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
        Schema::dropIfExists('booking_tickets');
    }
}
