<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prlpromocionesliquidadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prlpromocionesliquidadas', function (Blueprint $table) {
            $table->increments('prlid');
            $table->unsignedInteger('fecid');
            $table->unsignedInteger('sucid');
            $table->unsignedInteger('proid');
            $table->unsignedInteger('proidbonificado');
            $table->string('prlconcepto');
            $table->string('prlejecutivo');
            $table->string('prlgrupo');
            $table->string('prlcompra');
            $table->string('prlbonificacion');
            $table->string('prlmecanica');
            $table->string('prlcategoria');
            $table->string('prlplancha');
            $table->string('prlcombo');
            $table->string('prlreconocerxcombo');
            $table->string('prlreconocerxplancha');
            $table->string('prltotal');
            $table->string('prlliquidacionso');
            $table->string('prlliquidacioncombo');
            $table->string('prlliquidacionvalorizado');
            $table->string('prlliquidaciontotalpagar');
            
            $table->timestamps();

            $table->foreign('fecid')->references('fecid')->on('fecfechas');
            $table->foreign('sucid')->references('sucid')->on('sucsucursales');
            $table->foreign('proid')->references('proid')->on('proproductos');
            $table->foreign('proidbonificado')->references('proid')->on('proproductos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prlpromocionesliquidadas');
    }
}
