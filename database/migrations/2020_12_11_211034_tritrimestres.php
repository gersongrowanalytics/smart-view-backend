<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tritrimestres extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tritrimestres', function (Blueprint $table) {
            $table->increments('triid');
            $table->unsignedInteger('fecid');
            $table->string('trinombre');
            $table->boolean('triestado');
            $table->string('triano');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tritrimestres');
    }
}
