<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rtprebatetipospromociones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rtprebatetipospromociones', function (Blueprint $table) {
            $table->increments('rtpid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('tprid');
            $table->string('rtpporcentajedesde');
            $table->string('rtpporcentajehasta');
            $table->string('rtpporcentajerebate');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('tprid')->references('tprid')->on('tprtipospromociones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsutipospromocionessucursales');
    }
}
