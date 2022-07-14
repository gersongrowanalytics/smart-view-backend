<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Badbasedatos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badbasedatos', function (Blueprint $table) {
            $table->increments('badid');
            $table->unsignedInteger('areid');
            $table->string('badnombre');
            $table->foreign('areid')->references('areid')->on('areareas');
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
        Schema::dropIfExists('badbasedatos');
    }
}
