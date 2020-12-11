<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ttrtritre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ttrtritre', function (Blueprint $table) {
            $table->increments('ttrid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('triid');
            $table->unsignedInteger('treid');
            $table->unsignedInteger('catid');
            $table->unsignedInteger('tprid');
            $table->string('ttrporcentajedesde');
            $table->string('ttrporcentajehasta');
            $table->string('ttrporcentajerebate');
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('triid')->references('triid')->on('tritrimestres');
            $table->foreign('treid')->references('treid')->on('tretiposrebates');
            $table->foreign('catid')->references('catid')->on('catcategorias');
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
        Schema::dropIfExists('ttrtritre');
    }
}
