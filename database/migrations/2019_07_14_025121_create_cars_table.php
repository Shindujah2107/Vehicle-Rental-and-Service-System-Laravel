<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('car_number', 5)->unique();
            $table->text('description');
            $table->boolean('available')->default(true);
            $table->boolean('status')->default(true);
            $table->integer('car_type_id')->unsigned()->index();
            $table->timestamps();

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
        Schema::dropIfExists('cars');
    }
}
