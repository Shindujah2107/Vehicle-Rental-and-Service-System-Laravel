<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityCarTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_car_type', function (Blueprint $table) {

            $table->integer('facility_id')->unsigned()->index();
            $table->integer('car_type_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('facility_id')
                ->references('id')->on('facilities')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('car_type_id')
                ->references('id')->on('car_types')
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
        Schema::dropIfExists('facility_car_type');
    }
}
