<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prmpromociones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prmpromociones', function (Blueprint $table) {
            $table->increments('prmid');
            $table->unsignedInteger('tprid');
            $table->unsignedInteger('fecid');
            $table->string('prmcodigoprincipal');
            $table->string('prmcodigo');
            $table->text('prmmecanica');
            // $table->string('prmcantidadcombo');
            // $table->string('prmcantidadplancha');
            // $table->string('prmtotalcombo');
            // $table->string('prmtotalplancha');
            // $table->string('prmtotal');
            $table->string('prmaccion');
            $table->timestamps();

            $table->foreign('tprid')->references('tprid')->on('tprtipospromociones');
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
        Schema::dropIfExists('prmpromociones');
    }
}
