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
            $table->string('prmcombos');
            $table->timestamps();

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
        Schema::dropIfExists('prmpromociones');
    }
}
