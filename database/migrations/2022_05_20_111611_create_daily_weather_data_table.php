<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_weather_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('address_id')->index()->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->date('date');
            $table->string('current_temperature');
            $table->string('current_rain_level');
            $table->string('current_chance_rain');
            $table->string('next_temperature');
            $table->string('next_rain_level');
            $table->string('next_chance_rain');
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_weather_data');
    }
};
