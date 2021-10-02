<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_orders', function (Blueprint $table) {
            $table->integer('part_id')->unsigned()->index();
            $table->integer('car_booking_id')->unsigned()->index();
            $table->integer('quantity')->default(1);
            $table->integer('cost');

            $table->timestamps();

            $table->foreign('part_id')
                ->references('id')->on('parts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('car_booking_id')
                ->references('id')->on('car_bookings')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_orders');
    }
}
