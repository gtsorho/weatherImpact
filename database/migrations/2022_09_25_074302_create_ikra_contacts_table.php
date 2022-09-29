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
        Schema::create('ikra_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ikra_id')->index()->nullable();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone');
            $table->string('location');
            $table->timestamps();

            $table->foreign('ikra_id')->references('id')->on('ikra_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ikra_contacts');
    }
};
