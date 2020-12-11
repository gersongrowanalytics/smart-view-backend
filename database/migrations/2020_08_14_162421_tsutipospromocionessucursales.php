<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tsutipospromocionessucursales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsutipospromocionessucursales', function (Blueprint $table) {
            $table->increments('tsuid');
            $table->unsignedInteger('tprid');
            $table->unsignedInteger('sucid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('treid')->nullable();
            $table->string('tsuvalorizadoobjetivo');
            $table->string('tsuvalorizadoreal');
            $table->string('tsuvalorizadotogo');
            $table->string('tsuporcentajecumplimiento');
            $table->string('tsuvalorizadorebate');
            $table->string('tsuobjetivotrimestral')->nullable();
            $table->string('tsurealtrimestral')->nullable();
            $table->string('tsufacturartrimestral')->nullable();
            $table->string('tsucumplimientotrimestral')->nullable();
            $table->string('tsurebatetrimestral')->nullable();
            $table->timestamps();

            $table->foreign('tprid')->references('tprid')->on('tprtipospromociones');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('treid')->references('treid')->on('tretiposrebates');
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
