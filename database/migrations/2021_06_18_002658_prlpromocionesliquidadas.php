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
            // $table->unsignedInteger('proid')->nullable();
            // $table->unsignedInteger('proidbonificado')->nullable();
            $table->string('prlsku');
            $table->string('prlproducto');
            $table->string('prlskubonificado');
            $table->string('prlproductobonificado');

            $table->string('prlconcepto');
            $table->string('prlejecutivo')->nullable();
            $table->string('prlgrupo')->nullable();
            $table->string('prlcompra')->nullable();
            $table->string('prlbonificacion')->nullable();
            $table->string('prlmecanica')->nullable();
            $table->string('prlcategoria');
            $table->string('prlplancha');
            $table->string('prlcombo')->nullable();
            $table->string('prlreconocerxcombo')->nullable();
            $table->string('prlreconocerxplancha')->nullable();
            $table->string('prltotal');
            $table->string('prlliquidacionso')->nullable();
            $table->string('prlliquidacioncombo')->nullable();
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
